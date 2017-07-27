<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin\Merchant;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Validator;
use Form;
use Log;
use App\Services\MediaService as MediaService;

class ProductController extends Controller
{
    use GuzzleClient;

    protected $admin;
    protected $statusLabel;

    public function __construct()
    {
        $this->middleware('permission:edit.restock', ['only' => ['edit', 'update', 'updateItem', 'receive']]);
        $this->middleware('permission:create.restock', ['only' => ['createCreate', 'createRestock', 'storeCreate', 'storeRestock']]);
        $this->middleware('permission:delete.restock', ['only' => ['destroy', 'deleteItem']]);
        $this->middleware('permission:view.restock', ['only' => ['indexCreate', 'indexRestock', 'show']]);
        $this->admin = \Auth::user();
        $this->statusLabel = config('globals.product_management_status');
    }

    // Shared functions
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $id = request()->route('batch_id');
        // get purchase batch
        $batch = json_decode($this->getGuzzleClient(array(), 'admin/procurements/'.$id.'/with-trashed')->getBody()->getContents());
        // get merchant
        $merchant = json_decode($this->getGuzzleClient(array(), 'admin/merchants/'.$batch->merchant_id.'/with-trashed')->getBody()->getContents());
        // get supplier
        $supplier = json_decode($this->getGuzzleClient(null,'admin/suppliers/'.$batch->supplier_id.'/with-trashed')->getBody()->getContents());
        // get channel
        $channel = json_decode($this->getGuzzleClient(null,'channels/channel/'.$batch->channel_id.'/with-trashed')->getBody()->getContents());
        $adminList = array();
        // get user
        $user = json_decode($this->getGuzzleClient([], 'admin/users/'.$batch->user_id)->getBody()->getContents());
        $data['id']             =   $id;
        $data['admin']          =   $user->first_name . ' ' . $user->last_name;
        $data['batch_date']     =   $batch->batch_date;
        $data['merchant']       =   $merchant->name;
        $data['supplier']       =   $supplier->name;
        $data['channel']        =   $channel->name;
        $data['batch_remarks']  =   $batch->batch_remarks;
        $data['statusLabel']    =   $this->statusLabel[$batch->batch_status];
        $data['status']         =   $batch->batch_status;
        $data['replenishment']  =   $batch->replenishment;
        $data['products']       =   array();
        // get purchase item list

