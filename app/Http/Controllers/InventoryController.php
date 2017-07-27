<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use App\Http\Controllers\Controller;
use App\Services\MediaService as MediaService;

use stdClass;
use Response;
use DB;
use Image;
use File;

class InventoryController extends Controller
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:view.product', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.product', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.product', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.product', ['only' => ['destroy']]);

        $this->admin = \Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_id=null)
    {
        $data = $this->getFiltersData($channel_id);
        if (empty($data['channels']) || count($data['channels']) == 0) {
            $data['channels'] = [];
            $data['channel_types'] = [];
        }

        $channels = array();
        $channel_by_types = array();
        $non_active = array();
        foreach ($data['channels'] as $channel) {
            if(strcmp($channel['status'],"Active")!=0){
                $non_active[$channel['status']][$channel['id']] = $channel['name'];
            }else{
                $channels[$channel['id']] = $channel['name'];
                $channel_by_types[$channel['type']][$channel['id']] = $channel['name'];
            }
        }

        if(\Input::get('pid')){
            $data['selectedProds'] = $this->getProductsView(\Input::get('pid'), false, 'product-management.inventory.product');
        }else{
            $data['selectedProds'] = '';
        }

        $channel_by_types = array_merge($channel_by_types,$non_active);
        $data['channel_by_types'] =  $channel_by_types;
        $data['channels'] = $channels;
        $data['channel_id'] = $channel_id;
        $data['admin'] = $this->admin;
        $response = json_decode($this->getGuzzleClient(array(), 'admin/categories')->getBody()->getContents());
        $data['categories'] = array();
        foreach ($response->categories as $cat) {
            $data['categories'][$cat->id] = $cat->full_name;
        }
        return view('product-management.inventory.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->route('product_id');

        $merchant_id = ($this->admin->is('clientadmin|clientuser')) ? $this->admin->merchant_id : null;
        $filterData = json_decode($this->getGuzzleClient(array('merchant_id' => $merchant_id), 'admin/inventories/getFiltersData')->getBody()->getContents(), true);
        $product = json_decode($this->getGuzzleClient(array(), 'admin/inventories/'.$id)->getBody()->getContents());
        $response = json_decode($this->getGuzzleClient(array(), 'admin/categories')->getBody()->getContents());
        //dd($product);
        $colors             =   array();
        $sizes              =   array();
        $tags               =   array();
        $medias             =   array();
        $defaultMedia       =   array();
        $skuList            =   array();
        $channelList        =   array();
        $mediaSortOrder     =   array();
        $categories         =   array();
        foreach ($response->categories as $cat) {
            $categories[$cat->id] = $cat->full_name;
        }
        //dd($product->sku_in_channel);
        foreach($product->sku_in_channel as $channelSku){
            // get colors from all sku
            foreach($channelSku->sku_options as $option){
                if($option->option_name == 'Size'){
                    $sizes[] = $option->option_value;
                }elseif($option->option_name == 'Colour'){
                    $colors[] = $option->option_value;
                }
            }
            // get sku list
            $skuList[$channelSku->sku->sku_id]['skuId']         = $channelSku->sku->sku_id;
            $skuList[$channelSku->sku->sku_id]['hwSku']         = $channelSku->sku->hubwire_sku;
            $skuList[$channelSku->sku->sku_id]['clientSku']     = $channelSku->sku->client_sku;
            $skuList[$channelSku->sku->sku_id]['supplierSku']   = $channelSku->sku->sku_supplier_code;
            $skuList[$channelSku->sku->sku_id]['skuWeight']     = $channelSku->sku->sku_weight;
            // get channel list
            $chnl = $channelSku->channel;
            if(!isset($channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['qty'])){
                $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['qty'] = 0;
            }
            if(!isset($channelList[$chnl->id]['qty'])){
                $channelList[$chnl->id]['qty'] = 0;
            }

            $channelList[$chnl->id]['name'] = $chnl->name;
            $channelList[$chnl->id]['qty'] += $channelSku->channel_sku_quantity;

            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['hwSku'] = $channelSku->sku->hubwire_sku;
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['live'] = number_format((float)$channelSku->channel_sku_live_price, 2, '.', '');
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['price'] = number_format((float)$channelSku->channel_sku_price, 2, '.', '');
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['qty'] += $channelSku->channel_sku_quantity;
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['sale'] = number_format((float)$channelSku->channel_sku_promo_price, 2, '.', '');
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['sale_start'] = $channelSku->promo_start_date;
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['sale_end'] = $channelSku->promo_end_date;
            $channelList[$chnl->id]['chnlSkuList'][$channelSku->channel_sku_id]['coordinates'] = $channelSku->channel_sku_coordinates;

            if ($channelSku->channel->channel_type->name == "Shopify POS") {
                $getType = 'POS';
            }elseif ($channelSku->channel->channel_type->name != "Shopify POS") {
                $getType = NULL;
            }
            if(!isset($skuQuantityList[$channelSku->sku->hubwire_sku]['whqty'])){
                $skuQuantityList[$channelSku->sku->hubwire_sku]['whqty'] = 0;
            }
            if(!isset($skuQuantityList[$channelSku->sku->hubwire_sku]['psqty'])){
                $skuQuantityList[$channelSku->sku->hubwire_sku]['psqty'] = 0;
            }
            if(!isset($skuTotalList['psTotal'])){
                $skuTotalList['psTotal'] = 0;
            }
            if(!isset($skuTotalList['whTotal'])){
                $skuTotalList['whTotal'] = 0;
            }
            if ($getType=='POS') {
                $skuQuantityList[$channelSku->sku->hubwire_sku]['psqty'] += $channelSku->channel_sku_quantity;
                $skuTotalList['psTotal'] += $channelSku->channel_sku_quantity;
            }elseif ($getType!='POS') {
                $skuQuantityList[$channelSku->sku->hubwire_sku]['whqty'] += $channelSku->channel_sku_quantity;
                $skuTotalList['whTotal'] += $channelSku->channel_sku_quantity;
            }
            $skuQuantityList[$channelSku->sku->hubwire_sku]['warehouseCoordinate'] = empty($channelSku->channel_sku_coordinates)?"N/A": $channelSku->channel_sku_coordinates;

        }
        // get product tags
        foreach($product->tags as $tag){
            $tags[] = $tag->value;
        }
        // get media (images)
        if(!is_null($product->media)){
            foreach($product->media as $media){
                $medias[$media->sort_order]['id']        =   $media->id;
                $medias[$media->sort_order]['media_id']  =   $media->media_id;
                $medias[$media->sort_order]['path']      =   $this->removeExtensionFromMediaURL($media->media->media_url, $media->media->media_key);
                $mediaSortOrder[]                        =   $media->id;
            }
        }else{
            $medias = NULL;
        }

        if(!is_null($product->default_media)){
            $defaultMedia['id']             =   $product->default_media->id;
            $defaultMedia['media_id']       =   $product->default_media->media_id;
            $defaultMedia['path']           =   $this->removeExtensionFromMediaURL($media->media->media_url, $media->media->media_key);
        }else{
            $defaultMedia = NULL;
        }
        $data = array();
        $data['id']                 =   $product->id;
        $data['active']             =   $product->active;
        $data['name']               =   $product->name;
        $data['description']        =   $product->description;
        $data['brand_name']         =   !empty($product->brand)?$product->brand->name:'';
        $data['merchant_name']      =   !empty($product->merchant)?$product->merchant->name:'';
        $data['merchant_id']        =   $product->merchant_id;
        $data['colors']             =   array_unique($colors);
        $data['sizes']              =   array_unique($sizes);
        $data['tags']               =   $tags;
        $data['default_media']      =   $defaultMedia;
        $data['media']              =   $medias;
        $data['skuList']            =   $skuList;
        $data['channelList']        =   $channelList;
        $data['tagsList']           =   $filterData['tags'];
        $data['mediaSortOrder']     =   implode(',', $mediaSortOrder);
        $data['categories']         =   $categories;
        $data['category_id']        =   $product->category_id;
        $data['skuQuantityList']    =   $skuQuantityList;
        $data['skuTotalList']       =   $skuTotalList;

        // get 't' query string to set active tab
        if($request->get('t')){
            $data['activeTab'] = $request->get('t');
        }else{
            $data['activeTab'] = 'product';
        }

        return view('product-management.inventory.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $type = $request->get('type');
        $postData = array();
        if($type == 'product'){
            // validation rules
            $rules = [
                'name'                  => 'required',
                'description'           => 'sometimes',
                'active'                => 'sometimes|boolean',
                //'category_id'           => 'required',
            ];

            $messages = [
                'name.required'         => 'Product title cannot be blank.',
                //'category_id.required'  => 'Category cannot be blank.'
            ];
            $v = \Validator::make($request->all(), $rules, $messages);

            if ($v->fails()) {
                $errors = $v->errors()->all();
                $response = array(
                    'error'      => $errors,
                    'success'    => false,
                    );
                return response()->json($response);
            }else{
                // prepare post data if validation passed
                $postData['product']['name']            =   $request->get('name');
                $postData['product']['description']     =   $request->get('description');
                $postData['product']['active']          =   $request->get('active');
                !empty($request->get('category_id') ? $postData['product']['category_id'] = $request->get('category_id') : null);
            }
        }elseif($type == 'tags'){
            // prepare post data
            $postData['tags']  =   $request->get('product_tags');
        }elseif($type == 'sku'){
            // validation rules
            $rules = [
                'sku_weight'    => 'required|numeric',
            ];

            $messages = [
                'sku_weight.required'     => 'Please enter the SKU weight.',
                'sku_weight.numeric'      => 'Please enter a valid SKU weight.',
            ];

            $v = \Validator::make($request->all(), $rules, $messages);

            if ($v->fails()) {
                $errors = $v->errors()->all();
                $response = array(
                    'error'      => $errors,
                    'success'    => false,
                    );
                return response()->json($response);
            }else{
                // prepare post data if validation passed
                $data = $request->all();
                unset($data['type']);
                $postData['sku'][] = $data;
            }
        }elseif($type == 'channel_sku'){
            $data = $request->all();
            unset($data['type']);
            $rules = [
                'channel_sku' => 'required|array'
               ];
            // validation
            foreach($data['channel_sku'] as $index => $chnlSku){
                // validation rules
                $rules["channel_sku.".$index.".channel_sku_price"] = 'required|numeric|min:0.01';
                $rules["channel_sku.".$index.".channel_sku_promo_price"] = 'required|numeric|min:0';
                // $rules["channel_sku.".$index.".promo_start_date"] = "before:promo_end_date|date";
                // $rules["channel_sku.".$index.".promo_end_date"] = "after:promo_start_date|date";

                // Channel SKU Price
                $messages["channel_sku.".$index.".channel_sku_price.required"] = 'Please enter a valid retail price for Channel SKU ID '.$index;
                $messages["channel_sku.".$index.".channel_sku_price.numeric"] = 'Please enter a valid retail price for Channel SKU ID '.$index;
                $messages["channel_sku.".$index.".channel_sku_price.min"] = 'Retail price cannot be lower than 0.01 for Channel SKU ID '.$index;

                // Channel Promo Price
                $messages["channel_sku.".$index.".channel_sku_promo_price.required"] = 'Please enter a listing price for Channel SKU ID '.$index;
                $messages["channel_sku.".$index.".channel_sku_promo_price.numeric"] = 'Please enter a listing price for Channel SKU ID '.$index;
                $messages["channel_sku.".$index.".channel_sku_promo_price.min"] = 'Listing price cannot be lower than 0.01 for Channel SKU ID '.$index;

                // preset post data
                $postData['sku_in_channel'][] = array(
                    'channel_sku_id'            =>  $index,
                    'channel_sku_price'         =>  $chnlSku["channel_sku_price"],
                    'channel_sku_promo_price'   =>  $chnlSku["channel_sku_promo_price"],
                    'promo_start_date'          =>  $chnlSku["promo_start_date"],
                    'promo_end_date'            =>  $chnlSku["promo_end_date"],
                    'channel_sku_coordinates'   =>  $chnlSku["channel_sku_coordinates"],
                );
            }

            $v = \Validator::make($data, $rules, $messages);

            if ($v->fails()) {
                $errors = $v->errors()->all();
                $response = array(
                    'error'      => $errors,
                    'success'    => false,
                );
                return response()->json($response);
            }
        }
        // post data to hapi
        $updateResponse = json_decode($this->putGuzzleClient($postData, 'admin/inventories/'.$id)->getBody()->getContents());
        //dd($response);
        if(empty($updateResponse->error)){
            $response = array(
                'success'   => true,
                'response'  => $updateResponse,
                );
        }else{
            $errorMsg = new MessageBag((array)$updateResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function uploadProductMedia(Request $request, $id){
        $rules = [
            'upload_file'   => 'required|image',
        ];

        $messages = [
            'upload_file.required'     => 'Please select an image.',
            'upload_file.image'        => 'File type not supported.',
        ];
        $v = \Validator::make($request->all(), $rules, $messages);

        if ($v->fails()) {
            $errors = $v->errors()->all();
            $response = array(
                'error'      => $errors,
                'success'    => false,
                );
            return response()->json($response);
        }else{
            // Passed validation
            if ($request->hasFile('upload_file')) {
                $file = $request->file('upload_file');
                $allowed = array('jpg','jpeg','png');
                $extension = strtolower($file->getClientOriginalExtension());
                $tfile = $file->getRealPath();
                $fileName = uniqid();
                $contentType = $file->getMimeType();
                $mediaService = new MediaService(false, true);
                // This media service will move the file to elsewhere.
                $response = $mediaService->uploadFile($file, 'upload_file', $allowed, 50000, $fileName, 'products/'.$id, false);
                if (!isset($response->errors)) {
                    // Passed uploading and creating media record
                    $postData = array();
                    $postData['media_id'] = $response->media_id;
                    // Post media to HAPI
                    $createResponse = json_decode($this->postGuzzleClient($postData, 'admin/products/'.$id.'/medias')->getBody()->getContents());
                    if(empty($createResponse->error)){
                        // Get Image dimensions list from HAPI
                        $imgDimensions = parent::getImageWHHapi();
                        foreach($imgDimensions as $imgDimension){
                            $path = "/tmp/".uniqid();
                            // Get the image file from the path where the media service have moved it to
                            if($imgDimension['width'] == $imgDimension['height']){
                                //Generate square image
                                $background = Image::canvas($imgDimension['width'], $imgDimension['height']);
                                $squaredImage = Image::make(storage_path('temp/' . $fileName . '.' . $extension))->resize($imgDimension['width'], $imgDimension['height'], function ($c) {
                                    $c->aspectRatio();
                                    $c->upsize();
                                });
                                $background->insert($squaredImage, 'center');
                                $background->encode('png')->save($path, 100);
                            }else{
                                $img = Image::make(storage_path('temp/' . $fileName . '.' . $extension));
                                $img->resize($imgDimension['width'], $imgDimension['height']);
                                $img->save($path, 100);
                            }
                            $mediaKey = 'products/'.$id . '/' . $fileName.'_'.$imgDimension['width'].'x'.$imgDimension['height'];
                            $s3Upload = $mediaService->uploadFileToS3($path, $mediaKey, true, $contentType);
                            \Log::info(print_r($s3Upload, true));
                        }

                        // Delete local file
                        File::delete(storage_path('temp/' . $fileName . '.' . $extension));
                        // Generate response
                        $result = array(
                            'media_id'      =>  $response->media_id,
                            'media_path'    =>  $this->removeExtensionFromMediaURL($response->media_url, $response->media_key),
                            'id'            =>  $createResponse->id,
                        );
                        // Return success
                        $returnResponse = array(
                            'response'      => $result,
                            'success'       => true,
                        );
                    }else{
                        $errorMsg = new MessageBag((array)$createResponse->error);
                        $returnResponse = array(
                            'error'     => $errorMsg->all(),
                            'success'   => false,
                            );
                    }
                }
                else{
                    $errorMsg = new MessageBag((array)$response->errors);
                    $returnResponse = array(
                        'error'     => $errorMsg->all(),
                        'success'   => false,
                        );
                }
                return response()->json($returnResponse);
            }
        }
    }

    public function setDefaultProductImg(Request $request, $id){
        //$postData['product']['name']  =   $request->get('name');
        // post data to hapi
        $postData['product']['default_media']  =   $request->get('product_media_id');
        $updateResponse = json_decode($this->putGuzzleClient($postData, 'admin/inventories/'.$id)->getBody()->getContents());
        //dd($response);
        if(empty($updateResponse->error)){
            $response = array(
                'success'   => true,
                'response'  => $updateResponse,
                );
        }else{
            $errorMsg = new MessageBag((array)$updateResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }

        return response()->json($response);
    }

    public function deleteProductImg(Request $request, $id){
        $mediaService = new MediaService();

        // get media info to obtain media key to delete other image dimensions
        $mediaInfo = $mediaService->getMediaInfo($request->get('media_id'));

        // delete media from S3
        $deleteResponse = $mediaService->deleteFile($request->get('media_id'));

        if(isset($deleteResponse->success) && $deleteResponse->success){
            // get all dimensions from HAPI
            $imgDimensions = parent::getImageWHHapi();
            $mediaInfo->media_key = $this->removeExtensionFromKey($mediaInfo->media_key);

            // loop thru each dimension and delete the media from S3
            foreach($imgDimensions as $imgDimension){
                // Get the image file from the path where the media service have moved it to
                $mediaKey = $mediaInfo->media_key . '_' . $imgDimension['width'].'x'.$imgDimension['height'];
                $exist = $mediaService->checkFileInS3($mediaKey);
                if($exist){
                    $s3Delete = $mediaService->removeFileFromS3($mediaKey);
                }
            }

            // after deleting media from S3, delete product_media record
            $response = json_decode($this->deleteGuzzleClient(array(), 'admin/products/'.$id.'/medias/'.$request->get('product_media_id'))->getBody()->getContents());

            if(empty($response->error)){
                $response = array(
                    'success'   => true,
                    'response'  => $response,
                    );
            }else{
                $errorMsg = new MessageBag((array)$response->error);
                $response = array(
                    'error'     => $errorMsg->all(),
                    'success'   => false,
                    );
            }

            return response()->json($response);
        }
    }

    public function updateProductImgOrder(Request $request, $id){
        $postData = array();
        $postData['img_sort_order']  =   $request->get('img_sort_order');
        $apiResponse = json_decode($this->postGuzzleClient($postData, 'admin/products/'.$id.'/medias/reorder')->getBody()->getContents());

        if(empty($apiResponse->error)){
            $response = array(
                'success'   => true,
                'response'  => $apiResponse,
                );
        }else{
            $errorMsg = new MessageBag((array)$apiResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }
        return response()->json($response);
    }

    public function searchProductInventory(Request $request) {
        $data = $this->search($request, 'product-management.inventory.product');

        return $data;
    }

    public function getFiltersData($channel_id=null) {
        $filters = array();
        $filters['merchant_id'] = ($this->admin->is('clientadmin|clientuser')) ? $this->admin->merchant_id : null;
        if(!is_null($channel_id)) $filters['channel_id'] = $channel_id;
        //$data = json_decode($this->getGuzzleClient($filters, 'admin/inventories/getFiltersData')->getBody()->getContents(), true);

        //check whether to hide merchant filter
        $data['hide_merchant'] = ($this->admin->is('clientadmin|clientuser')) ? true : false;
        $data['merchants'] = array();
        $data['suppliers'] = array();
        $data['statuses'] = array();
        $data['stock_statuses'] = array();
        $data['keywords'] = array();
        $data['tags'] = array();
        return $data;
    }

    /**
     * Search for products.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $productView) {
        $data['start'] = $request->input('start');
        $data['length'] = $request->input('length');
        $data['draw'] = $request->input('draw');

        $data['columns'] = array(
            'product'       => $request->input('keyword', null),
            'min_price'     => $request->input('min_price', null),
            'max_price'     => $request->input('max_price', null),
            'coordinate'    => $request->input('coordinate',null),
            //'gst'           => $request->input('gst', null),
            'no_image'      => $request->input('no_image', null),
            'channel_id'    => $request->input('channel', null),
            'merchant_id'   => $request->input('merchant', null),
            'supplier_id'   => $request->input('supplier', null),
            'status'        => $request->input('status', null),
            'has_stock'     => $request->input('stock_status', null),
            'tag_value'     => $request->input('tags', null),
            'purchase_id'   => $request->input('procurement_batch', null),
            'sync_status'   => $request->input('sync_status', null),
            'channel_sku_active'  => $request->input('channel_sku_active', null),
            'category_id'  => $request->input('category_id', null),
        );

        if($this->admin->is('clientadmin') || $this->admin->is('clientuser')) {
             $data['columns']['merchant_id'] = $this->admin->merchant_id;
        }
        $response = json_decode($this->getGuzzleClient($data, 'admin/inventories')->getBody()->getContents());

        $data['recordsTotal'] = $response->total;
        $data['recordsFiltered'] = $response->total;
        $data['data'] = array();

        $show_sync_indicator = $request->input('third_party', false);

        if ($data['recordsFiltered'] > 0)
        {
            foreach ($response->products as $product)
            {
                if(!empty($product->default_media->media_url))
                    $img_path = $this->removeExtensionFromMediaURL($product->default_media->media_url, $product->default_media->media_key) . "_";
                else
                    $img_path = '//placehold.it/';

                $img_path.=$this->getImageWH('md-sm');

                $productData = new stdClass();
                $productData->id = $product->id;
                $productData->name = $product->name;
                // $productData->desc = $product->description;
                // $productData->desc2 = $product->description2;
                $productData->brand = !empty($product->brand->prefix) ? $product->brand->prefix : null;
                // $productData->created_at = $product->created_at;
                // $productData->updated_at = $product->updated_at;
                // $productData->sku = !empty($product->sku_in_channel) ? $product->sku_in_channel : null;
                $productData->sync_status = !empty($product->sku_in_channel) ? $product->sku_in_channel[0]->sync_status : '';
                $productData->total_quantity = 0;

                if(!empty($product->sku_in_channel)){
                    foreach($product->sku_in_channel as $channel){
                        if(!empty($data['columns']['channel_id']) && ($channel->channel_id!=intval($data['columns']['channel_id']))) continue;
                        $productData->total_quantity += $channel->channel_sku_quantity;
                    }
                }

                $pdata['show_sync_indicator'] = $show_sync_indicator;
                $pdata['admin'] = $this->admin;
                $pdata['product'] = $productData;
                $pdata['img_path'] = $img_path;
                $pdata['channel_id'] = $data['columns']['channel_id'];
                $view = view($productView, $pdata)->render();
                $data['data'][] = array(
                    $view,
                    json_encode($product)
                );
            }
        }

        return Response::JSON($data);
    }

    public function createRejectProducts(Request $request, $errorMessage=array()){
        //dd($request);
        $products = $request->get('products');
        $reasons = config('globals.reject_sku.reasons');
        $data = array();
        $product = array();
        $channels = array();
        $postData = array();
        $postData['product_ids'] = array_keys($products);
        $data['product_ids'] = $postData['product_ids'];
        // get qty from HAPI
        $response = json_decode($this->postGuzzleClient($postData, 'admin/inventories/get_chnl_sku_qty')->getBody()->getContents());

        foreach($reasons as $reason){
            $data['reasons'][$reason] = $reason;
        }

        foreach($products as $product){
            $product = json_decode($product);
            //dd($product);
            $skus = array();
            $data['products'][$product->id]['id'] = $product->id;
            $data['products'][$product->id]['name'] = $product->name;
            $postData['product_ids'][] = $product->id;
            // replace elasticsearch data with data retrieved from HAPI
            foreach($response as $index => $newProduct){
                if($product->id == $newProduct->id){
                    $product->sku_in_channel = $newProduct->sku_in_channel;
                    unset($response[$index]);
                }
            }
            if(isset($product->sku_in_channel)){
                foreach($product->sku_in_channel as $chnlSku){
                    $sku['sku_id'] = $chnlSku->sku_id;
                    $sku['hubwire_sku'] = $chnlSku->sku->hubwire_sku;
                    $sku['qty'] = $chnlSku->channel_sku_quantity;
                    $sku['channel_id'] = $chnlSku->channel_id;
                    $sku['channel_name'] = $chnlSku->channel->name;
                    $data['products'][$product->id]['skus'][$chnlSku->sku_id][] = $sku;
                    $data['channels'][$chnlSku->channel_id] = $chnlSku->channel->name;
                }
            }
        }

        $data['original_products'] = htmlentities(json_encode($products));

        if(!empty($errorMessage)) {
            if(isset($errorMessage->qty)){
                $message[] = 'Rejected amount exceeded available quantity.';
            }
            flash()->error('An error has occurred while rejecting SKU(s). '.(!empty($message)?"<ul><li>" . implode("</li><li>", $message) . "</li></ul>":''));
        }

        return view('product-management.inventory.reject', $data);
    }

    public function storeRejectProducts(Request $request){
        $rejects = $request->get('qty');
        $skus = array();
        foreach($rejects as $sku_id => $v){
            foreach($v as $channel_id => $qty) {
                if(strlen($qty) <= 0 || $qty <= 0)
                    continue;

                $data['qty'] = $qty;
                $data['sku_id'] = $sku_id;
                $data['channel_id'] = $channel_id;
                $data['remarks'] = $request->get('remarks')[$sku_id][$channel_id];
                $skus['sku'][] = $data;
            }
        }

        $rejectResponse = json_decode($this->postGuzzleClient($skus, 'admin/inventories/bulk_reject')->getBody()->getContents());

        if(empty($rejectResponse->error)){
            $message = 'Successfully rejected SKU(s).';

            flash()->success($message);
            return redirect()->route('products.inventory.index', ['pid' => $request->get('product_ids')]);
        }else{
            $request->request->add(['products'=>json_decode($request->get('original-products'), true)]);
            $errorMessage = $rejectResponse->error;
            return $this->createRejectProducts($request, $errorMessage);
        }
    }

    public function syncImages(Request $request, $id){
        $syncResponse = json_decode($this->postGuzzleClient(array(), 'admin/products/'.$id.'/medias/syncImages')->getBody()->getContents());

        if(empty($syncResponse->error)){
            $response = array(
                'success'   => true,
                'response'  => $syncResponse,
                );
        }else{
            $errorMsg = new MessageBag((array)$syncResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }

        return response()->json($response);
    }

    // bulk update products - product level
    public function showBulkUpdate(Request $request) {
        $data = array();
        $productIDs = $request->input('products');
        $data['product_ids'] = $productIDs;
        $data['categories'] = $this->getCategory();

        return view('product-management.inventory.bulk_update', $data);
    }
    // download prepared barcode list

    public function downloadBarcode(Request $request)
    {
        $items = json_decode(json_encode($request->get('items')));

        $list = array(
            array(
                trans('product-management.create_form_label_hw_sku'),
                trans('product-management.create_form_label_supplier_sku'),
                trans('product-management.create_form_label_name'),
                trans('product-management.create_form_label_brand'),
                trans('product-management.create_form_label_unit_cost'),
                trans('product-management.create_form_label_unit_price'),
                trans('product-management.create_form_label_unit_price_without_gst'),
                trans('product-management.create_form_label_color'),
                trans('product-management.create_form_label_size'),
            )
        );
        foreach($items as $item)
        {
            for($i = 1; $i <= $item->copies; $i++) {
                // $temp = [ $item->hubwire_sku, $item->supplier_code, $item->name, number_format(floatval($item->unit_cost), 2), number_format(floatval($item->retail), 2), $item->retail2,$item->colour, $item->size ];
                $temp = [ $item->hubwire_sku, $item->supplier_code, htmlspecialchars_decode($item->name), $item->brand_name, '', '', '' ,$item->colour, $item->size ];
                array_push($list, $temp);
            }
        }

        $filename = 'products_barcode_inventory_'.uniqid().'.csv';
        $fileloc = '/tmp/'.$filename;
        $fp = fopen($fileloc, 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        $s3 = new MediaService();

        $upload = $s3->uploadFileToS3($fileloc, "barcodes/$filename");
        fclose($fp);

        if(is_array($upload) && array_key_exists("errors", $upload)) {
            return false;
        }

        return redirect()->to($upload->url);
    }

    // bulk barcode print
    public function showBarcode(Request $request) {
        $data = array();
        $ids = $request->input('products');
        $data['product_ids'] = $ids;
        $response = json_decode($this->getGuzzleClient(array('ids'=>$ids), 'admin/inventories/bulk_update_products')->getBody()->getContents());
        // $data['data'] = $response->data;
        $products = array();
        $copies = array();
        // $products = $response->data;
        foreach($response->data as $channel_sku)
        {
            if($channel_sku->channel_sku_quantity < 1) continue;
            $options = explode(',',$channel_sku->options);
            $channel_sku->colour = trim(ucfirst(str_replace('Colour: ','',strip_tags($options[0]))));
            $channel_sku->size = trim(ucfirst(str_replace('Size:','',strip_tags($options[1]))));
            $products[$channel_sku->sku_id] = $channel_sku;
            if(empty($copies[$channel_sku->sku_id])) $copies[$channel_sku->sku_id] = 0;
            $copies[$channel_sku->sku_id] += $channel_sku->channel_sku_quantity;
            $products[$channel_sku->sku_id]->channel_sku_quantity = $copies[$channel_sku->sku_id];
        }

        $data['products'] = $products;

        return view('product-management.inventory.barcode', $data);
    }

    // Grabs a list of product ids and return products and associated skus & channel skus
    public function bulkLoad(Request $request) {
        try {
            $ids = $request->input('product_ids');

            //dd($products);
            $response = json_decode($this->getGuzzleClient(array('ids'=>$ids), 'admin/inventories/bulk_update_products')->getBody()->getContents());

            $data['products'] = $response->data;

            return response()->json(['success' => true, 'data' => $response->data]);
        }
        catch(Exception $e)
        {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => 'An error has occurred on the server: '.$e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function bulkSave(Request $request) {
       try {
            /*
             * fields that can be edited:
             * product_name -> products
             * supplier sku -> sku_supplier_code -> sku
             * sku weight   -> sku_weight -> sku
             * retail price -> channel_sku_price -> channel_sku
             * listing price   -> channel_sku_promo_price channel_sku
             * warehouse coordinates -> channel_sku_coordinates -> channel_sku
             */
            $categories = $this->getCategory(true);
            $changes = $request->input("data");
            $categoryExists = true;
            $i=0;
            foreach($changes as $change)
            {
                if($changes[$i][1]=="category_name")
                {
                    $changes[$i][1] = "category_id";
                    if (array_key_exists($change[3], $categories)) {
                        $changes[$i][3] = $categories[$change[3]];
                    }
                    else {
                        $changes[$i][3] = 'null';
                    }
                }
                $i++;
            }
            $response = json_decode($this->postGuzzleClient(array('changes'=>$changes), 'admin/inventories/bulk_update_products/save')->getBody()->getContents());

            return response()->json(['success' => true, 'message' => 'Your changes were successfully saved.']);
        }
        catch(Exception $e)
        {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => 'An error has occurred on the server: '.$e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function createDeleteProducts(Request $request, $errorMessage=array()){
        $products = $request->get('products');
        $data = array();
        $product = array();
        $channels = array();
        foreach($products as $product){
            $product = json_decode($product);
            $skus = array();
            $data['products'][$product->id]['id'] = $product->id;
            $data['product_ids'][] = $product->id;
            $data['products'][$product->id]['name'] = $product->name;
            if(isset($product->sku_in_channel)){
                foreach($product->sku_in_channel as $chnlSku){
                    $sku['sku_id'] = $chnlSku->sku_id;
                    $sku['hubwire_sku'] = $chnlSku->sku->hubwire_sku;
                    $sku['qty'] = $chnlSku->channel_sku_quantity;
                    $sku['channel_id'] = $chnlSku->channel_id;
                    $sku['channel_name'] = $chnlSku->channel->name;
                    $data['products'][$product->id]['skus'][$chnlSku->sku_id][] = $sku;
                    $data['channels'][$chnlSku->channel_id] = $chnlSku->channel->name;
                }
            }
        }
        $data['original_products'] = htmlentities(json_encode($products));
        if(!empty($errorMessage)) {
            $message = [];
            if(isset($errorMessage->quantity)){
                $message[] = $errorMessage->quantity[0];
            }
            if(isset($errorMessage->sales_count)){
                $message[] = $errorMessage->sales_count[0];
            }
            flash()->error('An error has occurred while deleting product(s). '.(!empty($message)?"<ul><li>" . implode("</li><li>", $message) . "</li></ul>":''));
        }
        return view('product-management.inventory.delete', $data);
    }

    public function storeDeleteProducts(Request $request){
        $data = array();
        $data['products'] = $request->get('reason-delete');

        $response = json_decode($this->postGuzzleClient($data, 'admin/inventories/bulk_delete')->getBody()->getContents());
        if(empty($response->error))
        {
            flash()->success('Successfully deleted product(s).');
            return redirect()->route('products.inventory.index', ['pid' => $request->get('product_ids')]);
        }
        else
        {
            $request->request->add(['products'=>json_decode($request->get('original-products'), true)]);
            $errorMessage = $response->error;
            return $this->createDeleteProducts($request, $errorMessage);
        }
    }

    public function getProductsView($ids, $show_sync_indicator = false, $productView, $channelId = NULL)
    {
        $postData['product_ids'] = explode(',', $ids);
        $data = array();
        $skuInChannel = array();

        $response = json_decode($this->postGuzzleClient($postData, 'admin/inventories/get_products')->getBody()->getContents());
        //dd($response);
        foreach($response as $product){
            if(!empty($product->default_media->media->media_url))
                $img_path = $this->removeExtensionFromMediaURL($product->default_media->media->media_url, $product->default_media->media->media_key) . "_";
            else
                $img_path = '//placehold.it/';

            $img_path.=$this->getImageWH('md-sm');

            $productData = new stdClass();
            $productData->id = $product->id;
            $productData->name = $product->name;
            $productData->brand = !empty($product->brand->prefix) ? $product->brand->prefix : null;

            if(!is_null($channelId)){
                foreach($product->sku_in_channel as $sku){
                    if($sku->channel_id == $channelId){
                        $skuInChannel[] = $sku;
                    }
                }
                $product->sku_in_channel = $skuInChannel;
            }

            $productData->sync_status = !empty($product->sku_in_channel) ? $product->sku_in_channel[0]->sync_status : '';
            $productData->total_quantity = 0;

            if(!empty($product->sku_in_channel)){
                foreach($product->sku_in_channel as $channel){
                    if(!empty($channelId) && ($channel->channel_id!=intval($channelId))) continue;
                    $productData->total_quantity += $channel->channel_sku_quantity;
                }
            }

            $pdata['show_sync_indicator'] = $show_sync_indicator;
            $pdata['admin'] = $this->admin;
            $pdata['product'] = $productData;
            $pdata['img_path'] = $img_path;
            $pdata['channel_id'] = $channelId;
            $view = view($productView, $pdata)->render();
            $data['data'][] = array(
                $view,
                json_encode($product)
            );
        }

        return $data;
    }

    public function export()
    {
        if(empty(request()->get('products'))) return redirect()->back()->withErrors(['No product selected']);

        $list = array(
            array(
                trans('product-management.channel_inventory_csv_label_sku_id'),
                trans('product-management.channel_inventory_csv_channel_sku_id'),
                trans('product-management.channel_inventory_csv_product_id'),
                trans('product-management.channel_inventory_csv_hubwire_sku'),
                trans('product-management.channel_inventory_csv_supplier_sku'),
                trans('product-management.channel_inventory_csv_status'),
                trans('product-management.channel_inventory_csv_merchant'),
                trans('product-management.channel_inventory_csv_channel_name'),
                trans('product-management.channel_inventory_csv_product_name'),
                trans('product-management.channel_inventory_csv_brand_prefix'),
                trans('product-management.channel_inventory_csv_coordinates'),
                trans('product-management.channel_inventory_csv_retail_price'),
                trans('product-management.channel_inventory_csv_listing_price'),
                trans('product-management.channel_inventory_csv_quantity'),

            )
        );
        foreach(request()->get('products') as $json)
        {
            $product = json_decode($json);
            if(!empty($product->sku_in_channel))
            {
                foreach($product->sku_in_channel as $channel_sku)
                {
                    array_push($list, [
                            $channel_sku->sku->sku_id,
                            $channel_sku->channel_sku_id,
                            $product->id,
                            $channel_sku->sku->hubwire_sku,
                            $channel_sku->sku->sku_supplier_code,
                            $channel_sku->channel_sku_active?'Active':'Inactive',
                            $product->merchant_name,
                            $channel_sku->channel->name,
                            $product->name,
                            $product->brand->prefix,
                            $channel_sku->channel_sku_coordinates,
                            $channel_sku->channel_sku_price,
                            $channel_sku->channel_sku_promo_price,
                            $channel_sku->channel_sku_quantity,
                        ]);
                }
            }
        }
        $filename  = '/tmp/products_inventory_export_'.uniqid().'.csv';
        $fp = fopen($filename, 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $this->downloadFile($filename, 'inventory_export', '.csv');
        unlink($filename);

    }

    public function getCategory($flipped=false)
    {
        $categories = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;
        $data = array();
        foreach($categories as $category)
        {
            if($flipped)
                $data[$category->full_name] = $category->id;
            else
                $data[] = $category->full_name;
        }
        return $data;
    }

    public function getProductsByBrand($brandId)
    {
        $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$brandId.'/byBrand')->getBody()->getContents());
        return response()->json($products);
    }
}
