<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use Validator;
use App\Http\Traits\GuzzleClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\MediaService as MediaService;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Image;
use File;
class IssuingCompanyController extends AdminController
{
    use GuzzleClient;
    protected $adminId;

    public function __construct()
    {
        $this->middleware('permission:view.issuingcompany', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.issuingcompany', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.issuingcompany', ['only' => ['create', 'store']]);
        $this->adminId = \Auth::user();
    }

    public function index()
    {
        return view('admin.issuing-company.list', ['user'=>\Auth::user()]);
    }

    public function create()
    {
        return view('admin.issuing-company.create');
    }

    public function store()
    {
        $inputs = request()->except('_token');

        $rules = array(
            'name' => 'required|string',
            'prefix' => 'required|max:6',
            'address' => 'required',
            'date_format' => 'required|string',
            'gst_reg' => 'required',
            'gst_reg_no' => 'required_if:gst_reg,1',
            'logo' => 'required|image',
        );

        if(!empty($inputs['extra'])){
            $data = array();
            foreach($inputs['extra'] as $key => $val)
            {
                if(empty($inputs['extra'][$key]) && empty($inputs['extra_detail'][$key]) )
                {
                    unset($inputs['extra'][$key]); unset($inputs['extra_detail'][$key]);
                    continue;
                }
                $rules['extra.'.$key] = 'sometimes|string';
                $rules['extra_detail.'.$key] = 'sometimes|string';
                $data[] = ['extra'=>$val,'extra_detail'=>$inputs['extra_detail'][$key] ];
            }
        }else{
            unset($inputs['extra']);
            unset($inputs['extra_detail']);
        }

        $v = \Validator::make($inputs, $rules);

        if($v->fails()){
            flash()->error('An error has occurred while creating issuing company.');
            return redirect()->back()->withErrors($v)->withInput();
        }

        if (request()->hasFile('logo')) {
            $mediaService = new MediaService();

            $file = request()->file('logo');
            $allowed = array('jpg','jpeg','png');
            $fileName = $inputs['prefix'].'-logo';
            $extension = strtolower($file->getClientOriginalExtension());
            // This media service will move the file to elsewhere.
            $media = $mediaService->uploadFile($file, 'logo', $allowed, 200, $fileName, 'tax-invoice-logo', false);

            if (!empty($media->errors)) {
                if (is_string($media->errors)) $media->errors = array($media->errors);
                foreach ($media->errors as $error) {
                    $v->getMessageBag()->add('logo', $error);
                }
                flash()->error('An error has occurred while creating issuing company.');
                return redirect()->back()->withErrors($v)->withInput();
            }else{
                // Get Image dimensions list from HAPI
               $imgMaxHeight = 100;
                $path = "/tmp/".uniqid();
                    // Get the image file from the path where the media service have moved it to
                   $img = Image::make(storage_path('temp/' . $fileName . '.' . $extension));

                   if($img->height() >$imgMaxHeight){
                        // resize the image to a maxHeight and constrain aspect ratio (auto width)
                        $img->resize(null, $imgMaxHeight, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                   }
                $img->save($path, 100);

                $mediaKey = 'tax-invoice-logo/'.$fileName.".".$extension;
                $s3Upload = $mediaService->uploadFileToS3($path, $mediaKey);

                // Delete local file
                File::delete(storage_path('temp/' . $fileName . '.' . $extension));

                $inputs['logo_url'] = $media->media_url;
            }
        }

        unset($inputs['logo']);

        $response = json_decode($this->postGuzzleClient($inputs, 'admin/issuing_companies')->getBody()->getContents());
        if(empty($response->error)) {
            flash()->success('Issuing Company ' . $inputs['name'] . ' has been successfully created.');
            return redirect()->route('admin.issuing_companies.index');
        }
        else {
            flash()->error('An error has occurred while creating issuing company.');
            if(!empty($mediaService) && !empty($media->media_id)) $mediaService->deleteFile($media->media_id);
            return redirect()->back()->withErrors($response->error)->withInput();
        }

    }

    public function show($id)
    {
        $user = \Auth::user();
        $issuing_company = json_decode($this->getGuzzleClient(array(), 'admin/issuing_companies/'.$id)->getBody()->getContents());

        $issuing_company->address = str_replace("\\n",PHP_EOL,$issuing_company->address);
        $issuing_company->extra  = json_decode($issuing_company->extra);

            if($issuing_company->gst_reg == 1){
                $issuing_company->gst_reg = 'Yes';
            }else if($issuing_company->gst_reg == 0){
                $issuing_company->gst_reg = 'No';
            }

        $example_date =Carbon::now()->format($issuing_company->date_format);
        $issuing_company->date_format  = $issuing_company->date_format;
        $issuing_company->example_date = "e.g. ".$example_date;
        $issuing_company->document_format = $issuing_company->prefix."-".$example_date."-00001";

        return view('admin.issuing-company.show', compact('user','issuing_company'));
    }

    public function edit($id)
    {

        $user = \Auth::user();
        $response = json_decode($this->getGuzzleClient(array(), 'admin/issuing_companies/'.$id)->getBody()->getContents());
        $data['id'] = $response->id;
        $data['name'] = $response->name;
        $data['gst_reg'] = $response->gst_reg;
        $data['prefix'] = $response->prefix;
        $data['address'] = str_replace("\\n",PHP_EOL,$response->address);
        $data['date_format'] = $response->date_format;
        $data['logo_url'] = $response->logo_url;
        $data['gst_reg_no'] = $response->gst_reg_no;
        $example_date =Carbon::now()->format($response->date_format);
        $data['document_format'] = $response->prefix."-".$example_date."-00001";
        $extras  = json_decode($response->extra);
        $data['extra'] = array();
        $data['extra_detail'] = array();
        if(!empty($extras))
        {
            foreach ($extras as $field => $value) {
                $data['extra'][] = $field;
                $data['extra_detail'][] = $value;
            }
        }
        $data['user'] = $user;
        return view('admin.issuing-company.edit', $data);
    }

    public function update($id)
    {
        $inputs = request()->except('_token');

        $rules = array(
            'name' => 'required|string',
            'prefix' => 'required|max:6',
            'address' => 'required',
            'date_format' => 'required|string',
            'gst_reg' => 'required',
            'gst_reg_no' => 'required_if:gst_reg,1',
        );

        if($inputs['logo_url']==='' || empty($inputs['logo_url'])){
            $rules['logo'] = 'image';
        }
        if(!empty($inputs['extra'])){
            $data = array();
            foreach($inputs['extra'] as $key => $val)
            {
                if(empty($inputs['extra'][$key]) && empty($inputs['extra_detail'][$key]) )
                {
                    unset($inputs['extra'][$key]); unset($inputs['extra_detail'][$key]);
                    continue;
                }
                $rules['extra.'.$key] = 'sometimes|string';
                $rules['extra_detail.'.$key] = 'sometimes|string';
                $data[] = ['extra'=>$val,'extra_detail'=>$inputs['extra_detail'][$key] ];
            }
        }else{
            unset($inputs['extra']);
            unset($inputs['extra_detail']);
        }
        $v = \Validator::make($inputs, $rules);

        if($v->fails()){
            flash()->error('An error has occurred while editing issuing company.');
            return redirect()->back()->withErrors($v)->withInput();
        }

        if (request()->hasFile('logo')) {
            $mediaService = new MediaService();
            $file = request()->file('logo');
            $allowed = array('jpg','jpeg','png');
            $fileName = $inputs['prefix'].'-logo';
            $extension = strtolower($file->getClientOriginalExtension());
            // This media service will move the file to elsewhere.
            $media = $mediaService->uploadFile($file, 'logo', $allowed, 200, $fileName, 'tax-invoice-logo', false);

            if (!empty($media->errors)) {
                if (is_string($media->errors)) $media->errors = array($media->errors);
                foreach ($media->errors as $error) {
                    $v->getMessageBag()->add('logo', $error);
                }
                flash()->error('An error has occurred while creating issuing company.');
                return redirect()->back()->withErrors($v)->withInput();
            }else{
               // Get Image dimensions list from HAPI
               $imgMaxHeight = 100;
                $path = "/tmp/".uniqid();
                    // Get the image file from the path where the media service have moved it to
                   $img = Image::make(storage_path('temp/' . $fileName . '.' . $extension));

                   if($img->height() >$imgMaxHeight){
                        // resize the image to a maxHeight and constrain aspect ratio (auto width)
                        $img->resize(null, $imgMaxHeight, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                   }
                $img->save($path, 100);

                $mediaKey = 'tax-invoice-logo/'.$fileName.".".$extension;
                $s3Upload = $mediaService->uploadFileToS3($path, $mediaKey);

                // Delete local file
                File::delete(storage_path('temp/' . $fileName . '.' . $extension));

                $inputs['logo_url'] = $media->media_url;
            }
        }
        unset($inputs['date_format']);
        unset($inputs['prefix']);
        unset($inputs['logo']);
        unset($inputs['_method']);

        $response = json_decode($this->putGuzzleClient($inputs, 'admin/issuing_companies/'.$id)->getBody()->getContents());
        if(empty($response->error)) {
            flash()->success('Issuing Company ' . $inputs['name'] . ' has been successfully updated.');
            return redirect()->route('admin.issuing_companies.index');
        }
        else {
            $message = [];
            flash()->error('An error has occurred while editing issuing company. '.(!empty($message)?"<ul><li>" . implode("</li><li>", $message) . "</li></ul>":''));
            if(!empty($mediaService) && !empty($media->media_id)) $mediaService->deleteFile($media->media_id);
            return redirect()->back()->withErrors($response->error)->withInput();
        }
    }

    public function destroy($slug)
    {
    }

    public function getTableData()
    {
        $issuing_companies = json_decode($this->getGuzzleClient(request()->all(), 'admin/issuing_companies')->getBody()->getContents());
        $data = array();
        
        foreach ($issuing_companies as $issuing_company) {            
            $issuing_company->name = '<a href="'.route('admin.issuing_companies.show', $issuing_company->id).'">'.$issuing_company->name.'</a>';

            if($issuing_company->gst_reg == 1){
                $issuing_company->gst_reg = 'Yes';
            }else if($issuing_company->gst_reg == 0){
                $issuing_company->gst_reg = 'No';
            }

            $actions = $this->adminId->can('edit.issuingcompany')?'<a href="/admin/issuing_companies/'.$issuing_company->id.'/edit">Edit</a> ':'';
            $issuing_company->actions = $actions;
        }
        return json_encode(array("data" => $issuing_companies));
    }

    public function getPermissions()
    {
        $roles = Role::where('level', '<=', $this->adminId->level())->get();
        return view('admin.roles.list', compact($roles));
    }




}
