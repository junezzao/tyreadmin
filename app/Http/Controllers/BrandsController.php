<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Merchant;
use App\Http\Traits\GuzzleClient;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Form;

class BrandsController extends Controller
{
    use GuzzleClient;
    protected $admin;
    protected $statuses;

    public function __construct()
    {
        $this->middleware('permission:view.brand', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.brand', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.brand', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.brand', ['only' => ['destroy']]);
        $this->admin = \Auth::user();
        $this->statuses = array(1 => "Active", 0 => "Inactive");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_id=null)
    {
        $data['statuses'] = array("Active" => "Active", "Inactive" => "Inactive");
        $data['merchants'] = array();
        $merchants = json_decode($this->getGuzzleClient(array('channel_id'=>$channel_id), 'admin/merchants')->getBody()->getContents())->merchants;
        foreach ($merchants as $merchant) {
            $data['merchants'][$merchant->name] = $merchant->name;
        }

        $brands = json_decode($this->getGuzzleClient(array('channel_id'=>$channel_id), 'admin/brands')->getBody()->getContents())->brands;
        $data['brands'] = array();
        foreach ($brands as $brand) {
            if ($this->admin->is('superadministrator|administrator|channelmanager') || $brand->merchant_id==$this->admin->merchant_id) {
              $data['brands'][$brand->name] = $brand->name;
            }      
        } 

        $data['user'] = $this->admin;
        $data['channel_id'] = $channel_id;
        return view('brands.index', $data);
    }

    public function archived($channel_id=null)
    {
        $data['merchants'] = array();
        $merchants = json_decode($this->getGuzzleClient(array('channel_id'=>$channel_id), 'admin/merchants')->getBody()->getContents())->merchants;
        foreach ($merchants as $merchant) {
            $data['merchants'][$merchant->name] = $merchant->name;
        }

        $brands = json_decode($this->getGuzzleClient(array('channel_id'=>$channel_id), 'admin/brands')->getBody()->getContents())->brands;
        $data['brands'] = array();
        foreach ($brands as $brand) {
            if ($this->admin->is('superadministrator|administrator|channelmanager') || $brand->merchant_id==$this->admin->merchant_id) {
                $data['brands'][$brand->name] = $brand->name;
            }
        }

        $data['user'] = $this->admin;
        $data['channel_id'] = $channel_id;
        return view('brands.deactivated', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['merchants'] = array();
        $data['user'] = $this->admin;
        $data['statuses'] = $this->statuses;

        if ($this->admin->is('clientadmin')) {
            $data['selectedMerchant'] = $this->admin->merchant->id;
            $data['merchants'][$this->admin->merchant->id] = $this->admin->merchant->name;
        } else {
            $response = $this->getGuzzleClient(array(), 'admin/merchants');
            $merchants = json_decode($response->getBody()->getContents())->merchants;
            
            foreach ($merchants as $merchant) {
                $data['merchants'][$merchant->id] = $merchant->name;
            }
        }
        return view('brands.create', $data);
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
        $inputs = $request->only('name','active','merchant_id', 'prefix');
        $inputs['prefix'] = strtoupper($inputs['prefix']);
        $this->validate($request, array(
            'name'        => 'required',
            'prefix'      => 'required',
            'active'      => 'required',
            'merchant_id' => 'required',
        ));

        $response = json_decode($this->postGuzzleClient($inputs, 'admin/brands')->getBody()->getContents());
        if (isset($response->code) && $response->code==422) {
            flash()->error("An error has occurred while creating the brand.");       
            return back()->withInput()->withErrors($response->error);
        } else {
            flash()->success('The brand was successfully created.');
            return redirect()->route('brands.index');
        }
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
    public function edit($id)
    {
        //
        $data['brand'] = json_decode($this->getGuzzleClient([], 'admin/brands/'.$id)->getBody()->getContents());
        $data['brand']->active = intval($data['brand']->active);
        $data['merchants'] = array();
        $data['user'] = $this->admin;
        $data['statuses'] = $this->statuses;
        $data['id'] = $id;
        
        if ($this->admin->is('clientadmin')) {
            $data['selectedMerchant'] = $this->admin->merchant->id;
            $data['merchants'][$this->admin->merchant->id] = $this->admin->merchant->name;
        } else {
            $response = $this->getGuzzleClient(array(), 'admin/merchants');
            $merchants = json_decode($response->getBody()->getContents())->merchants;
            
            foreach ($merchants as $merchant) {
                $data['merchants'][$merchant->id] = $merchant->name;
            }
        }

        return view('brands.edit', $data);
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
        
        $inputs = $request->only('name','active','merchant_id');
        
        $this->validate($request, [
            'name'        => 'sometimes|required|max:255',
            'active'      => 'sometimes|required',
            'merchant_id'    => 'sometimes|required',
        ]);

        $response = json_decode($this->putGuzzleClient($inputs, 'admin/brands/'.$id)->getBody()->getContents());
        
        if (isset($response->code)) {
            $message = [];
            if(isset($response->error->active_products)){
                $message[] = $response->error->active_products[0];
            }
            if(isset($response->error->active_merchant)){
                $message[] = $response->error->active_merchant[0];
            }

            flash()->error('An error has occurred while editing the brand. '.(!empty($message)?"<ul><li>" . implode("</li><li>", $message) . "</li></ul>":''));     
            return back()->withInput()->withErrors($response->error);
        }
        else {
            flash()->success('The brand was successfully updated.');
            return redirect()->route('brands.index');
        }
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
        $response = json_decode($this->deleteGuzzleClient([], 'admin/brands/'.$id)->getBody()->getContents());
        if (isset($response->code) && $response->code==422) {
            flash()->error($response->error->product[0]);       
        } else {
            flash()->success('The brand was successfully deleted.');
        }
        
        return redirect()->route('brands.index');
    }

    public function getByMerchant($merchantId)
    {
      $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$merchantId.'/byMerchant')->getBody()->getContents());

      return response()->json($brands);
    }

    public function getTableData()
    {
        $brands = json_decode($this->getGuzzleClient(request()->all(), 'admin/brands')->getBody()->getContents())->brands;
        $data = array();
        $merchants = json_decode($this->getGuzzleClient(request()->all(), 'admin/merchants')->getBody()->getContents())->merchants;
        foreach ($merchants as $merchant) {
            $merchants[$merchant->id] = $merchant->name;
        }
        foreach ($brands as $brand) {
            $actions = ($this->admin->can('edit.brand') && is_null($brand->deleted_at)) ? '<a href="'.route('brands.edit',['id'=>$brand->id]).'">Edit</a>':'';
            $actions .= (!empty($actions) && $this->admin->can('edit.brand')) ? ' | ' : '';
            $actions .= $this->admin->can('edit.brand')?'<a href="javascript:void(0);" data-status="'.($brand->active?0:1).'" data-href="'.route('brands.update',['id'=>$brand->id]).'" data-message="" class="confirm">'.($brand->active?'Deactivate':'Activate').'</a>':'';

            $dataArray = ["id" => $brand->id,
                    "name" => $brand->name,
                    "prefix" => $brand->prefix,
                    "merchant" => ($brand->merchant_id!=0 && isset($merchants[$brand->merchant_id]))?$merchants[$brand->merchant_id]:"", 
                    "status"  => isset($brand->active)?$this->statuses[$brand->active]:'',
                    "updated_at" => $brand->updated_at,
                    "product_count" => isset($brand->products_total->total) ? $brand->products_total->total : (isset($brand->product_count) ? $brand->product_count : 0),
                    "actions" => $actions,
                ];

          if (($this->admin->is('clientadmin|clientuser') && $this->admin->merchant_id==$brand->merchant_id) || !$this->admin->is('clientadmin|clientuser')) {
            $data[] = $dataArray;
          }
        }
        
        return json_encode(array("data" => $data));
    }
}
