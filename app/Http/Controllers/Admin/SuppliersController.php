<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use Validator;
use App\Http\Traits\GuzzleClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Http\Request;

class SuppliersController extends AdminController
{
	use GuzzleClient;
	protected $supplierRepo;
	protected $merchantRepo;
    protected $userRepo;
    protected $adminId;

    public function __construct(SupplierRepository $supplierRepo,MerchantRepository $merchantRepo, UserRepository $userRepo)
    {
        $this->middleware('permission:view.supplier', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.supplier', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.supplier', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.supplier', ['only' => ['destroy']]);
        $this->supplierRepo = $supplierRepo;
        $this->merchantRepo = $merchantRepo;
        $this->userRepo = $userRepo;
        $this->adminId = \Auth::user();

    }

    public function index($channel_id=null)
    { 
        return view('admin.suppliers.list', ['user'=>\Auth::user(), 'channel_id'=>$channel_id]);
    }

	public function create()
	{
        $user = \Auth::user();
        $merchants = array();
        $activeMerchants = $this->merchantRepo->getActiveMerchants();
        foreach ($activeMerchants as $merchant) {
            $merchants[$merchant->id] = $merchant->name;
        } 
        
        return view('admin.suppliers.create',compact('merchants','user'));
	}

	public function store()
	{
		$inputs = request()->except('_token');

        $rules = array(
            'name' => 'required|string',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'mobile' => 'sometimes',
            'contact_person' => 'required', 
            'merchant_id' => 'sometimes|required|exists:hapi.merchants,id',
            'registration_no' => 'required|string'
		);
        
        $messages = [];
        

		$v = \Validator::make($inputs, $rules, $messages );

        if($v->fails()){
            flash()->error('An error has occurred while creating supplier.');
            return redirect()->back()->withErrors($v)->withInput();
        }
    
        $response = json_decode($this->postGuzzleClient($inputs, 'admin/suppliers')->getBody()->getContents());
		
		if(empty($response->error)) {
			flash()->success('Supplier ' . request()->get('name') . ' has been successfully created.');
            return redirect()->route('admin.suppliers.index');
		}
		else {
			flash()->error('An error has occurred while creating supplier.');
            return redirect()->back()->withErrors($response->error)->withInput();
		}
		
	}

    public function show($id)
    {
        $supplier = $this->supplierRepo->with('merchant')->find($id);
        $user = \Auth::user();
        return view('admin.suppliers.show', compact('user','supplier'));
    }

    public function edit($id)
    {
        $merchants = array();
        $activeMerchants = $this->merchantRepo->getActiveMerchants();
        foreach ($activeMerchants as $merchant) {
            $merchants[$merchant->id] = $merchant->name;
        } 
        $supplier = $this->supplierRepo->with('merchant')->find($id);
        $user = \Auth::user();
        return view('admin.suppliers.edit', compact('user','supplier','merchants'));
    }

	public function update($id)
	{
		$supplier = $this->supplierRepo->find($id);
        $id = $supplier->id;
		$inputs = \Input::except('_token');

		$rules = array(
            'name' => 'sometimes|required|string',
            'address' => 'sometimes|required',
            'phone' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'mobile' => 'sometimes',
            'contact_person' => 'sometimes|required',
            'merchant_id' => 'sometimes|required|exists:hapi.merchants,id',
            'active' => 'sometimes|boolean',
            'registration_no' => 'sometimes|required|string'
		);
        
        $messages = [];
       
        $v = \Validator::make($inputs, $rules, $messages);

		if($v->fails()){

			flash()->error('An error has occurred while editing supplier.');
            return redirect()->back()->withErrors($v)->withInput();
		}

		unset($inputs['_method']);
        
		$response = json_decode($this->putGuzzleClient($inputs, 'admin/suppliers/'.$id)->getBody()->getContents());
		if(empty($response->error)) {

            flash()->success('Supplier ' . $supplier->name . ' has been successfully updated. ');
            return redirect()->route('admin.suppliers.index');
		}
		else {
            $message = array();
            if(isset($response->error->active_merchant)){
                $message[] = $response->error->active_merchant[0];
            }
			flash()->error('An error has occurred while editing supplier. '.(!empty($message)?"<ul><li>" . implode("</li><li>", $message) . "</li></ul>":''));
            return redirect()->back()->withErrors($response->error)->withInput();
		}
	}
	
    public function destroy($id)
    {	
    	$supplier = $this->supplierRepo->find($id);
        $response = json_decode($this->deleteGuzzleClient(null,'admin/suppliers/'.$supplier->id)->getBody()->getContents());
    	if($response->acknowledged) {
    		flash()->success('Supplier ' . $supplier->name . ' has been successfully deleted.');
            return redirect()->route('admin.suppliers.index');
		}
		else {
			flash()->error('An error has occurred while deleting supplier.');
            return redirect()->back()->withErrors($response)->withInput();
		}
    }

    public function getTableData()
    {
        $filters = request()->all();
        if($this->adminId->is('clientadmin|clientuser'))
        {
            $filters['merchant_id'] = $this->adminId->merchant_id;
        }
        $suppliers = json_decode($this->getGuzzleClient($filters, 'admin/suppliers')->getBody()->getContents())->suppliers;

        $data = array();
        foreach ($suppliers as $supplier) {
        	$actions = $this->adminId->can('edit.supplier')?'<a href="/admin/suppliers/'.str_slug($supplier->id).'/edit">Edit</a> ':'';
        	$actions.= $this->adminId->can('edit.supplier')?'| <a href="javascript:void(0);" data-status="'.($supplier->active?0:1).'" data-href="/admin/suppliers/'.$supplier->id.'" data-message="" class="confirm">'.($supplier->active?'Deactivate':'Activate').'</a>':'';
            $data[] = [ "id" => $supplier->id,
                      "name" => $supplier->name,
                      "merchant" => !empty($supplier->merchant)?$supplier->merchant->name:'<em>NULL</em>',
                      "phone" => $supplier->phone,
                      "address" => $supplier->address,
                      "contact_person" => $supplier->contact_person,
                      "status" => $supplier->active?'Active':'Inactive',
                      "mobile" => $supplier->mobile,
                      "updated_at" => $supplier->updated_at,
                      "actions" => $actions
              ];
        }
        return json_encode(array("data" => $data));
    }

    public function getSupplierByMerchant($merchantId){
        $response = json_decode($this->getGuzzleClient(null,'admin/suppliers/'.$merchantId.'/byMerchant')->getBody()->getContents());
        return response()->json($response);
    }
}