        foreach($batch->items_with_trashed as $item){
            //dd($item);
            $product = array();
            $product['name']            =   (is_null($item->sku_with_trashed->product_with_trashed->deleted_at))
                                                ?$item->sku_with_trashed->product_with_trashed->name:'[Deleted] '.$item->sku_with_trashed->product_with_trashed->name;
            if($item->sku_with_trashed->sku_supplier_code == '')
                $item->sku_with_trashed->sku_supplier_code = '-';
            $product['supplier_sku']    =   (is_null($item->sku_with_trashed->deleted_at))?$item->sku_with_trashed->sku_supplier_code:'[Deleted] '.$item->sku_with_trashed->sku_supplier_code;
            $product['hw_sku']          =   (is_null($item->sku_with_trashed->deleted_at))?$item->sku_with_trashed->hubwire_sku:$item->sku_with_trashed->hubwire_sku;
            $product['item_quantity']   =   $item->item_quantity;
            $product['unit_cost']       =   number_format((float)$item->item_cost, 2, '.', '');
            $product['unit_price']      =   number_format((float)$item->item_retail_price, 2, '.', '');
            $product['weight']          =   $item->sku_with_trashed->sku_weight;
            $product['tags']            =   '';
            $product['category']        =   isset($item->sku_with_trashed->product_with_trashed->category->full_name)?$item->sku_with_trashed->product_with_trashed->category->full_name:'';
            foreach($item->sku_with_trashed->combinations as $combination){
                if($combination->option_name == 'Size'){
                    $product['size'] = $combination->option_value;
                }elseif($combination->option_name == 'Colour'){
                    $product['color'] = $combination->option_value;
                }
            }
            foreach($item->sku_with_trashed->product_with_trashed->tags as $tag){
                $product['tags'] .= $tag->value . ', ';
            }
            $product['tags'] = rtrim($product['tags'], ", ");
            $data['products'][] = $product;
        }
        //dd($data);
        return view('product-management.show', $data);
    }

    public function receive($id)
    {
        $response = json_decode($this->postGuzzleClient(array(), 'admin/procurements/'.$id.'/receive')->getBody()->getContents());
        if(empty($response->error)){
            $message = 'Successfully received products in batch ID: ' . $id;
            flash()->success($message);
            if($response->replenishment == 0)
                return redirect()->route('products.create.show', $id);
            elseif($response->replenishment == 1)
                return redirect()->route('products.restock.show', $id);
        }else{
            $errorMsgs = new MessageBag((array)$response->error);
            $errorOutput = '';
            foreach($errorMsgs->all() as $errorMsg){
                $errorOutput .= $errorMsg . '<br>';
            }

            flash()->error($errorOutput);
            return back();
        }
    }

    public function edit($batch_id)
    {
        // get purchase batch
        $batch = json_decode($this->getGuzzleClient(array(), 'admin/procurements/'.$batch_id.'/with-trashed')->getBody()->getContents());

        // get merchant
        $merchant = json_decode($this->getGuzzleClient(array(), 'admin/merchants/'.$batch->merchant_id.'/with-trashed')->getBody()->getContents());

        // get channel type
        $channelTypeResponse = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/')->getBody()->getContents());
        $channelTypes = array();
        foreach($channelTypeResponse as $channelType){
            $channelTypes[$channelType->id] = $channelType->name;
            if($channelType->name == 'Warehouse'){
                $chnlTypeId = $channelType->id;
            }
        }

        $channelList = array();
        $nonActiveChannels = array();
        $channelByStatus = array();
        $nonActiveSuppliers = array();
        $supplierByStatus = array();
        $nonActiveUser = array();
        $userByStatus = array();

        if($batch->replenishment == 1){
            // get channel list by merchant
            $merchantChannels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$batch->merchant_id.'/with-trashed')->getBody()->getContents());
            foreach($merchantChannels->channels as $channel)
            {
                //$channelList[$channel->id] = $channel->name;
                if(strcmp($channel->status,"Active")!=0){
                    $nonActiveChannels[$channel->status][$channel->id] = $channel->name;
                }else{
                    $channelByStatus[$channelTypes[$channel->channel_type_id]][$channel->id] = $channel->name;
                }
            }
            $channelList = array_merge($channelByStatus,$nonActiveChannels);
        }else{
            // get channel warehouse list by merchant
            $merchantChannels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$batch->merchant_id.'/channel_type/'.$chnlTypeId)->getBody()->getContents());
            foreach($merchantChannels as $channel)
            {
                //$channelList[$channel->id] = $channel->name;
                if(strcmp($channel->status,"Active")!=0){
                    $nonActiveChannels[$channel->status][$channel->id] = $channel->name;
                }else{
                    $channelByStatus[$channelTypes[$channel->channel_type_id]][$channel->id] = $channel->name;
                }
            }
            $channelList = array_merge($channelByStatus,$nonActiveChannels);
        }

        // get supplier list by merchant
        $merchantSuppliers = json_decode($this->getGuzzleClient(null,'admin/suppliers/'.$batch->merchant_id.'/byMerchant')->getBody()->getContents());

        $merchantList = array();
        $adminList = array();
        $supplierList = array();

        foreach($merchantSuppliers as $supplier)
        {
            if($supplier->active == 0){
                $nonActiveSuppliers['Inactive'][$supplier->id] = $supplier->name;
            }else{
                $supplierByStatus['Active'][$supplier->id] = $supplier->name;
            }
            //$supplierList[$supplier->id] = $supplier->name;
        }
        $supplierList = array_merge($supplierByStatus,$nonActiveSuppliers);
        // get AE list
        $users = User::AE();
        foreach($users as $user)
        {
            // $aeList[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
            if(strcmp($user['status'],"Active")!=0){
                $nonActiveUser[$user['status']][$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
            }else{
                $userByStatus[$user['status']][$user['id']] = $user['first_name'] . ' ' . $user['last_name'];;
            }
        }
        $adminList = array_merge($userByStatus, $nonActiveUser);

        // $data['merchantList']   =   $merchantList;
        // $data['channelTypes']   =   $channelTypes;
        $data['merchant']       =   $merchant->name;
        $data['adminList']      =   $adminList;
        $data['channelList']    =   $channelList;
        $data['supplierList']   =   $supplierList;
        $data['id']             =   $batch_id;
        $data['user_id']        =   $batch->user_id;
        $data['batch_date']     =   $batch->batch_date;
        $data['merchant_id']    =   $batch->merchant_id;
        $data['supplier_id']    =   $batch->supplier_id;
        $data['channel_id']     =   $batch->channel_id;
        $data['batch_remarks']  =   $batch->batch_remarks;
        $data['statusLabel']    =   $this->statusLabel[$batch->batch_status];
        $data['status']         =   $batch->batch_status;
        $data['replenishment']  =   $batch->replenishment;
        if($batch->replenishment == 1)
            $data['type'] = 'restock';
        elseif($batch->replenishment == 0)
            $data['type'] = 'create';
        $data['no_of_items']    =   count($batch->items_with_trashed);
        $data['products']       =   array();
        //dd($batch);
        // get purchase item list

        foreach($batch->items_with_trashed as $item){
            //dd($item);
            $product = array();
            $product['item_id']         =   $item->item_id;
            $product['name']            =   (is_null($item->sku_with_trashed->product_with_trashed->deleted_at))
                                                ?$item->sku_with_trashed->product_with_trashed->name:'[Deleted] '.$item->sku_with_trashed->product_with_trashed->name;
            $product['product_id']      =   $item->sku_with_trashed->product_with_trashed->id;
            $product['sku_id']          =   $item->sku_with_trashed->sku_id;

            if($item->sku_with_trashed->sku_supplier_code == '')
                $item->sku_with_trashed->sku_supplier_code = '-';
            $product['supplier_sku']    =   (is_null($item->sku_with_trashed->deleted_at))?$item->sku_with_trashed->sku_supplier_code:'[Deleted] '.$item->sku_with_trashed->sku_supplier_code;
            $product['hw_sku']          =   (is_null($item->sku_with_trashed->deleted_at))?$item->sku_with_trashed->hubwire_sku:$item->sku_with_trashed->hubwire_sku;
            $product['item_quantity']   =   $item->item_quantity;
            $product['unit_cost']       =   number_format((float)$item->item_cost, 2, '.', '');
            $product['unit_price']      =   number_format((float)$item->item_retail_price, 2, '.', '');
            $product['weight']          =   $item->sku_with_trashed->sku_weight;
            $product['tags']            =   '';
            $product['category']        =   isset($item->sku_with_trashed->product_with_trashed->category->full_name)?$item->sku_with_trashed->product_with_trashed->category->full_name:'';
            foreach($item->sku_with_trashed->combinations as $combination){
                if($combination->option_name == 'Size'){
                    $product['size'] = $combination->option_value;
                }elseif($combination->option_name == 'Colour'){
                    $product['color'] = $combination->option_value;
                }
            }
            foreach($item->sku_with_trashed->product_with_trashed->tags as $tag){
                $product['tags'] .= $tag->value . ', ';
            }
            $product['tags'] = rtrim($product['tags'], ", ");
            $data['products'][] = $product;
        }
        return view('product-management.edit', $data);
    }

    public function update(Request $request, $type, $batch_id)
    {
        $this->validate($request, [
           'merchandiser' => 'required|integer|min:1',
           'batch_date' => 'required|date_format:Y-m-d',
           'channel' => 'required|integer|min:1',
           'supplier' => 'required|integer|min:1',
        ]);
        $postData = array(
                'user_id'              => $request->input('merchandiser'),
                'batch_date'            => $request->input('batch_date'),
            );

        if($request->input('channel')){
            $postData['channel_id'] = $request->input('channel');
        }

        if($request->input('remarks') != ''){
            $postData['batch_remarks'] = $request->input('remarks');
        }
        $updateResponse = json_decode($this->putGuzzleClient($postData, 'admin/procurements/'.$batch_id)->getBody()->getContents());

        if(empty($updateResponse->error)){
            $message = 'Successfully update '.$type.' sheet ID: ' . $batch_id;

            flash()->success($message);
            return redirect()->route('products.'.$type.'.edit', [$batch_id]);
        }else{
            return back()->withInput();
        }
    }

    public function destroy(Request $request, $type, $batch_id)
    {
        $deleteResponse = json_decode($this->deleteGuzzleClient(array(), 'admin/procurements/'.$batch_id)->getBody()->getContents());

        if(empty($deleteResponse->error)){
            $message = 'Successfully deleted product batch ID: ' . $batch_id;

            flash()->success($message);
            return redirect()->route('products.'.$type.'.index');
        }else{
            $message = 'Unable to delete create sheet, please try again later.';

            flash()->error($message);
            return back();
        }
    }

    public function updateItem(Request $request, $batch_id)
    {
        //Product Details
        $inputs['name'] = trim($request->input('name'));
        $inputs['color'] = trim($request->input('color'));
        $inputs['size'] = trim($request->input('size'));
        $inputs['tags'] = trim($request->input('tags'));

        $productData = array();
        if($request->input('name')){
            $productData['product']['name'] = $request->input('name');
        }

        if($request->input('sku_id')){
            $productData['sku'][] = [
                'sku_id'=>$request->input('sku_id'),
                'options'=> [
                    'Colour'=> $request->input('color'),
                    'Size' => $request->input('size')
                ],
                'sku_weight' => $request->input('weight')
            ];
        }
        if($request->input('tags')){
            $productData['tags']            = $request->input('tags');
        }

        // Procurement Details
        $postData = array();
        if(!empty($productData)) $postData['productData'] = $productData;
        $postData['item_cost']          =   $request->input('item_cost');
        $postData['item_quantity']      =   $request->input('item_quantity');
        if($request->input('unit_price')){
            $postData['item_retail_price']  =   $request->input('unit_price');
        }

        $rules = [
            'product_id'            => 'sometimes|required|exists:hapi.products,id,deleted_at,NULL',
            'sku_id'                => 'sometimes|required|exists:hapi.sku,sku_id,deleted_at,NULL',
            'name'                  => 'sometimes|required',
            'tags'                  => 'sometimes|required|tag',
            'color'                 => 'sometimes|required',
            'size'                  => 'sometimes|required',
            'weight'                => 'sometimes|required|numeric|min:0.001',
            'item_cost'             => 'sometimes|required|numeric|min:0.01',
            'item_quantity'         => 'sometimes|required|integer|min:0',
            'unit_price'            => 'sometimes|required|numeric|min:0.00',
        ];

        $messages = [
            'product_id.exists' => 'The product has been deleted.',
            'sku_id.exists' => 'The sku has been deleted.',
            'tags.tag' => 'Tags input is invalid.',
            'tags.required' => 'Tags input is required.',
            'color.required' => 'Colour input value is required.',
            'size.required' => 'Size input value is required.',
            'weight.required' => 'Weight input value is required.',
            'weight.numeric' => 'Weight input value must be in number.',
            'item_cost.required' => 'Unit cost is required.',
            'item_quantity.required' => 'Quantity is required.',
            'unit_price.required' => 'Retail price is required.',
            'name.required' => 'Product name is required.',

        ];

        $v = \Validator::make($request->all(), $rules, $messages);

        if ($v->fails()) {
            $errors = $v->errors()->all();
            $response = array(
                'error'      => $errors,
                'success'    => false,
                );
            return response()->json($response);
        }else {
            $updateResponse = json_decode($this->putGuzzleClient($postData, 'admin/procurements/'.$request->input('batch_id').'/items/'.$batch_id)->getBody()->getContents());

            if(empty($updateResponse->error)){
                // remove existing barcode file from S3 if exists
                $s3 = new MediaService();
                $file = $s3->checkFileInS3("barcodes/barcode_list_batch_".$request->input('batch_id').".csv");
                if($file) {   
                    $s3->removeFileFromS3("barcodes/barcode_list_batch_".$request->input('batch_id').".csv");
                }
                $response = array(
                    'success'   => true,
                    'response'  => $updateResponse,
                    's3'        => $file,
                    );
            }else{
                $errorMsg = new MessageBag((array)$updateResponse->error);
                $response = array(
                    'error'     => $errorMsg->all(),
                    'success'   => false,
                    );
            }
        }

        return response()->json($response);
    }

    public function deleteItem(Request $request, $batch_id)
    {
        $deleteResponse = json_decode($this->deleteGuzzleClient(array(), 'admin/procurements/'.$request->input('batch_id').'/items/'.$batch_id)->getBody()->getContents());

        if(empty($deleteResponse->error)){
            $response = array(
                'success'   => true,
                'response'  => $deleteResponse,
                );
        }else{
            $errorMsg = new MessageBag((array)$deleteResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }
        return response()->json($response);
    }

    // START Create products CRUD
    public function indexCreate($channel_id=null)
    {
        return view('product-management.create.list', ['channel_id'=>$channel_id]);
    }

    public function getTableDataCreate()
    {
        $channel_id = request()->get('channel_id', null);
        // get purchase batch list
        $response = json_decode($this->getGuzzleClient(request()->all(), 'admin/procurements')->getBody()->getContents());
        // get merchant list
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants/with-trashed')->getBody()->getContents());

        $merchantList = array();
        $adminList = array();
        foreach($merchants->merchants as $merchant)
        {
            $merchantList[$merchant->id] = $merchant->name;
        }
        $merchantList[0] = 'N/A';
        // get AE list
        $users = User::all();
        foreach($users as $user)
        {
            $adminList[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
        }
        $adminList[0] = 'N/A';
        $dataArray = array();
        $data = array();
        // prepare the purchase batches
        foreach($response->batches as $batch){
            if($batch->replenishment < 1){
                if(!is_null($channel_id) && !empty($channel_id)) {
                    $route = route('byChannel.products.create.show', ['channel_id'=>$channel_id, 'batch_id'=>$batch->batch_id]);
                } else {
                    $route = route('products.create.show', $batch->batch_id);
                }
                $actions = '<a href="'.$route.'">View Items</a>';
                if($batch->batch_status < 1 && $this->admin->can('edit.restock'))
                    $actions .= ' | <a href="'.route('products.create.edit', [$batch->batch_id]).'">Edit</a>';
                if($batch->user_id == null){
                    $batch->user_id = 0;
                }
                $dataArray = [
                        "id"            => $batch->batch_id,
                        "batch_date"    => $batch->batch_date,
                        "merchandiser"  => $adminList[$batch->user_id],
                        "merchant"      => $merchantList[$batch->merchant_id],
                        "no_of_items"   => isset($batch->item_count->item_count)?number_format((float)$batch->item_count->item_count, 0):0,
                        "total_value"   => isset($batch->total_item_cost->total_item_cost)?number_format((float)$batch->total_item_cost->total_item_cost, 2, '.', ',').'':'0.00',
                        "status"        => $this->statusLabel[$batch->batch_status],
                        "action"        => $actions,
                      ];

                $data[] = $dataArray;
            }
        }

        return json_encode(array("data" => $data));
    }

    public function createCreate()
    {
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants/')->getBody()->getContents());
        $merchantList = array();
        $aeList = array();
        $nonActive = array();
        $merchantByStatus = array();
        $nonActiveUser = array();
        $userByStatus = array();

        // get channel type
        $channelTypeResponse = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/')->getBody()->getContents());
        $channelTypes = array();
        foreach($channelTypeResponse as $channelType){
            $channelTypes[$channelType->id] = $channelType->name;
            if($channelType->name == 'Warehouse'){
                $data['warehouseTypeId'] = $channelType->id;
            }
        }

        // To separate active and inactive statuses
        foreach($merchants->merchants as $merchant)
        {
            //$merchantList[$merchant->id] = $merchant->name;
            if(strcmp($merchant->status,"Active")!=0){
                $nonActive[$merchant->status][$merchant->id] = $merchant->name;
            }else{
                $merchantByStatus[$merchant->status][$merchant->id] = $merchant->name;
            }
        }
        $merchantList = array_merge($merchantByStatus,$nonActive);

        $users = User::AEOE();
        //dd($users);
        foreach($users as $user)
        {
            // $aeList[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
            if(strcmp($user['status'],"Active")!=0){
                $nonActiveUser[$user['status']][$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
            }else{
                $userByStatus[$user['status']][$user['id']] = $user['first_name'] . ' ' . $user['last_name'];;
            }
        }
        $aeList = array_merge($userByStatus, $nonActiveUser);

        $data['channelTypes'] = $channelTypes;
        $data['templateUrl'] = $this->getProductSheetTemplate('create');
        $data['merchandiser'] = $aeList;
        $data['merchant'] = $merchantList;
        //dd($merchants->merchants);
        return view('product-management.create.create', $data);
    }

    public function storeCreate($request, $productData)
    {
        $return = array();
        $rules = [
            'merchandiser'      => 'required',
            'batch_date'        => 'required|date_format:Y-m-d',
            'merchant'          => 'required',
            'channel'           => 'required|integer|min:1',
            'supplier'          => 'required|integer|min:1',
        ];
        $messages = [
            'merchandiser.required'     => 'Person-in-charge field is required',
            'batch_date.required'       => 'Batch date field is required',
            'batch_date.date_format'    => 'Please make sure the batch date is entered in this format: yyyy-mm-dd',
            'merchant.required'         => 'Merchant field is required',
            'supplier.required'         => 'Supplier field is required',
            'supplier.integer'          => 'Supplier field is required',
            'channel.required'          => 'Channel field is required',
            'channel.integer'           => 'Channel field is required',
        ];
        $v = Validator::make($request->all(), $rules, $messages);
        if ($v->fails()) {
            $return['success'] = false;
            $return['error'] = array('messages'=>$v->errors()->all());
            return $return;
        }
        //dd($request->input());
        $batchTax = 0;
        $batchShipping = 0;
        $channelId = 0;
        $batchStatus = 0;
        $products = array();
        foreach($productData['items'] as $product){
            $products[] = array(
                'description'       =>  $product['product_desc'],
                'prefix'            =>  $product['product_brand'],
                'client_sku'        =>  $product['client_sku'],
                'sku_supplier_code' =>  $product['sku_supplier_code'],
                'name'              =>  $product['product_name'],
                'option_name'       =>  $product['option_name'],
                'option_value'      =>  $product['option_value'],
                'item_quantity'     =>  $product['item_quantity'],
                'item_cost'         =>  $product['item_cost'],
                'item_retail_price' =>  $product['unit_price'],
                'sku_weight'        =>  $product['sku_weight'],
                'category_id'       =>  $product['category_id'],
                'tags'              =>  $product['sku_tag'],
                'product'           =>  $product['product'],
            );
        }
        $postData = array(
                'batch_currency'        => 'MYR',
                'batch_conversion_rate' => 1.0,
                'batch_remarks'         => $request->input('remarks'),
                'batch_status'          => $batchStatus,
                'client_id'             => 4,
                'replenishment'         => 0,
                'user_id'               => $request->input('merchandiser'),
                'supplier_id'           => $request->input('supplier'),
                'channel_id'            => $request->input('channel'),
                'batch_tax'             => 0,
                'batch_shipping'        => 0,
                'batch_date'            => $request->input('batch_date'),
                'merchant_id'           => $request->input('merchant'),//4,$request->input('merchant')
                'items'                 => $products,
            );

        $response = json_decode($this->postGuzzleClient($postData, 'admin/procurements')->getBody()->getContents());
        if(empty($response->error)){
            // change to get route
            $return['success'] = true;
            $return['redirect'] = route('products.create.edit', ['id'=>$response->batch_id]);
        }else{
            $message = array();
            foreach($response->error as $key => $error){
                if(strpos($key, 'items') !== false){
                    //$itemNo = (int)substr($key, 6, 1);
                    $itemNo = explode('.',$key);
                    $message[] = 'Error at product #' . ($itemNo[1]+1) . ' :' . $error[0] . '.';
                }
                else{
                    $message []= $error[0] . '.';
                }
            }
            $return['success'] = false;
            $return['error']['messages'] = $message;
        }
        return $return;
    }

    // END Create Products CRUD

    // START Restock Products CRUD
    public function indexRestock($channel_id=null)
    {
        return view('product-management.restock.list', ['channel_id'=>$channel_id]);
    }

    public function getTableDataRestock()
    {
        $channel_id = request()->get('channel_id', null);
        // get purchase batch list
        $response = json_decode($this->getGuzzleClient(request()->all(), 'admin/procurements')->getBody()->getContents());
        // get merchant list
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants/with-trashed')->getBody()->getContents());
        $merchantList = array();
        $adminList = array();
        foreach($merchants->merchants as $merchant)
        {
            $merchantList[$merchant->id] = $merchant->name;
        }
        $merchantList[0] = 'N/A';
        // get AE list
        $users = User::all();
        foreach($users as $user)
        {
            $adminList[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
        }
        $adminList[0] = 'N/A';
        $dataArray = array();
        $data = array();
        // prepare the purchase batches
        foreach($response->batches as $batch){
            if($batch->replenishment > 0){
                if(!is_null($channel_id) && !empty($channel_id)) {
                    $route = route('byChannel.products.restock.show', ['channel_id'=>$channel_id, 'batch_id'=>$batch->batch_id]);
                } else {
                    $route = route('products.restock.show', $batch->batch_id);
                }
                $actions = '<a href="'.$route.'">View Items</a>';
                if($batch->batch_status < 1 && $this->admin->can('edit.restock'))
                    $actions .= ' | <a href="'.route('products.restock.edit', [$batch->batch_id]).'">Edit</a>';
                if($batch->user_id == null){
                    $batch->user_id = 0;
                }
                $dataArray = [
                        "id"            => $batch->batch_id,
                        "batch_date"    => $batch->batch_date,
                        "merchandiser"  => $adminList[$batch->user_id],
                        "merchant"      => $merchantList[$batch->merchant_id],
                        "no_of_items"   => isset($batch->item_count->item_count)?number_format((float)$batch->item_count->item_count, 0):0,
                        "total_value"   => isset($batch->total_item_cost->total_item_cost)?number_format((float)$batch->total_item_cost->total_item_cost, 2, '.', ',').'':'0.00',
                        "status"        => $this->statusLabel[$batch->batch_status],
                        "action"        => $actions,
                      ];

                $data[] = $dataArray;
            }
        }

        return json_encode(array("data" => $data));
    }

    public function createRestock()
    {
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants/')->getBody()->getContents());
        $merchantList = array();
        $aeList = array();
        $nonActive = array();
        $merchantByStatus = array();
        $nonActiveUser = array();
        $userByStatus = array();

        // get channel type
        $channelTypeResponse = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/')->getBody()->getContents());
        $channelTypes = array();
        foreach($channelTypeResponse as $channelType){
            $channelTypes[$channelType->id] = $channelType->name;
        }

        // To separate active and inactive statuses
        foreach($merchants->merchants as $merchant)
        {
            //$merchantList[$merchant->id] = $merchant->name;
            if(strcmp($merchant->status,"Active")!=0){
                $nonActive[$merchant->status][$merchant->id] = $merchant->name;
            }else{
                $merchantByStatus[$merchant->status][$merchant->id] = $merchant->name;
            }
        }
        $merchantList = array_merge($merchantByStatus,$nonActive);

        $users = User::AEOE();
        //dd($users);
        foreach($users as $user)
        {
            // $aeList[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
            if(strcmp($user['status'],"Active")!=0){
                $nonActiveUser[$user['status']][$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
            }else{
                $userByStatus[$user['status']][$user['id']] = $user['first_name'] . ' ' . $user['last_name'];;
            }
        }
        $aeList = array_merge($userByStatus, $nonActiveUser);

        $data['channelTypes'] = $channelTypes;
        $data['templateUrl'] = $this->getProductSheetTemplate('restock');
        $data['merchandiser'] = $aeList;
        $data['merchant'] = $merchantList;

        return view('product-management.restock.create', $data);
    }

    public function storeRestock($request, $productData)
    {
        $return = array();
        $rules = [
            'merchandiser'      => 'required',
            'batch_date'        => 'required|date_format:Y-m-d',
            'merchant'          => 'required',
            'channel'           => 'required|integer|min:1',
            'supplier'          => 'required|integer|min:1',
        ];
        $messages = [
            'merchandiser.required'     => 'Person-in-charge field is required',
            'batch_date.required'       => 'Batch date field is required',
            'batch_date.date_format'    => 'Please make sure the batch date is entered in this format: yyyy-mm-dd',
            'merchant.required'         => 'Merchant field is required',
            'channel.required'          => 'Channel field is required',
            'channel.integer'           => 'Channel field is required',
            'supplier.required'         => 'Supplier field is required',
            'supplier.integer'          => 'Supplier field is required',
        ];
        $v = Validator::make($request->all(), $rules, $messages);
        if ($v->fails()) {
            $return['success'] = false;
            $return['error'] = array('messages'=>$v->errors()->all());
            return $return;
        }
        //dd($request->input());
        $batchTax = 0;
        $batchShipping = 0;
        $channelId = 0;
        $batchStatus = 0;
        $products = array();
        //dd($request->input('sku_supplier_code'));
        foreach($productData['items'] as $product){
            $products[] = array(
                'sku_id'            =>  $product['sku_id'],
                'item_quantity'     =>  $product['item_quantity'],
                'item_cost'         =>  $product['item_cost'],
                'item_retail_price' =>  0,
            );
        }
        $postData = array(
                'batch_currency'        => 'MYR',
                'batch_conversion_rate' => 1.0,
                'batch_remarks'         => $request->input('remarks'),
                'channel_id'            => $request->input('channel'),
                'client_id'             => 4,
                'batch_status'          => $batchStatus,
                'replenishment'         => 1,
                'user_id'               => $request->input('merchandiser'),
                'supplier_id'           => $request->input('supplier'),
                'batch_tax'             => 0,
                'batch_shipping'        => 0,
                'batch_date'            => $request->input('batch_date'),
                'merchant_id'           => $request->input('merchant'),//4,$request->input('merchant')
                'items'                 => $products,
            );
        $response = json_decode($this->postGuzzleClient($postData, 'admin/procurements')->getBody()->getContents());
        //dd($response);
        if(empty($response->error)){
            // change to get route
            $return['success'] = true;
            $return['redirect'] = route('products.restock.edit', ['id'=>$response->batch_id]);
        }else{
            //dd($response);
            $message = array();
            foreach($response->error as $key => $error){
                if(strpos($key, 'items') !== false){
                    //$itemNo = (int)substr($key, 6, 1);
                    $itemNo = explode('.',$key);
                    $message[] = 'Error at product #' . ($itemNo[1]+1) . ' :' . $error[0] . '.';
                }
                else{
                    $message []= $error[0] . '.';
                }
            }
            $return['success'] = false;
            $return['error']['messages'] = $message;
        }
        return $return;
    }

    // END Restock Products CRUD

    // CSV upload functions
    public function upload(Request $request, $type)
    {
        if($request->hasFile('product_sheet')){
            $file = $request->file('product_sheet');
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
            $data = $this->csv_to_array($tfile,',',',');

            if ($data['ok'] === false) {
                $return['success'] = false;
                $return['error'] = array('messages'=>$data['messages']);
                return json_encode($return);
            }

            $return['data'] = $data;

            if(strtolower($return['data']['type']) == 'replenishment'){
                if(strtolower($type) != 'restock'){
                    $return['success'] = false;
                    $return['error'] = array('messages'=>array('File is invalid!'));
                }else{
                    $return['success'] = true;
                    /*
                    $postSku = array();
                    foreach($return['data']['items'] as $item){
                        $postSku['post']['sku_id'][(int)$item['sku_id']] = $item['sku_id'];
                        $postSku['data'][(int)$item['sku_id']] = $item;
                    }
                    $skuResponse = json_decode($this->postGuzzleClient($postSku['post'], 'admin/sku/bulk/sku_id')->getBody()->getContents());
                    unset($postSku['post']);
                    foreach($skuResponse as $skuItem){
                        $postSku['data'][$skuItem->sku_id]['name'] = $skuItem->product_details->name;
                        //$postSku['data'][$skuItem->sku_id]['tags'] = $skuItem->tags->tag_value;
                        $postSku['data'][$skuItem->sku_id]['weight'] = $skuItem->sku_weight;
                        $postSku['data'][$skuItem->sku_id]['tags'] = '';
                        if($skuItem->sku_supplier_code == '')
                            $skuItem->sku_supplier_code = '-';
                        $postSku['data'][$skuItem->sku_id]['supplier_sku'] = $skuItem->sku_supplier_code;
                        $postSku['data'][$skuItem->sku_id]['hubwire_sku'] = $skuItem->hubwire_sku;
                        foreach($skuItem->combinations as $combination){
                            if($combination->option_name == 'Size'){
                                $postSku['data'][$skuItem->sku_id]['size'] = $combination->option_value;
                            }elseif($combination->option_name == 'Colour'){
                                $postSku['data'][$skuItem->sku_id]['color'] = $combination->option_value;
                            }
                        }
                        foreach($skuItem->tags as $tag){
                            $postSku['data'][$skuItem->sku_id]['tags'] .= $tag->tag_value . ', ';
                        }
                        $postSku['data'][$skuItem->sku_id]['tags'] = rtrim($postSku['data'][$skuItem->sku_id]['tags'], ', ');
                    }
                    unset($return['data']['items']);
                    $return['data']['items'] = $postSku['data'];
                    */
                }
            }elseif(strtolower($return['data']['type']) == 'create'){
                if(strtolower($type) != 'create'){
                    $return['success'] = false;
                    $return['error'] = array('messages'=>array('File is invalid!'));
                }else{
                    $return['success'] = true;
                }
            }
            if($return['success']){
                // proceed to store
                if(strtolower($type) == 'create'){
                    // storecreate($request, $return['data'])
                    $response = $this->storeCreate($request, $return['data']);
                    //\Log::info($return['data']);
                }elseif(strtolower($type) == 'restock'){
                    // storerestock($request, $return['data'])
                    $response = $this->storeRestock($request, $return['data']);
                }
                return json_encode($response);
            }else{
                //return error message
                return json_encode($return);
            }
        }
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
            if(count($test) < 13 && count($test) != 3 )
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
                        'sku_id'=>$row[0],
                        'item_cost'=>$row[1],
                        'item_quantity'=>$row[2],
                        //'item_retail_price'=>$row[3]
                    );
                }
                $mydata['type']     = "replenishment";
            }
            //NEW CREATION
            if(count($test) >= 14)
            {
                $r = 1;
                while (($row = fgetcsv($handle, 0, $delimiter1)) !== FALSE)
                {
                    $row = array_map('trim', $row);

                    if($r==1) { // capture the options names
                        $tmp = array_slice($row,count($header)-4);
                        $g_option_names = array_values(array_filter($tmp)); 
                        if(!in_array('colour', array_map('strtolower',$g_option_names))) {
                            $mydata['messages'][] = 'Colour option not found.';
                            $mydata['ok'] = false;
                        }
                        if(!in_array('size', array_map('strtolower', $g_option_names))) {
                            $mydata['messages'][] = 'Size option not found.';
                            $mydata['ok'] = false;
                        }
                        if(!$mydata['ok']){
                            return $mydata;
                        }
                    }
                    else{
                        // check row empty
                        $chk = array_filter($row,'strlen');
                        $chk2 = array_filter($chk,'trim');
                        if(empty($chk2)){
                            continue;//$r++;
                        }
                        if(trim(strtoupper($row[0]))==='NEW')
                        {
                            $product_id = 0;
                        }
                        else{
                            $product_id = $row[0];
                        }

                        $row[0] = $product_id;
                        // get the default option_values
                        $g_option_values = array_slice($row,count($header)-3,count($g_option_names));
                        // combine option_name vs option_value by default
                        $options_combine = array_combine($g_option_names, $g_option_values);
                        // remove empty value from options_combine, etc 'Size'=>''
                        $option_f = array_filter($options_combine,'strlen');
                        // extract option_names from valid options
                        $option_names = array_keys($option_f);

                        if(!in_array('colour', array_map('strtolower',$option_names))) {
                            $mydata['messages'][] = 'ITEM #'.($r-1).' => Colour: option value is required.';
                            $mydata['ok'] = false;
                            //return $mydata;
                        }
                        if(!in_array('size', array_map('strtolower', $option_names))) {
                            $mydata['messages'][] = 'ITEM #'.($r-1).' => Size: option value is required.';
                            $mydata['ok'] = false;
                            //return $mydata;
                        }
                        if(!$row[12]) {
                            $mydata['messages'][] = 'ITEM #'.($r-1).' => Category: category is required.';
                            $mydata['ok'] = false; 
                        }

                        // extract option_values from valid options
                        $option_values = array_values($option_f);
                        // get the whole row values, exclude options
                        $row_values = array_slice($row,0,count($header)-3);
                        //push the option_names (array) to end of the row
                        array_push($row_values,$option_names);
                        // push the option_values (array) to end of the row
                        array_push($row_values,$option_values);

                        $row_values[6] = floatval($row_values[6]*1);
                        $row_values[8] = floatval($row_values[8]*1);
                        // cast the tags value to array format
                        $row_values[13] = explode($delimiter2,$row_values[13]);
                        $row_values[13] = array_map('trim', $row_values[13]);
                        $row_values[13] = array_unique($row_values[13]);

                        // product_desc convert nl2br
                        $row_values[9] = str_replace("\r\n", "<br>", $row_values[9]);
                        $row_values[9] = str_replace("\r", "<br>", $row_values[9]);
                        $row_values[9] = html_entity_decode($row_values[9]);

                        // put item_status = 0 to the very begining of the array
                        array_unshift($row_values, 0);
                        //$data[] = $row_values;
                        $data[] = array_combine($header, $row_values);
                        //dd($data);
                    }
                    $r++;
                }
                $mydata['type'] = "create";
                $mydata['g_options_name'] = $g_option_names;
            }

            fclose($handle);
            $mydata['items'] = $data;
            return $mydata;
        }
    }

    public function getProductDetails(Request $request)
    {
        $channel = $request->input('channelId');
        $hubwireSku = trim($request->input('hubwireSku'));
        $found = false;
        $data = ['type' => 'hubwire_sku', 'keyword' => $hubwireSku, 'channel_id' => $channel];

        $response = json_decode($this->getGuzzleClient($data, 'admin/inventories/findBy/')->getBody()->getcontents());

        if($response !== false){
            $return = array();
            foreach ($response->product->sku_in_channel as $channel_sku) {
                if($channel_sku->channel_id == $channel && $channel_sku->sku->hubwire_sku == $hubwireSku){
                    $return['success'] = true;
                    $return['channel_sku_id'] = $channel_sku->channel_sku_id;
                    $return['hubwire_sku'] = $response->sku->hubwire_sku;
                    $return['name'] = $response->product->name;
                    $return['currency'] = $channel_sku->channel->currency;
                    $return['unit_price'] = number_format($channel_sku->channel_sku_price, 2);
                    $return['sale_price'] = number_format(($channel_sku->channel_sku_promo_price > 0 ? $channel_sku->channel_sku_promo_price : $channel_sku->channel_sku_price), 2);
                    $return['discount'] = number_format($return['unit_price'] - $return['sale_price'], 2);
                    $found = true;
                    break;
                }
            }
        }else{
            $found = true;
            $return['success'] = false;
            $return['error'] = 'The product '.$hubwireSku.' was not found for the selected channel.';
        }
        if($found == false){
            $return['success'] = false;
            $return['error'] = 'The product '.$hubwireSku.' was not found for the selected channel.';
            //$return['error'] = 'The channel id '.$channel.' was not found.';
        }

        return $return;
    }

    /**
     * Function to check if barcode csv already previously existed on S3
     *
     * return String s3 link
     */
    public function getBarcodeCsv($id)
    {
        $s3 = new MediaService();
        $link = $s3->checkFileInS3("barcodes/barcode_list_batch_".$id.".csv");

        if($link)

        {   
            $this->downloadFile($link, "barcode_list_batch_".$id, 'csv');
            //return response()->json(['success' =>true, 'url' => $link]);
        }else{
            $file = $this->generateBarcodeCsv($id);
            if($file){
                $this->downloadFile($file, "barcode_list_batch_".$id, 'csv');
                //return response()->json(['success' =>true, 'url' => $file]);    
            }else{
                flash()->error('There was an error generating the bracode list file. Please try again. If issue persist, please contact Technology for assistance.');
                return redirect()->back();
            }
        }
    }

    /**
     * Function to generate barcode in CSV format
     *
     * return boolean
     */
    public function generateBarcodeCsv($id)
    {
        $s3 = new MediaService();
        $batch = json_decode($this->getGuzzleClient(array(), "admin/procurements/$id")->getBody()->getContents());

        if(count($batch->items) <= 0){ return "false"; }

        //the assumption of this funciton is such that API will always return all products in the same option sequence, with all options included. Otherwise this will break.
        //eg. if a product A has options <Size> and <Colour>, while product B has options <Size> and <Weight>, all returned products must have <Size>, <Colour>, and <Weight> returned.

        $list = array(
            array(
                trans('product-management.create_form_label_hw_sku'),
                trans('product-management.create_form_label_supplier_sku'),
                trans('product-management.create_form_label_name'),
                trans('product-management.create_form_label_brand'),
                trans('product-management.create_form_label_unit_cost'),
                trans('product-management.create_form_label_unit_price'),
                trans('product-management.create_form_label_unit_price_without_gst'),
            )
        );
        $formattedData = array();
        foreach($batch->items[0]->sku->combinations as $option)
        {
            array_push($list[0], $option->option_name);
        }

        $items = $batch->items;

        foreach($items as $item)
        {
            for($i = 1; $i <= $item->item_quantity; $i++) {
                $item->retail_price_without_gst = number_format($item->item_retail_price / 1.06, 2);
                $temp = [ $item->sku->hubwire_sku,
                          $item->sku->sku_supplier_code,
                          htmlspecialchars_decode($item->sku->product->name),
                          $item->sku->product->brand_name,
                          number_format($item->item_cost, 2),
                          number_format($item->item_retail_price, 2),
                          $item->retail_price_without_gst
                        ];

                foreach($item->sku->combinations as $option) {
                    array_push($temp, $option->option_value);
                }
                $formattedData[$item->sku->product_id][$item->sku->sku_id][] = $temp;
                //array_push($list, $temp);
            }
        }
        ksort($formattedData);      // sort skus by product id

        foreach ($formattedData as $skus) {
            foreach($skus as $skuRows) {
                $list = array_merge($list, $skuRows);
                array_push($list, []);  // insert empty row between each sku
            }
        }

        $fp = fopen('/tmp/products_barcode_list_batch_'.$id.'.csv', 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        $upload = $s3->uploadFileToS3("/tmp/products_barcode_list_batch_".$id.".csv", "barcodes/barcode_list_batch_".$id.".csv");
        fclose($fp);

        if(is_array($upload) && array_key_exists("errors", $upload)) {
            return false;
        }

        return $upload->url;

    }

    public function getProductSheetTemplate($type = 'create')
    {
        if($type == 'create'){
            $filename = 'new_product_form';
        }elseif($type == 'restock'){
            $filename = 'replenishment_form';
        }
        $s3 = new MediaService();
        $link = $s3->checkFileInS3("templates/".$filename.".csv");

        if($link){
            return $link;
        }else{
            return 'templates/'.$filename.'.csv';
        }
    }

    public function downloadCsv(Request $request)
    {
        $link = $request->get('link');
        $filename = $request->get('filename');

        $this->downloadFile($link, $filename, 'csv');
    }
}
