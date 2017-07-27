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

class ChannelContractsController extends Controller
{
    use GuzzleClient;

    protected $admin;
    protected $feeTypes;
    protected $feeBases;
    protected $feeOperands;

    public function __construct()
    {
        $this->admin = \Auth::user();
        $this->middleware('permission:view.channelcontract', ['only' => ['index']]);
        $this->middleware('permission:edit.channelcontract', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.channelcontract', ['only' => ['create', 'store', 'duplicate']]);
        $this->middleware('permission:delete.channelcontract', ['only' => ['destroy']]);
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
    public function index($channel_id = null)
    {
        $data = array();
        $data['channel_id'] = $channel_id;
        $data['user'] = $this->admin;
        return view('contracts.channel.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($channel_id = null)
    {   
        $this->initializeDdData();
        $data['merchants']      = array();
        $data['feeTypes']       = $this->feeTypes;
        $data['feeBases']       = $this->feeBases;
        $data['feeOperands']    = $this->feeOperands;
        $data['categories']     = array();

        $categories = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;

        if(empty($channel_id)){
            $channels = json_decode($this->getGuzzleClient(request()->all(), 'channels/channel')->getBody()->getContents());

            foreach ($channels as $channel) {
                if($channel->status == 'Active'){
                    $data['channels']['Active'][$channel->id] = $channel->name;
                }elseif($channel->status == 'Inactive'){
                    $data['channels']['Inactive'][$channel->id] = $channel->name;
                }
            }
        }else{
            $channel = json_decode($this->getGuzzleClient(request()->all(), 'channels/channel/'.$channel_id)->getBody()->getContents());
            if($channel->status == 'Active'){
                $data['channels']['Active'][$channel->id] = $channel->name;
            }elseif($channel->status == 'Inactive'){
                $data['channels']['Active'][$channel->id] = $channel->name;
            }
        }

        foreach($categories as $category){
            $data['categories'][$category->id] = $category->full_name;
        }

        return view('contracts.channel.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $channel_id = null)
    {
        $rules = array(
            'name'                       => 'required',
            'channel'                    => 'required|integer|min:1',
            'merchant'                   => 'required|integer|min:1',
            'brand'                      => 'required|integer|min:1',
            'start_date'                 => 'required|date_format:Y-m-d',
            'end_date'                   => 'sometimes|date_format:Y-m-d|after:start_date',
            'minimum_guarantee'          => 'required',
            'minimum_guarantee_amount'   => 'required_if:minimum_guarantee,"applicable"|integer|min:0.01',
            'guarantee-charge'           => 'required',
        );

        $v = \Validator::make($request->except('_token'), $rules);

        if($v->fails()){
            // \Log::info(print_r($v->messages(), true));
            if(!empty($request->input('channel'))){
                // get merchants
                $merchantDropdown = array();
                $merchants = json_decode($this->getGuzzleClient(request()->all(), 'admin/merchants/'.$request->input('channel').'/byChannel')->getBody()->getContents());
                foreach($merchants as $merchant){
                    $merchantDropdown[$merchant->id] = $merchant->name;
                }

                // flash data to session
                $request->session()->flash('merchants', $merchantDropdown);
            }

            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
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
            'channel_id'    => $request->input('channel'),
            'brand_id'      => $request->input('brand'),
            'merchant_id'   => $request->input('merchant'),
            'start_date'    => $request->input('start_date'),
            'end_date'      => ($request->input('end_date') != '')?$request->input('end_date'):NULL,
            'guarantee'     => $request->input('minimum_guarantee_amount', NULL),
            'min_guarantee' => $request->input('guarantee-charge'),
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
                    'products'      => (isset($feeInfo['product'])) ? $feeInfo['product'] : array(),
                    'categories'    => (isset($feeInfo['category'])) ? $feeInfo['category'] : array(),
                );
                $postData['rule'][] = $postRule;
            }
        }

        // dd($postData);

        $response = json_decode($this->postGuzzleClient($postData, 'contracts/channels')->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Channel contract has been successfully created.';

            flash()->success($message);

            
            return redirect()->route('contracts.channels.index');
        }else{
            if(!empty($request->input('channel'))){
                // get merchants
                $merchantDropdown = array();
                $merchants = json_decode($this->getGuzzleClient(request()->all(), 'admin/merchants/'.$request->input('channel').'/byChannel')->getBody()->getContents());
                foreach($merchants as $merchant){
                    $merchantDropdown[$merchant->id] = $merchant->name;
                }

                // flash data to session
                $request->session()->flash('merchants', $merchantDropdown);
            }

            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
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

        //return back()->withInput(); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->initializeDdData();
        $response = json_decode($this->getGuzzleClient(array(), 'contracts/channels/'.$id)->getBody()->getContents());
        // dd($response);
        $data = array();
        $data['contract']       = $response;
        $data['feeTypes']       = $this->feeTypes;
        $data['feeBases']       = $this->feeBases;
        $data['feeOperands']    = $this->feeOperands;
        $data['categories']     = array();
        $data['user']           = $this->admin;

        $categories = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;
        $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$response->brand_id.'/byBrand')->getBody()->getContents())->products;

        foreach($products as $product){
            $data['products'][$product->id] = $product->name;
        }
        
        foreach($categories as $category){
            $data['categories'][$category->id] = $category->full_name;
        }

        return view('contracts.channel.show', $data);
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
        $response = json_decode($this->getGuzzleClient(array(), 'contracts/channels/'.$id)->getBody()->getContents());
        // dd($response);
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
        $channels = json_decode($this->getGuzzleClient(request()->all(), 'channels/channel')->getBody()->getContents());
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

        foreach($channels as $channel){
            if($channel->status == 'Active'){
                $data['channels']['Active'][$channel->id] = $channel->name;
            }elseif($channel->status == 'Inactive'){
                $data['channels']['Inactive'][$channel->id] = $channel->name;
            }
        }

        foreach($brands as $brand){
            if($brand->active){
                $data['brands']['Active'][$brand->id] = $brand->name;
            }else{
                $data['brands']['Inactive'][$brand->id] = $brand->name;
            }
        }

        foreach($products as $product){
            $data['products'][$product->id] = $product->name;
        }
        
        foreach($categories as $category){
            $data['categories'][$category->id] = $category->full_name;
        }
        // dd($data);

        return view('contracts.channel.edit', $data);
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
        // dd($request->all());
        $rules = array(
            'name'                       => 'required',
            'start_date'                 => 'required|date_format:Y-m-d',
            'end_date'                   => 'sometimes|date_format:Y-m-d|after:start_date',
            'merchant'                   => 'required|integer|min:1',
            'brand'                      => 'required|integer|min:1',
            'minimum_guarantee'          => 'required',
            'minimum_guarantee_amount'   => 'required_if:minimum_guarantee,"applicable"|integer|min:0.01',
            'guarantee-charge'           => 'required',
        );

        $v = \Validator::make($request->except('_token'), $rules);

        if($v->fails()){
            if(!empty($request->input('channel'))){
                // get merchants
                $merchantDropdown = array();
                $merchants = json_decode($this->getGuzzleClient(request()->all(), 'admin/merchants/'.$request->input('channel').'/byChannel')->getBody()->getContents());
                foreach($merchants as $merchant){
                    $merchantDropdown[$merchant->id] = $merchant->name;
                }

                // flash data to session
                $request->session()->flash('merchants', $merchantDropdown);
            }

            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
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
            'channel_id'    => $request->input('channel'),
            'brand_id'      => $request->input('brand'),
            'merchant_id'   => $request->input('merchant'),
            'start_date'    => $request->input('start_date'),
            'end_date'      => ($request->input('end_date') != '')?$request->input('end_date'):NULL,
            'guarantee'     => $request->input('minimum_guarantee_amount', NULL),
            'min_guarantee' => $request->input('guarantee-charge'),
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
                    'products'      => (isset($feeInfo['product'])) ? $feeInfo['product'] : array(),
                    'categories'    => (isset($feeInfo['category'])) ? $feeInfo['category'] : array(),
                );
                $postData['rule'][] = $postRule;
            }
        }

        $response = json_decode($this->putGuzzleClient($postData, 'contracts/channels/'.$id)->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Contract has been successfully updated.';

            flash()->success($message);

            return back();
            // return redirect()->route('admin.channels.index');
        }else{
            if(!empty($request->input('channel'))){
                // get merchants
                $merchantDropdown = array();
                $merchants = json_decode($this->getGuzzleClient(request()->all(), 'admin/merchants/'.$request->input('channel').'/byChannel')->getBody()->getContents());
                foreach($merchants as $merchant){
                    $merchantDropdown[$merchant->id] = $merchant->name;
                }

                // flash data to session
                $request->session()->flash('merchants', $merchantDropdown);
            }

            if(!empty($request->input('merchant'))){
                // get brands
                $brandsDropdown = array();
                $brands = json_decode($this->getGuzzleClient(array(), 'admin/brands/'.$request->input('merchant').'/byMerchant')->getBody()->getContents());
                foreach($brands as $brand){
                    $brandsDropdown[$brand->id] = $brand->name;
                }

                // flash data to session
                $request->session()->flash('brands', $brandsDropdown);
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

                flash()->error('Unable to update channel contract due to overlapping validity period with existing channel contracts. Click <a href="#" style="color: #3c8dbc;" data-toggle="modal" data-target="#dateEditorModal">here</a> to resolve.' );
            }else{
                flash()->error('An error has occurred while updating channel contract.');
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
        $response = json_decode($this->deleteGuzzleClient(null,'contracts/channels/'.$id)->getBody()->getContents());

        if(empty($response->error)) {
            flash()->success('Channel contract #' . $id . ' has been successfully deleted.');
            return redirect()->route('contracts.channels.index');
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
        $response = json_decode($this->postGuzzleClient(array(),'contracts/channels/'.$id.'/duplicate')->getBody()->getContents());

        if(empty($response->error)){
            $msg = 'Contract ID '.$id.' ['.$response->contract_index->name.'] has been duplicated successfully. <b>Please enter the start and end date of the validity period.</b>';
            flash()->success($msg);

            return redirect()->route('contracts.channels.edit', $response->contract_index->id);
        }else{
            $msg = 'An error has occurred while duplicating contract, please try again later.';
            flash()->error($msg);

            return redirect()->back();
        }
    }

    public function getTableData(Request $request){
        $channelId = $request->input('channel_id');
        $data = array();
        $contracts = json_decode($this->getGuzzleClient(array(), 'contracts/channels')->getBody()->getContents())->contracts;\Log::info(print_r($contracts, true));

        foreach($contracts as $contract){
            if(!empty($channelId)){
                if($contract->channel_id != $channelId){
                    continue;
                }
            }
            $actions = array();

            if($this->admin->can('view.channelcontract')){
                if ($this->admin->is('channelmanager')){
                    $actions[] = '<a href="'.route('byChannel.contracts.channels.show', [$channelId, $contract->id]).'">View</a>';
                }else{
                    $actions[] = '<a href="'.route('contracts.channels.show', [$contract->id]).'">View</a>';
                }
            }

            if($this->admin->can('edit.channelcontract')){
                $actions[] = '<a href="'.route('contracts.channels.edit', [$contract->id]).'">Edit</a>';
            }

            if($this->admin->can('create.channelcontract')){
                $actions[] = '<a href="'.route('contracts.channels.duplicate', [$contract->id]).'">Duplicate</a>';
            }

            if($this->admin->can('delete.channelcontract')){
                $actions[] = '<form action="'.route('contracts.channels.destroy', [$contract->id]).'" method="POST"><button type="button"  class="btn btn-link no-padding btn-delete-contract">Delete</button><input type="hidden" name="_method" value="DELETE"></form>';
            }

            $data[] = array(
                'contract_id'   => $contract->id,
                'channel'       => $contract->channel->name,
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

            $response = json_decode($this->postGuzzleClient($postData,'contracts/channels/'.$id.'/update-dates')->getBody()->getContents());

            if(!empty($response->error)){
                if(count($response->duplicate) > 0){
                    $dupContractLinks = array();
                    foreach($response->duplicate as $dupId){
                        $dupContractLinks[] = '<a href="' . route('contracts.channels.edit', $dupId) . '" target="_blank">'.$dupId.'</a>';
                    }
                    $return['errors'][] = 'Selected date(s) clashed with the following channel contracts: ' . implode(', ', $dupContractLinks);
                }else{
                    $return['errors'][] = 'An error has occured while updating date(s), please try again later.';
                }
            }else{
                $return['success'] = true;
            }
        }

        return json_encode($return);
    }

    public function channelShow($channelId, $id)
    {
        $this->initializeDdData();
        $response = json_decode($this->getGuzzleClient(array(), 'contracts/channels/'.$id)->getBody()->getContents());
        // dd($response);
        $data = array();
        $data['contract']       = $response;
        $data['feeTypes']       = $this->feeTypes;
        $data['feeBases']       = $this->feeBases;
        $data['feeOperands']    = $this->feeOperands;
        $data['categories']     = array();
        $data['user']           = $this->admin;

        $categories = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;
        $products = json_decode($this->getGuzzleClient(null, 'admin/products/'.$response->brand_id.'/byBrand')->getBody()->getContents())->products;

        foreach($products as $product){
            $data['products'][$product->id] = $product->name;
        }
        
        foreach($categories as $category){
            $data['categories'][$category->id] = $category->full_name;
        }

        return view('contracts.channel.show', $data);
    }
}
