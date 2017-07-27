<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\GuzzleClient;
use App\Models\User;
use Form;
use Input;
use Config;

class StockTransferController extends Controller
{
    use GuzzleClient;

    protected $admin;
    protected $users;
    protected $channels;
    protected $merchants;
    protected $merchantList;
    protected $channelList;
    protected $userList;

    public function __construct()
    {
        $this->middleware('permission:view.stocktransfer', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.stocktransfer', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.stocktransfer', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.stocktransfer', ['only' => ['destroy']]);
        $this->admin = \Auth::user();
        
        $users = User::get();
        $nonActiveUsers = array();
        $userByStatus = array();
        foreach ($users as $user) {
            if (is_null($user->deleted_at)){
                $this->users[$user->id] = $user->first_name . " " . $user->last_name;
                if(strcmp($user->status,"Active")!=0){
                    $nonActiveUsers[$user->status][$user->id] = $user->first_name . " " . $user->last_name;
                }else{
                    $userByStatus[$user->status][$user->id] = $user->first_name . " " . $user->last_name;
                }
            }
        }
        $this->userList = array_merge($userByStatus,$nonActiveUsers);

        $channels = json_decode($this->getGuzzleClient(array(), 'channels/channel')->getBody()->getContents());
        $nonActiveChannels = array();
        $channelsByType = array();
        foreach($channels as $channel) {
            $this->channels[$channel->id] = $channel->name;
            if(strcmp($channel->status,"Active")!=0){
                $nonActiveChannels[$channel->status][$channel->id] = $channel->name;
            }else{
                $channelsByType[$channel->channel_type->name][$channel->id] = $channel->name;
            }
        }
        $this->channelList = array_merge($channelsByType,$nonActiveChannels);

        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants')->getBody()->getContents())->merchants;
        $nonActive = array();
        $merchantByStatus = array();
        foreach ($merchants as $merchant) {
            $this->merchants[$merchant->id] = $merchant->name;
            //$merchantList[$merchant->id] = $merchant->name;
            if(strcmp($merchant->status,"Active")!=0){
                $nonActive[$merchant->status][$merchant->id] = $merchant->name;
            }else{
                $merchantByStatus[$merchant->status][$merchant->id] = $merchant->name;
            }
        }
        $this->merchantList = array_merge($merchantByStatus,$nonActive);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $channels = json_decode($this->getGuzzleClient(array(), 'channels/channel')->getBody()->getContents());
        foreach($channels as $channel) {
            $data['channels'][$channel->name] = $channel->name;
        }

        $data['statuses'] = array("Draft" => "Draft", "In Transit" => "In Transit", "Received" => "Received");
        $data['admin'] = $this->admin;
        $data['channel_id'] = request()->route('channel_id', null);
        return view('product-management.stocktransfer.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $doSelection = null;
        if($request->get('m')){
            $merchantId    = $request->get('m');
            $data['merchant_id'] = $merchantId;
        }else{
            $merchantId = $this->admin->merchant_id;
            $data['merchant_id'] = null;
        }

        if($request->get('p') && $request->get('c')){
            $doSelection = 1;
            // get all sku based on product and channel
            $chnlSkus = $this->getChannelSkus($request->get('c'), $request->get('p'), false);
            $product = $chnlSkus['channel_sku'];
            $tagsString = '';
            foreach($product->tags as $tag){
                $tagsString.=', '.$tag->value;
            }
            foreach($product->sku_in_channel as $chnlSku){
                if($chnlSku->channel_id == $request->get('c') && $chnlSku->channel_sku_quantity > 0){
                    $optsString = '';
                    foreach($chnlSku->sku_options as $option){
                        $optsString.=', <b>'.$option->option_name.'</b>: '.$option->option_value;
                    }
                    // only get chnl sku with the matching channel ID
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['prodid']         =   $product->id;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['options']        =   substr($optsString,2);
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['status']         =   $chnlSku->channel_sku_active;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['sale_price']     =   $chnlSku->channel_sku_promo_price;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['price']          =   $chnlSku->channel_sku_price;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['brand_prefix']   =   $product->brand->prefix;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['pname']          =   $product->name;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['skuid']          =   $chnlSku->sku_id;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['tags']           =   substr($tagsString,2);
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['hubwire_sku']    =   $chnlSku->sku->hubwire_sku;
                    $data['chnlSkus'][$chnlSku->channel_sku_id]['quantity']       =   $chnlSku->channel_sku_quantity;
                }
            }
        }
        if($request->get('c')){
            $data['channel_id'] = $request->get('c');
        }else{
            $data['channel_id'] = null;
        }

        // get channel type
        $channelTypeResponse = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/')->getBody()->getContents());
        $channelTypes = array();
        foreach($channelTypeResponse as $channelType){
            $channelTypes[$channelType->id] = $channelType->name;
        }

        $data['merchants'] = $this->merchantList;
        $channels = array();
        if (!is_null($merchantId)){
            $channelByStatus = array();
            $nonActiveChannels = array();

            $channels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$merchantId)->getBody()->getContents());
            foreach($channels->channels as $channel)
            {
                //$channelList[$channel->id] = $channel->name;
                if(strcmp($channel->status,"Active")!=0){
                    $nonActiveChannels[$channel->status][$channel->id] = $channel->name;
                }else{
                    $channelByStatus[$channelTypes[$channel->channel_type_id]][$channel->id] = $channel->name;
                }
            }
            $data['channels'] = array_merge($channelByStatus,$nonActiveChannels);
        }
        else
            $data['channels'] = $this->channelList;   
        
        $data['templateUrl'] = $this->getProductSheetTemplate('stock-out');
        $data['users'] = $this->userList;
        $data['doType'] = Config::get('globals.stock_transfer.do_type');
        $data['channelTypes'] = $channelTypes;
        $data['doSelection'] = $doSelection;

        return view('product-management.stocktransfer.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages['originating_channel_id.different'] = "The originating channel and target channel must be different."; 
        $messages['target_channel_id.different'] = "The target channel and originating channel must be different."; 
        //
        $this->validate($request, array(
            'do_type'=>'required|numeric|min:0',
            'merchant_id'=>'required|numeric|min:1',
            'originating_channel_id'=>'required_unless:do_type,2|numeric|min:1|different:target_channel_id',
            'target_channel_id'=>'required|numeric|min:1|different:originating_channel_id',
            'do_status' => 'required|numeric',
            'pic'=>'required|numeric|min:1',
            'batch_id'=>'required_if:do_type,0|numeric|min:1',
            'transport_co' => 'sometimes',
            'lorry_no' => 'sometimes',
            'driver_name' => 'sometimes',   
            'driver_id' => 'sometimes',
            'remarks' => 'sometimes',
        ), $messages);
        
        $response = json_decode($this->postGuzzleClient($request->except('_token'), 'admin/stock_transfer')->getBody()->getContents());
        
        if (isset($response->success) && $response->success) {
            flash()->success('The stock transfer was successfully created.');
            return redirect()->route('products.stock_transfer.index');
        } 

        else if (isset($response->code) && $response->code==422) {
            if (isset($response->error->duplicate))
                flash()->error($response->error->duplicate);
            else if (isset($response->error->o_channel)) {
                flash()->error($response->error->o_channel);
            }
            else if (isset($response->error->t_channel)) {
                flash()->error($response->error->t_channel);
            }
            else
                flash()->error("Quantity is required, cannot be greater than available quantity and must be greater than 0.");
        }
        
        else {
            flash()->error("An error has occurred. Please try again.");
            \Log::info(print_r($response, true));
        }
            
        return back()->withInput()->withErrors($response->error);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = request()->route('stock_transfer');
        // grab stock transfer from API
        $response = json_decode($this->getGuzzleClient(array(), 'admin/stock_transfer/'.$id)->getBody()->getContents());

        $stockTransfer = $response->stockTransfer;
        $stockTransfer->person_incharge = isset($this->users[$stockTransfer->person_incharge])?$this->users[$stockTransfer->person_incharge] : '';
        $data['stockTransfer'] = $stockTransfer;
        $data['id'] = $id;

        // items
        $items = $response->items;
        foreach($items as $item) {
            $options="";
            foreach($item->channel_sku->sku_options as $option){
                $options.=(!empty($options))?", <b>".$option->option_name."</b>:".$option->option_value:'<b>'.$option->option_name."</b>:".$option->option_value;
            }
            $tagsStr = '';
            if(!empty($item->ref->product->tags)){
                foreach ($item->ref->product->tags as $tag) {
                    $tagsStr .=(!empty($tagsStr))?', '.$tag->value:$tag->value;
                }
            }
            $item->options = $options;
            $item->tags = $tagsStr; 
        }
        $data['items'] = $response->items;
        $data['admin'] = $this->admin;
        return view('product-management.stocktransfer.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // grab stock transfer from API
        $response = json_decode($this->getGuzzleClient(array(), 'admin/stock_transfer/'.$id)->getBody()->getContents());

        // get channel type
        $channelTypeResponse = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/')->getBody()->getContents());
        $channelTypes = array();
        foreach($channelTypeResponse as $channelType){
            $channelTypes[$channelType->id] = $channelType->name;
        }
        
        // if not a draft, redirect to show page
        if ($response->stockTransfer->status!= 0) {
            return redirect()->route('products.stock_transfer.show', $id);
        }
        
        $data['stockTransfer'] = $response->stockTransfer;
        $data['merchants'] = $this->merchants;
        $data['channels'] = $this->channels;
        $data['id'] = $id;
        $data['doType'] = Config::get('globals.stock_transfer.do_type');  
        $data['users'] = $this->users;
        $data['channelTypes'] = $channelTypes;
        
        // items
        $items = $response->items;
        foreach($items as $item) {
            $options="";
            foreach($item->channel_sku->sku_options as $option){
                $options.=(!empty($options))?", <b>".$option->option_name."</b>: ".$option->option_value:'<b>'.$option->option_name."</b>: ".$option->option_value;
            }
            $tagsStr = '';
            if(!empty($item->ref->product->tags)){
                foreach ($item->ref->product->tags as $tag) {
                    $tagsStr .=(!empty($tagsStr))?', '.$tag->value:$tag->value;
                }
            }
            $item->options = $options;
            $item->tags = $tagsStr;
        }
        $data['items'] = $response->items;
        
        return view('product-management.stocktransfer.edit', $data);
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
        $messages['originating_channel_id.different'] = "The originating channel and target channel must be different."; 
        $messages['target_channel_id.different'] = "The target channel and originating channel must be different."; 

        $this->validate($request, array(
            'do_type'=>'required|numeric|min:0',
            'originating_channel_id'=>'required|numeric|min:1|different:target_channel_id',
            'target_channel_id'=>'required|numeric|min:1|different:originating_channel_id',
            'do_status' => 'required|numeric|min:0',
            'pic'=>'required|numeric|min:1',
            'batch_id'=>'required_if:do_type,0|numeric|min:1',
            'transport_co' => 'sometimes',
            'lorry_no' => 'sometimes',
            'driver_name' => 'sometimes',
            'driver_id' => 'sometimes',
            'remarks' => 'sometimes',
        ), $messages);

        $inputs = $request->except(['_method', '_token']);   
        $response = json_decode($this->putGuzzleClient($inputs, 'admin/stock_transfer/'.$id)->getBody()->getContents());
        
        if (isset($response->success) && $response->success) {
            flash()->success('The stock transfer was successfully updated.');
            return redirect()->route('products.stock_transfer.edit', $id);
        } 
        else if (isset($response->code) && $response->code==422) {
            if (isset($response->error->duplicate))
                flash()->error($response->error->duplicate);
            else if (isset($response->error->o_channel)) {
                flash()->error($response->error->o_channel);
            }
            else if (isset($response->error->t_channel)) {
                flash()->error($response->error->t_channel);
            }
            else
                flash()->error("Quantity is required, cannot be greater than available quantity and must be greater than 0.");
        }

        else 
            flash()->error("An error has occurred. Please try again.");
            
        return back()->withInput();
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
        $response = json_decode($this->deleteGuzzleClient([], 'admin/stock_transfer/'.$id)->getBody()->getContents());
        if (isset($response->code) && $response->code==422) {
            flash()->error($response->error->status[0]);
        } else {
            flash()->success('The stock transfer was successfully deleted.');
        }
        
        return redirect()->route('products.stock_transfer.index');
    }

    public function getTableData()
    {
        $data = array();
        $response = json_decode($this->getGuzzleClient(request()->all(), 'admin/stock_transfer')->getBody()->getContents());
        foreach ($response->stockTransfers as $stockTransfer) {      
            
            if($this->admin->is('channelmanager'))
                $viewUrl = route('byChannel.products.stock_transfer.show', ['channel_id'=>request()->get('channel_id', null), 'stock_transfer_id'=>$stockTransfer->id]);
            else
                $viewUrl = route('products.stock_transfer.show', [$stockTransfer->id]);

            $viewButton = '<a href="'.$viewUrl.'" class="btn btn-link stock-transfer">View</a>';
            $dataArray = ["id" => $stockTransfer->id,
                      "original_channel" => isset($this->channels[$stockTransfer->originating_channel_id])?$this->channels[$stockTransfer->originating_channel_id]:'',
                      "target_channel" => isset($this->channels[$stockTransfer->target_channel_id])?$this->channels[$stockTransfer->target_channel_id]:'',
                      "created_at" => $stockTransfer->created_at,
                      "received_at" => $stockTransfer->receive_at,
                      "pic"  => isset($this->users[$stockTransfer->person_incharge])?$this->users[$stockTransfer->person_incharge]:'',
                      "merchant" => isset($this->merchants[$stockTransfer->merchant_id])?$this->merchants[$stockTransfer->merchant_id]:'',
                      "status" => Config::get('globals.stock_transfer.statuses')[$stockTransfer->status],
                      "action" => $viewButton,
                      "updated_at" => $stockTransfer->updated_at,
                    ]; 
            $data[] = $dataArray;
        }
        return json_encode(array("data" => $data));
    }

    // get procurement batch by id 
    public function getBatchById($batchId, $merchantId, $channelId) {
        $response = json_decode($this->getGuzzleClient(array(), 'admin/procurements/' .$batchId.'/merchant/'.$merchantId.'/channel/'.$channelId.'/search')->getBody()->getContents());
        
        return json_encode($response);
    }

    public function addItemsModal($channelId, $merchantId) {
        $channel = $this->channels[$channelId];
        
        $data['ajax'] = true;
        $data['form_path'] = '';
        $data['wide'] = true;
        $data['request_type'] = 'POST';
        $data['form'] = true;
        $data['title'] = 'Add Item(s) @'.$channel;
        $data['channel_id'] = $channelId;
        $data['merchant_id'] = $merchantId;
        
        if($data['ajax'])
            return view('product-management.stocktransfer.add_items_modal', $data);
    }

    // search function in add items modal 
    public function searchDatatable($channel_id, $merchant_id)
    {   
        $data['start'] = Input::get('start');
        $data['length'] = Input::get('length');
        $data['draw'] = Input::get('draw');
        
        if(Input::has('columns')){
            foreach (Input::get('columns') as $column)
            {
                if($column['search']['value'] !== "")
                $data['columns'][$column['name']] = $column['search']['value'];
            }
        }
        $data['columns']['channel_id'] = $channel_id;
        $data['columns']['merchant_id'] = $merchant_id;
        $data['columns']['hasQuantity'] = true;

        $response = json_decode($this->getGuzzleClient($data, 'admin/inventories')->getBody()->getContents());
        
        $data['draw'] = Input::get('draw');
        $data['recordsTotal'] = $response->total;
        $data['recordsFiltered'] = $response->total;
        $data['data'] = array();

        if ($data['recordsFiltered']>0)
        {
            $index = 0;
            foreach ($response->products as $product)
            {
                $img_path = !empty($product->default_media->media_url)?$this->removeExtensionFromMediaURL($product->default_media->media_url, $product->default_media->media_key).'_':'//placehold.it/';
                $img_path.=$this->getImageWH('sm');
                $product->image = $img_path;
                
                $product->total_quantity = 0;

                if(!empty($product->sku_in_channel)){
                    foreach($product->sku_in_channel as $channel){
                        if ($channel->channel_id!=intval($channel_id)) continue;
                        $product->total_quantity += $channel->channel_sku_quantity;
                    }
                }

                $pdata['product'] = $product;
                $prod = view('product-management.stocktransfer.product',$pdata)->render();
                $data['data'][] = array(
                    '',
                    $prod,
                    '',
                    '',
                    '',
                    json_encode($product),
                );
                
            }
        }

        return json_encode($data);
    }

    // get all channel skus for a given product
    public function getChannelSkus($channelId, $productId, $ajax = true) {
        $data['columns']['channel_id'] = $channelId;
        $data['columns']['product'] = $productId;
        
        $data['channel_sku'] = json_decode($this->getGuzzleClient($data, 'admin/inventories/channelSkus')->getBody()->getContents());
        //dd($data['channel_sku']);
        if (isset($data['channel_sku']->tags)) {
            $tagsString = '';
            foreach ($data['channel_sku']->tags as $tag) {
                $tagsString.=', '.$tag->value;       
            }
            $data['channel_sku']->tag_string = $tagsString;
        }

        foreach($data['channel_sku']->sku_in_channel as $sku) {
            if (isset($sku->sku_options)) {
                $optsString='';
                foreach($sku->sku_options as $options){
                    $optsString.=', <b>'.$options->option_name.'</b>: '.$options->option_value;   
                }
                $sku->opts_string = substr($optsString,2);   
            }
        }
        
        if($ajax){
            $content = view('product-management.stocktransfer.channel_skus',$data);
            return $content;
        }
        else
            return $data;
    }

    public function transfer($id) {

        $response = json_decode($this->postGuzzleClient([], 'admin/stock_transfer/transfer/'.$id)->getBody()->getContents());
        
        if (isset($response->success) && $response->success) {
            flash()->success('The stock transfer was successfully sent.');
            return redirect()->route('products.stock_transfer.index');
        } 

        // if one or more skus does not have enough quantity or origin/target channel no longer tied to merchant
        else if (isset($response->code) && $response->code==422) {
            $message = "The following SKUs could not be transferred due to the following: ";

            foreach ($response->error as $error) {
                $message .= " <br />". $error;
            }
            flash()->error($message);
            return back()->withInput();
        }

        else {
            $message = "An error has occurred.";
            
            flash()->error($message);
            return back()->withInput();
        }
    }

    public function receive($id) {
        $response = json_decode($this->postGuzzleClient([], 'admin/stock_transfer/receive/'.$id)->getBody()->getContents());
        
        if (isset($response->success) && $response->success) {
            flash()->success('The stock transfer was successfully received.');
            return redirect()->route('products.stock_transfer.index');
        } 

        // if target channel is inactive
        else if (isset($response->code) && $response->code==422) {
            $message = $response->error->t_channel;
            flash()->error($message);
            return back()->withInput();
        }

        else {
            $message = "An error has occurred.";
            
            flash()->error($message);
            return back()->withInput();
        }
    }

    public function preprocessCreateAndTransfer(Request $request){
        $response = array();
        if($request->get('targetChannelId')){
            $targetChannelId = $request->get('targetChannelId');
        }
        else{
            $response['success'] = false;
            $response['message'] = 'The target channel ID is absent.';
            return response()->json($response);
        }

        // check if target channel is active
        $targetChannel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$targetChannelId)->getBody()->getContents());

        if(strcmp($targetChannel->status,"Active")!=0){
            $response['success'] = false;
            $response['message'] = 'The target channel is inactive. Please ensure the target channel is active before proceeding.';
        }else{
            $response['success'] = true;
        }

        return response()->json($response);
    }

