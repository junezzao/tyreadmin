<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Repositories\Contracts\MerchantRepository;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use Validator;
use App\Http\Traits\GuzzleClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\MediaService as MediaService;
use Illuminate\Http\Request;
use Log;
class MerchantsController extends AdminController
{
	use GuzzleClient;
	protected $merchantRepo;
	protected $userRepo;
    protected $adminId;

    public function __construct(MerchantRepository $merchantRepo, UserRepository $userRepo)
    {
        $this->middleware('permission:view.merchant', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.merchant', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.merchant', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.merchant', ['only' => ['destroy']]);
        $this->merchantRepo = $merchantRepo;
        $this->userRepo = $userRepo;
        $this->adminId = \Auth::user();
    }

    public function index($channel_id=null)
    {
        if(\Input::get("filterBy"))
            $filterBy = \Input::get("filterBy");
        else
            $filterBy = 'all';

        return view('admin.merchants.list', ['user'=>\Auth::user(), 'channel_id'=>$channel_id, 'filterBy'=>$filterBy]);
    }

	public function create()
	{
		$aes = $this->userRepo->where('category','=', 'Account Executive')->all()->lists('first_name','id');
        $currencies_arr = config('currencies');
		$timezones = Controller::generate_timezone_list();
		return view('admin.merchants.create', ['aes' => $aes, 'timezones' => $timezones, 'currencies_arr' =>  $currencies_arr ] );
	}

	public function store(Request $request)
	{
		\Input::merge(['status'=>'Active']);
		$inputs = \Input::except('_token');

        $rules = array(
            'name' => 'required|string',
            'slug' => 'required|alpha_num|unique:hapi.merchants,slug|max:20',
            'address' => 'sometimes|required_if:self_invoicing,1',
            'contact' => 'required',
            'email' => 'required|email',
            'gst_reg_no' => 'sometimes|required_if:self_invoicing,1',
            'self_invoicing' => 'sometimes|required|boolean',
            'logo' => 'required_if:self_invoicing,1|image',
            'timezone' => 'required',
            'currency' => 'required',
            'ae' => 'required|integer',
            'status' => 'required|string',
            'code'  => 'string'
		);

        $messages = array(
            'address.required_if' => 'The address field is required when self invoicing is checked.',
            'gst_reg_no.required_if' => 'The gst registration number field is required when self invoicing is checked.',
            'logo.required_if' => 'The logo image is required when self invoicing is checked.'
        );
        if(!empty($inputs['currencies'])){
            $data = array();
            foreach($inputs['currencies'] as $key => $val)
            {
                if(empty($inputs['currencies'][$key]) && empty($inputs['rate'][$key]) )
                {
                    unset($inputs['currencies'][$key]); unset($inputs['rate'][$key]);
                    continue;
                }
                $rules['currencies.'.$key] = 'sometimes|required|string|not_in:'.$inputs['currency'];
                $rules['rate.'.$key] = 'sometimes|required|numeric|min:0.0001';
                $data[] = ['currency'=>$val,'rate'=>floatval($inputs['rate'][$key]) ];
                $messages['currencies.'.$key.'.required'] = 'Currency is required.';
                $messages['currencies.'.$key.'.not_in'] = 'Currency cannot be the same with default.';
                $messages['rate.'.$key.'.required'] = 'Currency rate is required.';
                $messages['rate.'.$key.'.numeric'] = 'Currency rate must be in numeric.';
                $messages['rate.'.$key.'.min'] = 'Currency rate minimum is :min.';
            }
        }

		$v = \Validator::make($inputs, $rules, $messages );

        if($v->fails()){
            flash()->error('An error has occurred while creating merchant.');
            return redirect()->back()->withErrors($v)->withInput();
        }

        unset($inputs['logo']);

		if ($request->hasFile('logo')) {
            $mediaService = new MediaService();
            $media = $mediaService->uploadFile($request->file('logo'), 'logo', null, 'testing type', array(), 10000, null, 'merchants/'.$inputs['slug']);
            if (!empty($media->errors)) {
                return redirect()->to('admin/merchants/create')->with('errors', $media->errors);
            }
            $inputs['logo_url'] = $media->media_url;
        }
		$response = json_decode($this->postGuzzleClient($inputs, 'admin/merchants')->getBody()->getContents());

		if(empty($response->error)) {
			flash()->success('Merchant ' . $request->input('name') . ' has been successfully created.');
            return redirect()->route('admin.merchants.index');
		}
		else {
			flash()->error('An error has occurred while creating merchant.');
            if(!empty($mediaService) && !empty($media->media_id)) $mediaService->deleteFile($media->media_id);
			return redirect()->back()->withErrors($response->error)->withInput();
		}

	}

    public function show($slug)
    {
        $merchant = $this->merchantRepo->findBy('slug', $slug);
        $aes = $this->userRepo->where('category','=', 'Account Executive')->all()->lists('first_name','id');
        $currencies_arr = config('currencies');
        $timezones = Controller::generate_timezone_list();
        $user = \Auth::user();
        return view('admin.merchants.show', compact('user','merchant', 'timezones',  'currencies_arr', 'aes'));
    }

    public function edit($slug)
    {
        $merchant = $this->merchantRepo->findBy('slug', $slug);
        $aes = $this->userRepo->where('category','=', 'Account Executive')->all()->lists('first_name','id');
        $currencies_arr = config('currencies');
		$timezones = Controller::generate_timezone_list();
		$user = \Auth::user();
        return view('admin.merchants.edit', compact('user','merchant', 'timezones',  'currencies_arr', 'aes'));
    }

	public function update(Request $request,$slug)
	{
		$merchant = $this->merchantRepo->findBy('slug', $slug);
        $id = $merchant->id;
		$inputs = \Input::except('_token');

		$rules = array(
            'name' => 'sometimes|required|string',
            'slug' => 'sometimes|required|alpha_num|unique:hapi.merchants,slug,'.$merchant->id.'|max:20',
            'address' => 'sometimes|required_if:self_invoicing,1',
            'contact' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'gst_reg_no' => 'sometimes|required_if:self_invoicing,1',
            'self_invoicing' => 'sometimes|required|boolean',
            'timezone' => 'sometimes|required',
            'currency' => 'sometimes|required',
            'ae' => 'sometimes|required|integer',
            'status' => 'sometimes|required|string',
            'created_at' => 'sometimes|date_format:Y-m-d H:i:s',
            'updated_at' => 'sometimes|date_format:Y-m-d H:i:s',
            'code'  => 'string'
		);
        if($merchant->logo_url==='' || empty($merchant->logo_url)){
            $rules['logo'] = 'required_if:self_invoicing,1|image';
        }

        $messages = array(
            'address.required_if' => 'The address field is required when self invoicing is checked.',
            'gst_reg_no.required_if' => 'The gst registration number field is required when self invoicing is checked.',
            'logo.required_if' => 'The logo image is required when self invoicing is checked.'
        );
        if(!empty($inputs['currencies'])){
            $data = array();
            foreach($inputs['currencies'] as $key => $val)
            {
                if(empty($inputs['currencies'][$key]) && empty($inputs['rate'][$key]) )
                {
                    unset($inputs['currencies'][$key]); unset($inputs['rate'][$key]);
                    continue;
                }
                $rules['currencies.'.$key] = 'required|alpha_num|not_in:'.$inputs['currency'];
                $rules['rate.'.$key] = 'required|numeric|min:0.0001';
                $data[] = ['currency'=>$val,'rate'=>floatval($inputs['rate'][$key]) ];
                $messages['currencies.'.$key.'.required'] = 'Currency is required.';
                $messages['currencies.'.$key.'.not_in'] = 'Currency cannot be the same with default.';
                $messages['rate.'.$key.'.required'] = 'Currency rate is required.';
                $messages['rate.'.$key.'.numeric'] = 'Currency rate must be in numeric.';
                $messages['rate.'.$key.'.min'] = 'Currency rate minimum is :min.';
            }
        }
        $v = \Validator::make($inputs, $rules, $messages);

		if($v->fails()){
            flash()->error('An error has occurred while editing merchant.');
            return redirect()->back()->withErrors($v)->withInput();
		}

		if ($request->hasFile('logo')) {
            $mediaService = new MediaService();
            $media = $mediaService->uploadFile($request->file('logo'), 'logo', null, 'testing type', array(), 10000, null, 'merchants/'.$inputs['slug']);
            if (!empty($media->errors)) {
                return redirect()->to('admin/merchants/create')->with('errors', $media->errors);
            }
            $inputs['logo_url'] = $media->media_url;
        }

        // unset($inputs['slug']);
        unset($inputs['_method']);
        unset($inputs['logo']);

		$response = json_decode($this->putGuzzleClient($inputs, 'admin/merchants/'.$id)->getBody()->getContents());
		if(empty($response->error)) {
            flash()->success('Merchant ' . $merchant->name . ' has been successfully updated.');
            return redirect()->route('admin.merchants.index');
		}
		else {
            $message = [];
            if(isset($response->error->active_users)){
                $message[] = $response->error->active_users[0];
            }
            if(isset($response->error->active_brands)){
                $message[] = $response->error->active_brands[0];
            }
            if(isset($response->error->active_suppliers)){
                $message[] = $response->error->active_suppliers[0];
            }
            flash()->error('An error has occurred while editing merchant. '.(!empty($message)?"<ul><li>" . implode("</li><li>", $message) . "</li></ul>":''));
            if(!empty($mediaService) && !empty($media->media_id)) $mediaService->deleteFile($media->media_id);
			return redirect()->back()->withErrors($response->error)->withInput();
		}
	}

    public function destroy($slug)
    {
    	$merchant = $this->merchantRepo->findBy('slug', $slug);
        $response = json_decode($this->deleteGuzzleClient(null,'admin/merchants/'.$merchant->id)->getBody()->getContents());
        
    	if(empty($response->error)) {
    		flash()->success('Merchant ' . $merchant->name . ' has been successfully deleted.');
            return redirect()->route('admin.merchants.index');
		}
		else {
            $msg = 'An error has occurred while deleting merchant. Please fix the issues below and try again: <ul>';
            foreach ($response->error as $error => $desc) {
                $msg .= '<li>'.$error.' : '.$desc[0].'</li>';   
            }
            $msg .= '</ul>';
			flash()->error($msg);
            return redirect()->back()->withErrors($response->error)->withInput();
		}
    }

    public function getTableData()
    {
        $type = request()->input('filterBy');
        if(strtolower($type) == 'all'){
            $merchants = json_decode($this->getGuzzleClient(request()->except('filterBy'), 'admin/merchants')->getBody()->getContents())->merchants;
        }elseif(strtolower($type) == 'newsignups'){
            $merchants = json_decode($this->postGuzzleClient(array(), 'admin/merchants/new-signups')->getBody()->getContents())->merchants;
        }elseif(strtolower($type) == 'livebymonth'){
            $merchants = json_decode($this->postGuzzleClient(array('byDate'=>'month'), 'admin/merchants/last-live')->getBody()->getContents())->merchants;
        }elseif(strtolower($type) == 'livebyweek'){
            $merchants = json_decode($this->postGuzzleClient(array('byDate'=>'week'), 'admin/merchants/last-live')->getBody()->getContents())->merchants;
        }
        $data = array();
        if(!empty($merchants)){
            foreach ($merchants as $merchant) {
            	$ae = $merchant->ae;
            	$actions = $this->adminId->can('edit.merchant')?'<a href="/admin/merchants/'.str_slug($merchant->slug).'/edit">Edit</a> ':'';
            	$actions.= $this->adminId->can('edit.merchant')?'| <a href="javascript:void(0);" data-status="'.($merchant->status=='Active'?'Inactive':'Active').'" data-href="/admin/merchants/'.str_slug($merchant->slug).'" data-message="'.($merchant->status=='Active'?'Users & Suppliers under this account will be deactivate too.':'').'" class="confirm">'.($merchant->status=='Active'?'Deactivate':'Activate').'</a>':'';
                $data[] = [
                          "id" => $merchant->id,
                          "name" => '<a href="'.route('admin.merchants.show', str_slug($merchant->slug)).'">'.$merchant->name.'</a>',
                          "ae" => !empty($ae->first_name)?$ae->first_name:'',
                          "status" => $merchant->status,
                          "created_at" => $merchant->created_at,
                          "updated_at" => $merchant->updated_at,
                          "actions" => $actions
                  ];
            }
        }else{
            $data = array();
        }

        return json_encode(array("data" => $data));
    }

    public function getPermissions()
    {
        $roles = Role::where('level', '<=', $this->adminId->level())->get();
        return view('admin.roles.list', compact($roles));
    }

    public function getMerchantsByChannel($channelId)
    {
        $merchants = json_decode($this->getGuzzleClient(request()->all(), 'admin/merchants/'.$channelId.'/byChannel')->getBody()->getContents());

        return response()->json($merchants);
    }
}
