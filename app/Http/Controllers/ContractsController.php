<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\GuzzleClient;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Form;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Carbon\Carbon;
use Excel;

class ContractsController extends Controller
{
    use GuzzleClient;

    protected $admin;
    protected $feeTypes;
    protected $feeBases;
    protected $feeOperands;

    public function __construct()
    {
        $this->admin = \Auth::user();
        $this->middleware('permission:view.contract', ['only' => ['index']]);
        $this->middleware('permission:edit.contract', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.contract', ['only' => ['create', 'store', 'duplicate']]);
        $this->middleware('permission:delete.contract', ['only' => ['destroy']]);
    }

    public function initializeDdData()
    {
        $this->feeTypes = array(
            'Fixed Rate'    =>  'Fixed Rate',
            'Percentage'    =>  'Percentage',
        );

        $this->feeBases = array(
            'Not Applicable'                =>  'Not Applicable',
            'Retail Price'                  =>  'Retail Price',
            'Listing Price'                 =>  'Listing Price',
            'Sold Price'                    =>  'Sold Price',
            'Total Sales Retail Price'      =>  'Total Sales Retail Price',
            'Total Sales Listing Price'     =>  'Total Sales Listing Price',
            'Total Sales Sold Price'        =>  'Total Sales Sold Price',
            'Order Count'                   =>  'Order Count',
            'Order Item Count'              =>  'Order Item Count' 
        );

        $this->feeOperands = array(
            'Not Applicable'    =>  'Not Applicable',
            'Above'             =>  'Above',
            'Between'           =>  'Between',
            'Difference'        =>  'Difference',
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contracts.index', ['user'=>$this->admin]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $this->initializeDdData();
        $data['merchants']      = array();
        $data['feeTypes']       = $this->feeTypes;
        $data['feeBases']       = $this->feeBases;
        $data['feeOperands']    = $this->feeOperands;
        $data['categories']     = array();

        $categories = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;

        foreach($categories as $category){
            $data['categories'][$category->id] = $category->full_name;
        }

        $merchantsResponse = $this->getGuzzleClient(array(), 'admin/merchants');
        $merchants = json_decode($merchantsResponse->getBody()->getContents())->merchants;

        foreach ($merchants as $merchant) {
            if($merchant->status == 'Active'){
                $data['merchants']['Active'][$merchant->id] = $merchant->name;
            }elseif($merchant->status == 'Inactive'){
                $data['merchants']['Inactive'][$merchant->id] = $merchant->name;
            }
        }
        // dd($data);

        return view('contracts.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name'                       => 'required',
            'merchant'                   => 'required|integer|min:1',
            'brand'                      => 'required|integer|min:1',
            'start_date'                 => 'required|date_format:Y-m-d',
            'end_date'                   => 'sometimes|date_format:Y-m-d|after:start_date',
            'minimum_guarantee'          => 'required',
            'minimum_guarantee_amount'   => 'required_if:minimum_guarantee,"applicable"|numeric|min:0.01',
            'guarantee-charge'           => 'required',
            'storage_fee'                => 'sometimes|numeric',
            'inbound_fee'                => 'sometimes|numeric',
            'outbound_fee'               => 'sometimes|numeric',
            'return_fee'                 => 'sometimes|numeric',
            'shipped_fee'                => 'sometimes|numeric',
        );

        $v = \Validator::make($request->except('_token'), $rules);

        if($v->fails()){
            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // get channels
                $channelsDropdown = array();
                $channels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$request->input('merchant'))->getBody()->getContents())->channels;
                foreach($channels as $channel){
                    $channelsDropdown[$channel->id] = $channel->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
                $request->session()->flash('channels', $channelsDropdown);
            }

            if(!empty($request->input('brand'))){
                $productsDropdown = array();
                $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$request->input('brand').'/byBrand')->getBody()->getContents())->products;
                foreach($products as $product){
                    $productsDropdown[$product->id] = $product->name;
                }

                $request->session()->flash('products', $productsDropdown);
            }

