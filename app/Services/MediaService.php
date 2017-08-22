<?php

namespace App\Services;

use Log;
use File;
use AWS;
use Aws\CommandPool;
use Aws\CommandInterface;
use Aws\ResultInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GuzzleClient;

/**
 * A service used to upload/delete files from AWS S3 bucket as well as creating record at HPI
 */
class MediaService
{

    protected $s3Instance;

    protected $s3Bucket;

    protected $ajax;

    protected $storeExtension;

    use GuzzleClient;

    public function __construct($ajax = false, $storeExtension = true)
    {
        $this->s3Instance = \AWS::createClient('s3');

        $this->s3Bucket = env('AWS_S3_BUCKET');

        $this->ajax = $ajax;

        $this->storeExtension = $storeExtension;
    }

    /**
     * Performs validation and then uploads the file to S3 and records it in API media table.
     *
     * @param File $inputFile The file input
     *
     * @param string $inputName Name of the file input element
     *
     * @param array $allowedMIME The array of allowed MIME types.
     *        If not specified, it will automatically grab default from config.
     *
     * @param int $allowedSize The maximum allowed size for the file in kilobyte (KB)
     *
     * @param string $filename The filename which the file will be saved as. (including file extension)
     *
     * @param string $targetFolder The subfolder name where the file will be uploaded to in S3 bucket.
     *
     * @param string $deleteLocal Whether to delete the local file or not
     *
     * @return Object containing the uploaded file information or errors if an error has occured.
     */
    public function uploadFile($inputFile, $inputName, $allowedMIME = array(), $allowedSize = 10000, $filename = null, $targetFolder = 'uploads', $deleteLocal = true)
    {
        if ($inputFile->isValid()) {
            if (!count($allowedMIME) > 0) {
                $allowedMIME = config('file.allowedMIMEs');
            }
            
            $rules = array(
                $inputName    =>    'required|max:' . $allowedSize,
                'extension' =>  'required|in:' . implode(',', $allowedMIME),
            );

            $messages = [
                $inputName . 'required'    => 'File type not allowed.',
            ];

            $validator = Validator::make(array(
                $inputName => $inputFile,
                'extension' =>  strtolower($inputFile->getClientOriginalExtension())
            ), $rules, $messages);

            if ($validator->passes()) {
                if (is_null($filename)) {
                    $filename = $inputFile->getClientOriginalExtension() . '-' . time();
                }

                $mediaKey = $filename;
                $filename = $filename . '.' . $inputFile->getClientOriginalExtension();

                if($this->storeExtension){
                    $mediaKey = $filename;
                }

                $inputFile->move(storage_path('temp'), $filename);
                $result = $this->uploadFileToS3(storage_path('temp/' . $filename), $targetFolder . '/' . $mediaKey, $deleteLocal);
                if (!isset($result->errors) && isset($result->url)) {
                    $postData = array(
                        'filename'     =>    $filename,
                        'ext'          =>    $inputFile->getClientOriginalExtension(),
                        'media_url'    =>    $result->url,
                        'media_key'    =>    $targetFolder . '/' . $mediaKey,
                    );
                    $response = json_decode($this->postGuzzleClient($postData, 'media')->getBody()->getContents());
                } else {
                    $response = new \stdClass();
                    $response->errors = $result->errors;
                }
                if ($this->ajax) {
                    return Response::json($response);
                } else {
                    return $response;
                }
            } else {
                $response = new \stdClass();
                if (!$this->ajax) {
                    $response->errors = $validator->errors()->first();
                    return $response;
                } else {
                    $response->errors = $validator->errors()->first();
                    return Response::json($response);
                }
            }
        }
    }