    public function postUpload($merchant_id)
    {
        if(request()->hasFile('product_sheet')){
            $file = request()->file('product_sheet');
            $extension = $file->getClientOriginalExtension();
            $allowed = array('xls','csv');
            $return = array();
            if(!$file->isValid() || !in_array($extension,$allowed))
            {
                $return['success'] = false;
                $return['error'] = array('messages'=>array('File is invalid! <b>*.'.$extension.'</b>'));
                return json_encode($return);
            }
            $tfile = $file->getRealPath();
            $data = [
                [
                    'name' => 'tfile',
                    'contents' => fopen($tfile,'r')
                ],
                [
                    'name' => 'merchant_id',
                    'contents' => $merchant_id
                ]
            ];
            $response  = json_decode($this->postGuzzleClient($data, 'admin/stock_transfer/process_sku/'.$merchant_id,'multipart')->getBody()->getContents());
            return json_encode($response);
        }
        $return['success'] = false;
        $return['error'] = ["File is empty!"];
        return json_encode($return);
    }

    public function csv_to_array($filename='', $delimiter1='|', $delimiter2= ',')
    {
        ini_set('auto_detect_line_endings', true);
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;
        $header = config('csv.upload');
        $fields = config('csv.create');
        $mydata = array();
        $data = array();
        $mydata['ok'] = true;
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            $test = fgetcsv($handle, 1000, $delimiter1);
            $test = array_filter($test,'strlen');
            if(count($test) != 3 )
            {
                $mydata['messages'][] = 'Invalid template format. Please use the template provided.';
                $mydata['ok'] = false;
                $mydata['count'] = count($test);
                return $mydata;
            }
            //REPLENISHMENT
            if(count($test) == 3)
            {
                $flag = true;
                while (($row = fgetcsv($handle, 1000, $delimiter1)) !== FALSE)
                {
                    $chk = array_filter($row,'strlen');
                    if(empty($chk)){
                        continue;
                    }
                    $data[] = array(
                        'hubwire_sku'=>$row[0],
                        'channel_name'=>$row[1],
                        'quantity'=>$row[2]
                    );
                }
                $mydata['type']     = "stock-out";
            }
            fclose($handle);
            $mydata['items'] = $data;
            return $mydata;
        }
    }

    public function export($id)
    {
        $response = json_decode($this->getGuzzleClient(array(), 'admin/stock_transfer/'.$id.'/manifest')->getBody()->getContents());
        $list = array(
            array(
                trans('product-management.transfer_form_label_system_sku'),
                trans('product-management.transfer_form_label_hw_sku'),
                trans('product-management.transfer_form_csv_merchant'),
                trans('product-management.transfer_form_label_prefix'),
                trans('product-management.transfer_form_label_product'),
                trans('product-management.transfer_form_label_options'),
                trans('product-management.transfer_form_label_tags'),
                trans('product-management.transfer_form_csv_picked'),
                trans('product-management.transfer_form_csv_quantity')
            )
        );

        $items = $response->items;
        foreach($items as $item) {

            array_push($list, [
                        $item->sku_id,
                        $item->hubwire_sku,
                        $item->merchant,
                        $item->brand_prefix,
                        $item->product,
                        $item->options,
                        $item->tags,
                        $item->picked,
                        $item->quantity
                    ]);
        
        }
        $filename  = '/tmp/stock_transfer_export_'.uniqid().'.csv';
        $fp = fopen($filename, 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $this->downloadFile($filename, 'manifest_export_'.$id,'csv');
        unlink($filename);
    }
}