            return back()->withErrors($v)->withInput();
        }

        $postData = array();
        $postData['contract'] = array(
            'name'          => $request->input('name'),
            'brand_id'      => $request->input('brand'),
            'merchant_id'   => $request->input('merchant'),
            'start_date'    => $request->input('start_date'),
            'end_date'      => ($request->input('end_date') != '')?$request->input('end_date'):NULL,
            'guarantee'     => $request->input('minimum_guarantee_amount', NULL),
            'min_guarantee' => $request->input('guarantee-charge'),
            'storage_fee'   => $request->input('storage_fee'),
            'inbound_fee'   => $request->input('inbound_fee'),
            'outbound_fee'  => $request->input('outbound_fee'),
            'return_fee'    => $request->input('return_fee'),
            'shipped_fee'   => $request->input('shipped_fee'),
        );

        if(count($request->input('fee-info') > 0)){
            foreach($request->input('fee-info') as $feeInfo){
                $postRule = array(
                    'fixed_charge'  => $feeInfo['fixed-charge'],
                    'type'          => $feeInfo['type'],
                    'type_amount'   => $feeInfo['amount'],
                    'base'          => $feeInfo['base'],
                    'operand'       => (isset($feeInfo['base']) && $feeInfo['base'] == 'Not Applicable') ? 'Not Applicable' : $feeInfo['operand'],
                    'min_amount'    => (isset($feeInfo['min-amount'])) ? $feeInfo['min-amount'] : NULL,
                    'max_amount'    => (isset($feeInfo['max-amount'])) ? $feeInfo['max-amount'] : NULL,
                    'channels'      => (isset($feeInfo['channel'])) ? $feeInfo['channel'] : array(),
                    'products'      => (isset($feeInfo['product'])) ? $feeInfo['product'] : array(),
                    'categories'    => (isset($feeInfo['category'])) ? $feeInfo['category'] : array(),
                );
                $postData['rule'][] = $postRule;
            }
        }

        $response = json_decode($this->postGuzzleClient($postData, 'contracts')->getBody()->getContents());
        // dd($response);
        if(empty($response->error)){
            $message = 'Contract has been successfully created.';

            flash()->success($message);

            return redirect()->route('contracts.index');
        }else{
            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // get channels
                $channelsDropdown = array();
                $channels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$request->input('merchant'))->getBody()->getContents())->channels;
                foreach($channels as $channel){
                    $channelsDropdown[$channel->id] = $channel->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
                $request->session()->flash('channels', $channelsDropdown);
            }

            if(!empty($request->input('brand'))){
                $productsDropdown = array();
                $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$request->input('brand').'/byBrand')->getBody()->getContents())->products;
                foreach($products as $product){
                    $productsDropdown[$product->id] = $product->name;
                }

                $request->session()->flash('products', $productsDropdown);
            }

            if(isset($response->duplicate) && count($response->duplicate) > 0){
                foreach($response->duplicate as $index => $contract){
                    $response->duplicate[$index]->lockDate = false;
                    if(!empty($contract->end_date)){
                        $endDate = Carbon::createFromFormat('Y-m-d', $contract->end_date);
                        $endDate->hour = 23;
                        $endDate->minute = 59;
                        $endDate->second = 59;

                        $now = new Carbon();

                        if($endDate->lte($now)){
                            $response->duplicate[$index]->lockDate = true;
                        }
                    }
                }
                $request->session()->flash('duplicateContracts', $response->duplicate);

                flash()->error('Unable to create contract due to overlapping validity period with existing contracts. Click <a href="#" style="color: #3c8dbc;" data-toggle="modal" data-target="#dateEditorModal">here</a> to resolve.' );
            }else{
                flash()->error('An error has occurred while creating contract.');
            }

            return back()->withInput()->withErrors($response->error);
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
        $this->initializeDdData();
        $response = json_decode($this->getGuzzleClient(array(), 'contracts/'.$id)->getBody()->getContents());
        //dd($response);
        $data = array();
        $data['contract']       = $response;
        $data['merchants']      = array();
        $data['feeTypes']       = $this->feeTypes;
        $data['feeBases']       = $this->feeBases;
        $data['feeOperands']    = $this->feeOperands;
        $data['categories']     = array();
        $data['lockDate']       = false;

        if(!empty($response->end_date)){
            $endDate = Carbon::createFromFormat('Y-m-d', $response->end_date);
            $endDate->hour = 23;
            $endDate->minute = 59;
            $endDate->second = 59;

            $now = new Carbon();

            if($endDate->lte($now)){
                $data['lockDate'] = true;
            }
        }

        $categories = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;

        $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$response->merchant_id.'/byMerchant')->getBody()->getContents());
        $channels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$response->merchant_id)->getBody()->getContents())->channels;
        $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$response->brand_id.'/byBrand')->getBody()->getContents())->products;

        $merchantsResponse = $this->getGuzzleClient(array(), 'admin/merchants');
        $merchants = json_decode($merchantsResponse->getBody()->getContents())->merchants;

        foreach ($merchants as $merchant) {
            if($merchant->status == 'Active'){
                $data['merchants']['Active'][$merchant->id] = $merchant->name;
            }elseif($merchant->status == 'Inactive'){
                $data['merchants']['Inactive'][$merchant->id] = $merchant->name;
            }
        }

        foreach($brands as $brand){
            if($brand->active){
                $data['brands']['Active'][$brand->id] = $brand->name;
            }else{
                $data['brands']['Inactive'][$brand->id] = $brand->name;
            }
        }

        foreach($channels as $channel){
            $data['channels'][$channel->id] = $channel->name;
        }

        foreach($products as $product){
            $data['products'][$product->id] = $product->name;
        }
        
        foreach($categories as $category){
            $data['categories'][$category->id] = $category->full_name;
        }
        
        return view('contracts.edit', $data);
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
        $rules = array(
            'name'                       => 'required',
            'start_date'                 => 'required|date_format:Y-m-d',
            'end_date'                   => 'sometimes|date_format:Y-m-d|after:start_date',
            'merchant'                   => 'required|integer|min:1',
            'brand'                      => 'required|integer|min:1',
            'minimum_guarantee'          => 'required',
            'minimum_guarantee_amount'   => 'required_if:minimum_guarantee,"applicable"|integer|min:0.01',
            'guarantee-charge'           => 'required',
            'storage_fee'                => 'sometimes|numeric',
            'inbound_fee'                => 'sometimes|numeric',
            'outbound_fee'               => 'sometimes|numeric',
            'return_fee'                 => 'sometimes|numeric',
            'shipped_fee'                => 'sometimes|numeric',
        );

        $v = \Validator::make($request->except('_token'), $rules);

        if($v->fails()){
            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // get channels
                $channelsDropdown = array();
                $channels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$request->input('merchant'))->getBody()->getContents())->channels;
                foreach($channels as $channel){
                    $channelsDropdown[$channel->id] = $channel->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
                $request->session()->flash('channels', $channelsDropdown);
            }

            if(!empty($request->input('brand'))){
                $productsDropdown = array();
                $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$request->input('brand').'/byBrand')->getBody()->getContents())->products;
                foreach($products as $product){
                    $productsDropdown[$product->id] = $product->name;
                }

                $request->session()->flash('products', $productsDropdown);
            }

            return back()->withErrors($v)->withInput();
        }

        $postData = array();
        $postData['contract'] = array(
            'name'          => $request->input('name'),
            'brand_id'      => $request->input('brand'),
            'merchant_id'   => $request->input('merchant'),
            'start_date'    => $request->input('start_date'),
            'end_date'      => ($request->input('end_date') != '')?$request->input('end_date'):NULL,
            'guarantee'     => $request->input('minimum_guarantee_amount', NULL),
            'min_guarantee' => $request->input('guarantee-charge'),
            'storage_fee'   => $request->input('storage_fee'),
            'inbound_fee'   => $request->input('inbound_fee'),
            'outbound_fee'  => $request->input('outbound_fee'),
            'return_fee'    => $request->input('return_fee'),
            'shipped_fee'   => $request->input('shipped_fee'),
        );

        if(count($request->input('fee-info') > 0)){
            foreach($request->input('fee-info') as $feeInfo){
                $postRule = array(
                    'id'            => (isset($feeInfo['rule_id'])) ? $feeInfo['rule_id'] : '',
                    'fixed_charge'  => $feeInfo['fixed-charge'],
                    'type'          => $feeInfo['type'],
                    'type_amount'   => $feeInfo['amount'],
                    'base'          => $feeInfo['base'],
                    'operand'       => (isset($feeInfo['base']) && $feeInfo['base'] == 'Not Applicable') ? 'Not Applicable' : $feeInfo['operand'],
                    'min_amount'    => (isset($feeInfo['min-amount'])) ? $feeInfo['min-amount'] : NULL,
                    'max_amount'    => (isset($feeInfo['max-amount'])) ? $feeInfo['max-amount'] : NULL,
                    'channels'      => (isset($feeInfo['channel'])) ? $feeInfo['channel'] : array(),
                    'products'      => (isset($feeInfo['product'])) ? $feeInfo['product'] : array(),
                    'categories'    => (isset($feeInfo['category'])) ? $feeInfo['category'] : array(),
                );
                $postData['rule'][] = $postRule;
            }
        }

        $response = json_decode($this->putGuzzleClient($postData, 'contracts/'.$id)->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Contract has been successfully updated.';

            flash()->success($message);

            return back();
        }else{
            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // get channels
                $channelsDropdown = array();
                $channels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$request->input('merchant'))->getBody()->getContents())->channels;
                foreach($channels as $channel){
                    $channelsDropdown[$channel->id] = $channel->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
                $request->session()->flash('channels', $channelsDropdown);
            }

            if(!empty($request->input('brand'))){
                $productsDropdown = array();
                $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$request->input('brand').'/byBrand')->getBody()->getContents())->products;
                foreach($products as $product){
                    $productsDropdown[$product->id] = $product->name;
                }

                $request->session()->flash('products', $productsDropdown);
            }

            if(isset($response->duplicate) && count($response->duplicate) > 0){
                foreach($response->duplicate as $index => $contract){
                    $response->duplicate[$index]->lockDate = false;
                    if(!empty($contract->end_date)){
                        $endDate = Carbon::createFromFormat('Y-m-d', $contract->end_date);
                        $endDate->hour = 23;
                        $endDate->minute = 59;
                        $endDate->second = 59;

                        $now = new Carbon();

                        if($endDate->lte($now)){
                            $response->duplicate[$index]->lockDate = true;
                        }
                    }
                }
                $request->session()->flash('duplicateContracts', $response->duplicate);

                flash()->error('Unable to update contract due to overlapping validity period with existing contracts. Click <a href="#" style="color: #3c8dbc;" data-toggle="modal" data-target="#dateEditorModal">here</a> to resolve.' );
            }else{
                flash()->error('An error has occurred while updating contract.');
                flash()->error(json_encode($response->error));
            }
            return back()->withInput();
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
        $response = json_decode($this->deleteGuzzleClient(null,'contracts/'.$id)->getBody()->getContents());

        if(empty($response->error)) {
            flash()->success('Contract #' . $id . ' has been successfully deleted.');
            return redirect()->route('contracts.index');
        }
        else {
            $msg = 'An error has occurred while deleting contract. <ul>';
            foreach ($response->error as $error => $desc) {
                $msg .= '<li>'.$error.' : '.$desc[0].'</li>';   
            }
            $msg .= '</ul>';
            flash()->error($msg);
            return redirect()->back();
        }
    }

    public function duplicate($id)
    {
        $response = json_decode($this->postGuzzleClient(array(),'contracts/'.$id.'/duplicate')->getBody()->getContents());

        if(empty($response->error)){
            $msg = 'Contract ID '.$id.' ['.$response->contract_index->name.'] has been duplicated successfully. <b>Please enter the start and end date of the validity period.</b>';
            flash()->success($msg);

            return redirect()->route('contracts.edit', $response->contract_index->id);
        }else{
            $msg = 'An error has occurred while duplicating contract, please try again later.';
            flash()->error($msg);

            return redirect()->back();
        }
    }

    public function getTableData(){
        $data = array();
        $contracts = json_decode($this->getGuzzleClient(array(), 'contracts')->getBody()->getContents())->contracts;

        foreach($contracts as $contract){
            $actions = array();
            if($this->admin->can('edit.contract')){
                $actions[] = '<a href="'.route('contracts.edit', [$contract->id]).'">Edit</a>';
            }

            if($this->admin->can('create.contract')){
                $actions[] = '<a href="'.route('contracts.duplicate', [$contract->id]).'">Duplicate</a>';
            }

            if($this->admin->can('delete.contract')){
                $actions[] = '<form action="'.route('contracts.destroy', [$contract->id]).'" method="POST"><button type="button"  class="btn btn-link no-padding btn-delete-contract">Delete</button><input type="hidden" name="_method" value="DELETE"></form>';
            }

            $data[] = array(
                'contract_id'   => $contract->id,
                'merchant'      => $contract->merchant->name,
                'brand'         => $contract->brand->name,
                'name'          => $contract->name,
                'created_at'    => $contract->created_at,
                'updated_at'    => $contract->updated_at,
                'start_date'    => $contract->start_date,
                'end_date'      => $contract->end_date,
                'actions'       => implode(' | ', $actions),
            );
        }

        return json_encode(array("data" => $data));
    }

    public function updateDate(Request $request, $id)
    {
        $return = array();
        $rules = array(
            'start_date'                 => 'required|date_format:Y-m-d',
            'end_date'                   => 'sometimes|date_format:Y-m-d|after:start_date',
        );

        $v = \Validator::make($request->except('_token'), $rules);

        if($v->fails()){
            $errorMsgs = $v->messages();

            foreach($errorMsgs->all() as $errorMsg){
                $return['errors'][] = $errorMsg;
            }
        }else{
            $postData = array();
            $postData['start_date'] = $request->input('start_date');
            $postData['end_date'] = ($request->input('end_date') != '')?$request->input('end_date'):NULL;

            $response = json_decode($this->postGuzzleClient($postData,'contracts/'.$id.'/update-dates')->getBody()->getContents());

            if(!empty($response->error)){
                if(count($response->duplicate) > 0){
                    $dupContractLinks = array();
                    foreach($response->duplicate as $dupId){
                        $dupContractLinks[] = '<a href="' . route('contracts.edit', $dupId) . '" target="_blank">'.$dupId.'</a>';
                    }
                    $return['errors'][] = 'Selected date(s) clashed with the following contracts: ' . implode(', ', $dupContractLinks);
                }else{
                    $return['errors'][] = 'An error has occured while updating date(s), please try again later.';
                }
            }else{
                $return['success'] = true;
            }
        }

        return json_encode($return);
    }

    public function contractCalculator()
    {
        $contractHubwire = json_decode($this->getGuzzleClient(array(), 'contracts')->getBody()->getContents())->contracts;
        $data['contract']['hubwire'] =  array();
        foreach($contractHubwire as $contract){
            if (is_null($contract->end_date)) {
                $validityDate = $contract->start_date;
            }else {
                $validityDate = $contract->start_date.' - '.$contract->end_date;
            }
            $data['contract']['hubwire'][$contract->id] = $contract->name.' ( '.$validityDate.' )';
        }
        asort($data['contract']['hubwire']);

        $contractChannel = json_decode($this->getGuzzleClient(array(), 'contracts/channels')->getBody()->getContents())->contracts;
        $data['contract']['channel'] =  array();
        foreach($contractChannel as $contract){
            if (is_null($contract->end_date)) {
                $validityDate = $contract->start_date;
            }else {
                $validityDate = $contract->start_date.' - '.$contract->end_date;
            }
            $data['contract']['channel'][$contract->id] = $contract->name.' ( '.$validityDate.' )';
        }
        asort($data['contract']['hubwire']);
        $data['contracts'] = array();
        $data['contractType'] = null;
        $data['contractSelected'] = null;
        $data['contractSelectedID'] = null;
        $data['month'] = null;
        $data['result']['status'] = false;//dd($data);
        return view('contracts.contract_calculator', $data);
    }

    public function calculate(Request $request)
    {
        $this->validate($request, array(
            'contract_type' => 'required',
            'contract'      => 'required',
            'month'         => 'required',
        ));

        $response = json_decode($this->postGuzzleClient(request()->all(), 'contracts/calculate_fee')->getBody()->getContents());

        //get contract dropdown list
        $contractHubwire = json_decode($this->getGuzzleClient(array(), 'contracts')->getBody()->getContents())->contracts;
        $data['contract']['hubwire'] =  array();
        foreach($contractHubwire as $contract){
            if ($request->contract_type == 'Hubwire Fee' && $request->contract == $contract->id) {
                $data['contractSelected'] = $contract->name.' ( '.$validityDate.' )';

            }
            if (is_null($contract->end_date)) {
                $validityDate = $contract->start_date;
            }else {
                $validityDate = $contract->start_date.' - '.$contract->end_date;
            }
            $data['contract']['hubwire'][$contract->id] = $contract->name.' ( '.$validityDate.' )';
        }
        asort($data['contract']['hubwire']);
        if ($request->contract_type == 'Hubwire Fee') {
            $data['contracts'] = $data['contract']['hubwire'];
        }

        $contractChannel = json_decode($this->getGuzzleClient(array(), 'contracts/channels')->getBody()->getContents())->contracts;
        $data['contract']['channel'] =  array();
        foreach($contractChannel as $contract){
            if ($request->contract_type == 'Channel Fee' && $request->contract == $contract->id) {
                $data['contractSelected'] = $contract->name.' ( '.$validityDate.' )';
            }
            if (is_null($contract->end_date)) {
                $validityDate = $contract->start_date;
            }else {
                $validityDate = $contract->start_date.' - '.$contract->end_date;
            }
            $data['contract']['channel'][$contract->id] = $contract->name.' ( '.$validityDate.' )';
        }
        asort($data['contract']['channel']);
        if ($request->contract_type == 'Channel Fee') {
            $data['contracts'] = $data['contract']['channel'];
        }
        
        
        $data['contractType'] = [$request->contract_type => $request->contract_type];
        $data['contractSelectedID'] = [$request->contract => $request->contract];
        $data['month'] = $request->month;
        $data['result']['status']= true;
        $data['result']['totalOrderCount']= $response->totalOrder;
        $data['result']['totalOrderItem']= $response->totalItem;
        $data['result']['totalSalesAmount']= "RM ".number_format($response->totalSales, 2);
        $data['result']['totalListingAmount']= "RM ".number_format($response->totalListing, 2);
        $data['result']['totalRetailsAmount']= "RM ".number_format($response->totalRetails, 2);

        if ($response->fee->type == 'hubwireFee') {
            $data['result']['channel']= false;
            
            $data['result']['inbound']= $response->inbound;
            $data['result']['outbound']= $response->outbound;
            $data['result']['storage']= $response->storage;
            $data['result']['return']= $response->return;
            $data['result']['shipped']= $response->shipped;

            $data['result']['inbound_fee']= "RM ".number_format($response->inbound_fee,2);
            $data['result']['outbound_fee']= "RM ".number_format($response->outbound_fee,2);
            $data['result']['storage_fee']= "RM ".number_format($response->storage_fee,2);
            $data['result']['return_fee']= "RM ".number_format($response->return_fee,2);
            $data['result']['shipped_fee']= "RM ".number_format($response->shipped_fee,2);
            
            $total_hw_fee = floatval($response->fee->amount)+floatval($response->inbound_fee)+floatval($response->outbound_fee)+floatval($response->storage_fee)+floatval($response->return_fee)+floatval($response->shipped_fee);
            $data['result']['totalFee']= "RM ".number_format($total_hw_fee, 2);
            $contract = json_decode($this->getGuzzleClient(array(), 'contracts/'.$request->contract)->getBody()->getContents());

            $data['result']['inbound_rate'] = number_format($contract->inbound_fee,2);
            $data['result']['outbound_rate'] = number_format($contract->outbound_fee,2);
            $data['result']['storage_rate'] = number_format($contract->storage_fee,2);
            $data['result']['return_rate'] = number_format($contract->return_fee,2);
            $data['result']['shipped_rate'] = number_format($contract->shipped_fee,2);

            $rules = $contract->contract_rules;
        }elseif ($response->fee->type == 'channelFee') {
            $data['result']['channel']= true;
            $data['result']['channelName'] = ($response->fee->channelName);
            $data['result']['totalFee']= "RM ".number_format($response->fee->amount, 2);
            $contract = json_decode($this->getGuzzleClient(array(), 'contracts/channels/'.$request->contract)->getBody()->getContents());
            $rules = $contract->channel_contract_rules;

        }else{
            $data['result']['channelName'] = '';
            $data['result']['channel']= ($request->contract_type == 'Channel Fee') ? true:false;
            $data['result']['totalFee']= "RM 0.00";
            $rules = array();
            $contract = null;
        }
        $data['result']['endDate'] = $endOfMonth = Carbon::createFromFormat('M-Y', $request->month)->endOfMonth()->toDateString();//dd($contract);
        $data['result']['guarantee']= !empty($contract) ? (is_null($contract->guarantee)? 'Not Applicable' : 'RM '.$contract->guarantee) : '';
        $data['result']['min_guarantee']= !empty($contract) ? ($contract->min_guarantee ? 'Charge if amount bigger than min guarantee' : 'Charge on top of min guarantee') : '';
        $data['result']['item']= $response->itemId;

        foreach ($rules as $key => $rule) {
            $data['result']['rules'][$key] = '';
            if ($rule->type == 'Fixed Rate') {
                $data['result']['rules'][$key] .= $rule->type.' RM'.number_format($rule->type_amount, 2);
                if ($rule->base != 'Not Applicable') {
                    if ($rule->base == 'Order Count' || $rule->base == 'Order Item Count') {
                        $data['result']['rules'][$key] .= ' by '.$rule->base;
                    }else {
                        $data['result']['rules'][$key] .= ' where '.$rule->base;
                    }
                }
            }elseif ($rule->type == 'Percentage') {
                $data['result']['rules'][$key] .= $rule->type_amount.'%';
                if ($rule->base != 'Not Applicable') {
                    $data['result']['rules'][$key] .= ' of '.$rule->base;
                }
            }
            if ($rule->operand == 'Between') {
                $data['result']['rules'][$key] .= ' '.$rule->operand.' '.$rule->min_amount.' and '.$rule->max_amount;
            }elseif($rule->operand == 'Above' || $rule->operand == 'Difference'){
                $data['result']['rules'][$key] .= ' '.$rule->operand.' '.$rule->min_amount;
            }
        }

        return view('contracts.contract_calculator', $data);
    }

    public function exportFeeReport(Request $request)
    {
        $data['items'] = json_decode($request->input('data'), true);
        if (empty($data['items'])) {
            flash()->error('No items sales in the month selected.');
            //get contract dropdown list
            $contractHubwire = json_decode($this->getGuzzleClient(array(), 'contracts')->getBody()->getContents())->contracts;
            $data['contract']['hubwire'] =  array();
            foreach($contractHubwire as $contract){
                if (is_null($contract->end_date)) {
                    $validityDate = $contract->start_date;
                }else {
                    $validityDate = $contract->start_date.' - '.$contract->end_date;
                }
                $data['contract']['hubwire'][] = $contract->name.' ( '.$validityDate.' )';
            }
            asort($data['contract']['hubwire']);

            $contractChannel = json_decode($this->getGuzzleClient(array(), 'contracts/channels')->getBody()->getContents())->contracts;
            $data['contract']['channel'] =  array();
            foreach($contractChannel as $contract){
                if (is_null($contract->end_date)) {
                    $validityDate = $contract->start_date;
                }else {
                    $validityDate = $contract->start_date.' - '.$contract->end_date;
                }
                $data['contract']['channel'][$contract->id] = $contract->name.' ( '.$validityDate.' )';
            }
            asort($data['contract']['channel']);
            
            $data['contracts'] = array();
            $data['contractType'] = null;
            $data['contractSelected'] = null;
            $data['contractSelectedID'] = null;
            $data['month'] = null;
            $data['result']['status'] = false;
            return view('contracts.contract_calculator', $data);
        }
        $data['endDate'] = json_decode($request->input('endDate'), true);
        $data['type'] = (json_decode($request->input('type'), true))? 'channelFee':'hubwireFee';
        $response = json_decode($this->postGuzzleClient($data, 'contracts/exportFeeReport')->getBody()->getContents());
        //dd($response->mainData);

        $data = json_decode($response->mainData, true);
        $endDate = json_decode($request->input('endDate'), true);
        $ymdNow = Carbon::now()->format('YmdHis');
        $filename = 'sample_'.$response->fileNameDetails->merchantSlug.'_'.$response->fileNameDetails->brandName.'_'.$response->fileNameDetails->ym.'_'.$ymdNow;

        $excel = Excel::create($filename, function($excel) use($data, $endDate, $ymdNow) {
            $excel->sheet('Master List', function($sheet) use($data, $endDate, $ymdNow) {
                $fileNameDate = Carbon::createFromFormat('Y-m-d', $endDate)->format('F Y');
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->prependRow( array('') );
                $sheet->prependRow(
                            array('Sample Report '.$fileNameDate)
                        );
            });
            $excel->setActiveSheetIndex(0);
        })->download('csv');
    }
}