    /**
     * Performs validation and then uploads multiple files to S3 and records it in API media table.
     *
     * @param array $inputFiles The array of input files
     *
     * @param string $inputName Name of the file input element
     *
     * @param array $allowedMIME The array of allowed MIME types.
     *        If not specified, it will automatically grab default from config.
     *
     * @param int $allowedSize The maximum allowed size for the file in kilobyte (KB)
     *
     * @param string $targetFolder The subfolder name where the file will be uploaded to in S3 bucket.
     *
     * @return Object [results, errors]
     */
    public function uploadFiles($inputFiles, $inputName, $allowedMIME = array(), $allowedSize = 10000, $targetFolder = 'uploads')
    {
        if (!count($allowedMIME) > 0) {
            $allowedMIME = config('file.allowedMIMEs');
        }
        $errors = array();
        $mediaArray = array();
        $client = $this->s3Instance;
        $commandGenerator = function ($inputFiles, $bucket, $targetFolder) use ($client, &$errors, &$mediaArray, &$allowedMIME, &$allowedSize) {
            $x = 0;
            foreach ($inputFiles as $inputFile) {
                if ($inputFile->isValid()) {
                    $rules = array(
                        $inputFile->getClientOriginalName()    =>    'required|max:' . $allowedSize,
                        $inputFile->getClientOriginalName() . '\'s_extension' =>  'required|in:' . implode(',', $allowedMIME),
                    );
                    $messages = [
                        $inputFile->getClientOriginalName() . 'required'    => 'File type not allowed.',
                    ];
                    
                    $validator = Validator::make(array(
                        $inputFile->getClientOriginalName() => $inputFile,
                        $inputFile->getClientOriginalName() . '\'s_extension' =>  strtolower($inputFile->getClientOriginalExtension())
                    ), $rules, $messages);

                    if ($validator->passes()) {
                        $x++;
                        $mediaKey = $inputFile->getClientOriginalExtension() . '-' . time() . '-' . $x;
                        $filename = $mediaKey . '.' . $inputFile->getClientOriginalExtension();
                        if($this->storeExtension){
                            $mediaKey = $filename;
                        }
                        $inputFile->move(storage_path('temp'), $filename);
                        // Yield a command that will be executed by the pool.
                        yield $client->getCommand('PutObject', [
                            'Bucket'       => $bucket,
                            'Key'          => $targetFolder . '/' . $mediaKey,
                            'SourceFile'   => storage_path('temp/' . $filename),
                            'StorageClass' => 'REDUCED_REDUNDANCY',
                        ]);
                        $mediaArray[] = array(
                            'filename'        =>    $filename,
                            'ext'            =>    $inputFile->getClientOriginalExtension(),
                            'original_file'    =>    storage_path('temp/' . $filename),
                            'media_key'        =>    $targetFolder . '/' . $mediaKey,
                        );
                    } else {
                        $errors[] = $validator->errors()->first();
                    }
                }
            }
        };

        $commands = $commandGenerator($inputFiles, $this->s3Bucket, $targetFolder);
        $pool = new CommandPool($client, $commands, [
            // Invoke this function for each successful transfer.
            'fulfilled' => function (
                ResultInterface $result,
                $iterKey,
                PromiseInterface $aggregatePromise
            ) use (&$mediaArray) {
                $mediaArray[$iterKey]['media_url'] = $result['ObjectURL'];
            },
            // Invoke this function for each failed transfer.
            'rejected' => function (
                \Aws\S3\Exception\S3Exception $reason,
                $iterKey,
                PromiseInterface $aggregatePromise
            ) {
                Log::info("Image upload to S3 failed at iteration {$iterKey}: {$reason}");
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
        $promise->then(function () use (&$mediaArray) {
            foreach ($mediaArray as $media) {
                // Delete local storage file
                File::delete($media['original_file']);
            }
        });

        foreach ($mediaArray as $key => $media) {
            // Unset failed uploads from array
            if (!isset($media['media_url'])) {
                unset($mediaArray[$key]);
                $errors[] = 'Unable to upload file ' . $media['filename'] . '. ';
            }
        }
        $response = new \stdClass();
        if (!empty($mediaArray)) {
            $postData = array('medias' => json_encode($mediaArray));
            $response->results = json_decode($this->postGuzzleClient($postData, 'media/bulkUpload')->getBody()->getContents());
        }
        if (count($errors) > 0) {
            $response->errors = $errors;
        }
        if ($this->ajax) {
            return Response::json($response);
        } else {
            return $response;
        }
    }

    /**
     * Uploads the file to S3 and delete from storage.
     *
     * @param string $sourceFile The directory to the file
     *
     * @param string $targetKey The key name for the file to be uploaded to the S3 bucket.
     *
     * @param bool $deleteLocal Delete local file if set to true.
     *
     * @return Object [url or errors]
     */
    public function uploadFileToS3($sourceFile, $targetKey, $deleteLocal = true, $contentType = '')
    {
        try {
            $postS3Array = array(
                'Bucket'                => $this->s3Bucket,
                'Key'                   => $targetKey,
                'SourceFile'            => $sourceFile,
                'StorageClass'          => 'REDUCED_REDUNDANCY',
            );
            if($contentType != ''){
                $postS3Array['ContentType'] = $contentType;
            }
            $response = new \stdClass();
            $result = $this->s3Instance->putObject($postS3Array);
            $response->url = $result["ObjectURL"];
        } catch (\Aws\S3\Exception\S3Exception $e) {
            $error = $e->getMessage();
            $xml = simplexml_load_string(substr($error, strrpos($error, '<?xml')));
            $response->errors = $xml->Code . ': ' . $xml->Message;
        }
        if ($deleteLocal) {
            File::delete($sourceFile);
        }

        return $response;
    }

    /**
     * Deletes the file DB record through API and then deletes the file at the S3 bucket.
     *
     * @param int $mediaID The media record's ID
     *
     * @param bool $removeFromS3 Deletes the file from the S3 bucket if set to true
     *
     * @return Object [media_id or errors]
     */
    public function deleteFile($mediaID, $removeFromS3 = true)
    {
        $errors = array();
        $response = new \stdClass();
        try {
            if ($removeFromS3) {
                // Get media key from DB before deleting DB record
                $mediaInfo = $this->getMediaInfo($mediaID);
            }
            $deleteResponse = json_decode($this->deleteGuzzleClient(array(), 'media/' . $mediaID)->getBody()->getContents());
            if ($removeFromS3 && $deleteResponse->success) {
                $deleteS3 = $this->removeFileFromS3($mediaInfo->media_key);
            }
            $response = $deleteResponse;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() == '404') {
                $errors[] = 'Unable to delete media ID: ' . $mediaID;
            } else {
                $errors[] = $e->getMessage();
            }
        }
        if (count($errors) > 0) {
            $response->errors = $errors;
        }
        if (!$this->ajax) {
            return $response;
        } else {
            return Response::json($response);
        }
    }

    /**
     * Deletes the file in the S3 bucket
     *
     * @param string $targetKey The key name for the file to be deleted at the S3 bucket.
     *
     * @return bool
     */
    public function removeFileFromS3($targetKey)
    {
        $fileURL = $this->checkFileInS3($targetKey);
        if ($fileURL) {
            $this->s3Instance->deleteObject(array(
                'Bucket'     => $this->s3Bucket,
                'Key'        => $targetKey,
            ));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the media's information thru API based on the media ID.
     *
     * @param int $mediaID
     *
     * @return Object containing the media's information
     */
    public function getMediaInfo($mediaID)
    {
        $response = json_decode($this->getGuzzleClient(array(), 'media/' . $mediaID)->getBody()->getContents());
        return $response;
    }

    /**
     * Checks whether if the file exists in the S3 bucket based on key.
     *
     * @param string $targetKey The file's key.
     *
     * @return mixed|string:the file's URL or bool:false if the file does not exist.
     */
    public function checkFileInS3($targetKey)
    {
        $exist = false;
        try {
            $result = $this->s3Instance->getObject(array(
                'Bucket'                => $this->s3Bucket,
                'Key'                   => $targetKey,
            ));
            $url = $this->s3Instance->getObjectUrl($this->s3Bucket, $targetKey);
            $exist = true;
        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getCode() == 'NoSuchKey') {
                $exist = false;
            } else {
                throw new \Aws\S3\Exception\S3Exception($e->getMessage(), $e->getCode());
            }
        }

        if ($exist) {
            return $url;
        } else {
            return $exist;
        }
    }
}
