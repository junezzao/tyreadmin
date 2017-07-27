<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Http\Requests\ManualOrderRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\ChannelRepository;
use App\Http\Traits\GuzzleClient;
use DB;
use App\Http\Traits\DocumentGeneration;
use Log;
use Input;
use Response;
use Config;
use Carbon\Carbon;
use App\Models\Channel;

class OrdersController extends Controller
{
    use DocumentGeneration, GuzzleClient;

    protected $orderRepo;
    protected $channelRepo;
    protected $admin;
    protected $channels;
    protected $merchants;
    protected $statuses;
    protected $members;

    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepo, ChannelRepository $channelRepo)
    {
        $this->middleware('auth');

        $this->orderRepo = $orderRepo;
        $this->channelRepo = $channelRepo;
        $this->admin = \Auth::user();
        $this->merchants = $this->channels = array();

        $merchants = json_decode($this->getGuzzleClient(array(''), 'admin/merchants')->getBody()->getContents())->merchants;
        foreach ($merchants as $merchant) {
            $this->merchants[$merchant->id] = $merchant->name;
        }

        $channels = json_decode($this->getGuzzleClient(array(), 'channels/channel')->getBody()->getContents());
        foreach($channels as $channel) {
            $this->channels[$channel->id] = $channel->name;
        }

        $this->statuses = Config::get('globals.sales.search_status');
    }

    public function index()
    {
        $data['statuses'] = $this->statuses;
        $data['payments'] = Config::get('globals.sales.payment');

        $channel_id = request()->route('channel_id', null);

        $data['merchants'] = array();
        $merchants = json_decode($this->getGuzzleClient(array('channel_id'=>$channel_id), 'admin/merchants')->getBody()->getContents())->merchants;
        foreach ($merchants as $merchant) {
            $data['merchants'][$merchant->id] = $merchant->name;
        }

        $data['channels'] = array();
        $filters = request()->all();
        $filters['channel_id'] = $channel_id;
        $channels = json_decode($this->getGuzzleClient($filters, 'channels/channel')->getBody()->getContents());
        foreach($channels as $channel) {
            if ($channel->channel_type_id!=12)
                $data['channels'][$channel->id] = $channel->name;
        }
        $data['filters'] = $filters;
        $data['paid_status'] = [0=>'Unpaid', 1=>"Paid"]; 
        $data['partially_fulfilled'] = [0=>'Not Partially Fulfilled', 1=>"Partially Fulfilled"];
        $data['cancelled_status'] = [0=>'Not Cancelled', 1=>"Cancelled"];

        if (!is_null($this->admin->merchant_id) && $this->admin->is('clientuser|clientadmin')) 
            $data['merchant_id'] = $this->admin->merchant_id;
        
        $data['channel_id'] = $channel_id;

        return view('orders.index', $data);
    }

    public function showPrint($order_id)
    {
        $this->generateTaxInvoice($order_id);
    }

    public function show()
    {   
        $id = request()->route('order_id');
        $channel_id = request()->route('channel_id', null);

        $order = $this->orderRepo->find($id);
        $items = $this->orderRepo->getItems($id);
        $promotions = $this->orderRepo->getPromotionCodes($id);
        $order->promotions = $promotions;
        $member = $this->orderRepo->getOrderMemberDetails($order->member_id);
        $paidStatusList = $this->orderRepo->getPaidStatusList();
        $statusList = $this->orderRepo->getStatusList($order->status);
        $channel = $this->channelRepo->find($order->channel_id);//dd($channel);
        $returnLog = $this->orderRepo->getReturnsAndCancelledItems($id);
        $order->hasShipped = $this->orderRepo->hasShipped($order->status);
        $order->status = $this->orderRepo->getStatus($order->status);
        $notes = $this->orderRepo->getNotes($id);
        $history = $this->orderRepo->getHistory($id);
        $order->hasReturns = false;
        if(!empty($returnLog)){
            $order->hasReturns = true;
            foreach($items as $item){
                $restocked_count = 0;
                $in_transit_count = 0;
                $rejected_count = 0;
                foreach($returnLog as $return){
                    if($return->order_item_id == $item->item->id){
                        if($return->status == 'Restocked') $restocked_count++;
                        if($return->status == 'In Transit') $in_transit_count++;
                        if($return->status == 'Rejected') $rejected_count++;
                    }
                }
                $item->returns = ['Restocked' => $restocked_count, 'In Transit' => $in_transit_count, 'Rejected' => $rejected_count];
            }
        }
        if(!empty($history)){
            $groupedHistory = array();
            foreach($history as $record){
                if(!$record->invalidDate){
                    $date = Carbon::createFromFormat('Y-m-d H:i:s', $record->created_at)->format('j M. Y');
                }else{
                    $date = 'Invalid date';
                    $record->created_at = 'Invalid date';
                }
                $groupedHistory[$date][] = $record;
            }

        }
        $eventClasses = config('globals.order_history_event_type_class');
        /*$issuing_company_list = array();
        $issuing_companies = json_decode($this->getGuzzleClient(array(), 'admin/issuing_companies')->getBody()->getContents());
        foreach($issuing_companies as $company)
        {
            $issuing_company_list[$company->id] = $company->gst_reg;
        }
        $gst_reg_detail = $issuing_company_list[$channel->issuing_company];
        */

        //dd(compact('order', 'items', 'paidStatusList', 'statusList', 'member', 'channel', 'returnLog', 'notes', 'groupedHistory', 'eventClasses', 'channel_id','gst_reg_detail'));
        return view('orders.show', compact('order', 'items', 'paidStatusList', 'statusList', 'member', 'channel', 'returnLog', 'notes', 'groupedHistory', 'eventClasses', 'channel_id','gst_reg_detail'));
    }

    public function create($channel_id=null)
    {
        /* Temporarily remove merchant field
        $merchantList = array();
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants')->getBody()->getContents());
        foreach ($merchants->merchants as $merchant) {
            $merchantList = array_add($merchantList, $merchant->id, $merchant->name);
        }*/
        $paymentTypeList = Config::get('globals.sales.payment');
        $paymentTypeList = [''=>''] + $paymentTypeList;
        $channelList = array();
        //$channels = $this->channelRepo->all();
        $channels = json_decode($this->getGuzzleClient(array('channel_id'=>$channel_id), 'channels/channel/')->getBody()->getContents());
        foreach ($channels as $channel) {
            if (isset($channel->channel_detail)) {
                $getExtraInfo = json_decode($channel->channel_detail->raw_extra_info, true);
                if (!isset($getExtraInfo['shipping_provider'])) {
                    $getExtraInfo['shipping_provider'] = null;
                }
                if (!isset($getExtraInfo['shipping_provider_cod'])) {
                    $getExtraInfo['shipping_provider_cod'] = null;
                }
            }
            if($channel->channel_type_id != 12 && $channel->channel_type->manual_order == 1){
                array_push($channelList, ['value' => $channel->id, 'key' => $channel->name, 'data-type' => $channel->channel_type_id, 'shipping_provider' => $getExtraInfo['shipping_provider'], 'shipping_provider_cod' => $getExtraInfo['shipping_provider_cod']]);
            }
        }


        $currencyList = config('currencies');
        $data = compact('channelList', 'currencyList', 'paymentTypeList');
        $data['admin'] = $this->admin;

        if(Input::get('tpid')){
            $data['tpCode'] = Input::get('tpid');
        }else{
            $data['tpCode'] = NULL;
        }

        if(Input::get('cid')){
            $data['channelId'] = Input::get('cid');
        }else{
            $data['channelId'] = NULL;
        }

        return view('orders.create', $data);
    }

    public function getItemDetails($channel, $hubwireSku)
    {
        $data = array('channel' => $channel, 'hubwireSku' => $hubwireSku);
       // $item = $this->getGuzzleClient($data, 'channel//get_channel_sku_details')->getBody()->getContents()
    }

    public function store(ManualOrderRequest $request)
    {
        if (!$request->has('total_discount')) {
            $totalUnitPrice = 0;
            $totalSalePrice = 0;
            $totalHwDiscount = 0;
            $subtotal = 0;
            foreach ($request->input('unit_price') as $val) {
                $totalUnitPrice += $val;
            }
            foreach ($request->input('sale_price') as $val) {
                $totalSalePrice += $val;
            }
            $totalHwDiscount = $totalUnitPrice - $totalSalePrice;
        
            $request->merge(array('total_discount' => number_format($totalHwDiscount, 2)));
        }
        
        if (!$request->has('subtotal')) {
            foreach ($request->input('sold_price') as $val) {
                $subtotal += $val; 
            }
            $request->merge(array('subtotal' => number_format($subtotal, 2)));
        }
        $channel_type = array(8,9,13);
        if ($request->payment_type=="CashOnDelivery"&&(in_array($request->channel_type, $channel_type))) {
            $request->merge(array('shipping_provider' => $request->shipping_provider_cod));
        }
        
        $response = $this->orderRepo->create($request->except(['_token']));
        $response = json_decode($response->getBody()->getContents());
        
        if ($response->success) {
            $message = 'Order '.$response->order_id.' was successfully created.';
            
            flash()->success($message);

            if($this->admin->is('channelmanager')) {
                return redirect()->route('byChannel.orders.create', $request->get('channel'));
            }
            else {
                return redirect()->route('orders.create');
            }
        } else {
            flash()->error($response->error_desc);
            return back()->withInput();
        }
    }

    public function cancelItem(Request $request, $order_id, $item_id) {
        $response = json_decode($this->postGuzzleClient($request->all(), 'orders/' . $order_id . '/item/' . $item_id . '/cancel')->getBody()->getContents());        

        if (!empty($response->id)) {            
            flash()->success('Item has been cancelled successfully');
        } else {
            flash()->error($response);
        }

        return redirect()->route('order.show', $order_id);
    }

    public function returnItem(Request $request, $order_id, $item_id) {
        $response = json_decode($this->postGuzzleClient($request->all(), 'orders/' . $order_id . '/item/' . $item_id . '/return')->getBody()->getContents());        

        if (!empty($response->id)) {            
            flash()->success('Item has been returned successfully');
        } else {
            flash()->error($response);
        }

        return redirect()->route('order.show', $order_id);
    }

    public function updateOrderStatus(Request $request)
    {   
        $id = $request->input('orderId');
        //$channelId = $request->input('channelId');
        $data = json_decode($request->input('data'), true);
        $data = ['data' => (array)$data];

        $hapiResponse = $this->orderRepo->update($data, $id);
        
        if(empty($hapiResponse->error)){
            $newStatusList = $this->orderRepo->getStatusList($hapiResponse->order->status);
            $response = array(
                'success' => $hapiResponse->success, 
                'newStatusList' => $newStatusList,
                );
        }else{
            $errorMsg = new MessageBag((array)$hapiResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }
        return response()->json($response);
    }

    public function cancelOrder($order_id)
    {
        $response = $this->orderRepo->cancelOrder($order_id);
        
        return response()->json($response);
    }

    public function sendConsignmentNumber($order_id)
    {
        $consignment_no = Input::get('consignment_no');
        $response = json_decode($this->postGuzzleClient(array('tracking_no'=>$consignment_no), 'orders/'.$order_id.'/readyToShip')->getBody()->getContents());
        return Response::Json($response);
    }

    // Datatables search code
    public function search()
    {   
        $query = request()->all();
        // if user is a merchant
        if (!is_null($this->admin->merchant_id) && $this->admin->is('clientuser|clientadmin')) {
            $query['columns'][12]['search']['value'] = 1;//$this->admin->merchant_id;
        }

        $channel_id = request()->get('channel_id', '');
        if(!empty($channel_id)) {
            $query['columns'][11]['search']['value'] = $channel_id;
        }

        $orders = json_decode($this->getGuzzleClient($query, 'orders/search')->getBody()->getContents());
        $data['draw'] = \Input::get('draw');
        $data['recordsTotal'] = $orders->recordsTotal;
        $data['recordsFiltered'] = $orders->recordsFiltered;
        $data['data'] = array();
        $statuses = $this->statuses;

        foreach($orders->data as $order) {
            $currency = $order->currency;
            
            $hasRefund = ($order->item_quantity < $order->original_quantity)?'*':'';
            $data['data'][] = array(
                "merchant_name" => '',//$order->merchant_id,//isset($this->merchants[$order->merchant_id])?$this->merchants[$order->merchant_id]:'',
                "channel_name" => $order->channel_id,//isset($this->channels[$order->channel_id])?$this->channels[$order->channel_id]:'',
                "id" => $order->id,
                "member_name" => $order->member_name,
                "created_at" => Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone($this->admin->timezone)->format('Y-m-d H:i:s'),
                "sale_total" => $currency.' '.number_format($order->total,2),
                "item_quantity" => is_null($order->item_quantity)? '0' : $hasRefund.($order->item_quantity),
                //"promo" => '',//isset($order->promo)?$order->promo:"",
                "status" => isset($statuses[$order->status])?$statuses[$order->status]:'',
                "payment_type" => $order->payment_type,
                "channel_id" => $order->channel_id,
                "checkbox" => \Form::checkbox('sale', $order->id),
                "paid_status" => ($order->paid_status)?'Paid':'Unpaid',
                "tp_order_code" => '',
                "merchant_id" => '',
                "partially_fulfilled"=>'',
                "cancelled_status"=>($order->cancelled_status)?'Cancelled':'Not Cancelled',
            );
        }
        return json_encode($data);
        //return response()->json($orders);
    }
    // count number of orders based on level
    public function countLevels(Request $request)
    {
        return json_decode($this->getGuzzleClient($request->all(), 'orders/levels')->getBody()->getContents(), true);
    }

    // count number of orders for each status
    public function countOrders(Request $request)
    {
        $response = json_decode($this->getGuzzleClient($request->all(), 'orders/count')->getBody()->getContents());

        $results = array(
                    "new" => $response[0]->new,
                    "picking" => isset($response[0]->picking)?$response[0]->picking:0,
                    "packing" => isset($response[0]->packing)?$response[0]->packing:0,
                    "ready-to-ship" => isset($response[0]->ready_to_ship)?$response[0]->ready_to_ship:0,
                    "partially-fulfilled" => isset($response[0]->partially_fulfilled)?$response[0]->partially_fulfilled:0,
                );
        return $results;
    }

    public function checkOrder(Request $request)
    {
        $id = $request->input('id');
        $order = $this->orderRepo->find($id);
        $found = false;
        
        if (isset($order->id)) {
            $found = true;
        }

        return response()->json(array('found' => $found));
    }

    public function createNote(Request $request, $id)
    {
        $this->validate($request, [
           'notes' => 'required',
           'note_type' => 'required',
        ]);
        $response = $this->orderRepo->createNote($request->except(['_token']), $id);
        $response = json_decode($response->getBody()->getContents());
        return back();
    }

    public function packItem(Request $request, $order_id)
    {
        $postData = array();
        $postData['hw_sku'] = $request->input('hw_sku');

        $rules = [
            'hw_sku' => 'required',
        ];

        $messages = [
            'hw_sku.required'     => 'Please enter a Hubwire SKU.',
        ];

        $v = \Validator::make($postData, $rules, $messages);

        if ($v->fails()) {
            $errors = $v->errors()->all();
            $response = array(
                'error'      => $errors,
                'success'    => false, 
                );
        }else{
            // post data to HAPI
            $hapiResponse = json_decode($this->postGuzzleClient($postData, 'orders/'.$order_id.'/item/pack')->getBody()->getContents());
            if(empty($hapiResponse->error)){
                $response = array(
                    'success'   => true,
                    'response'  => $hapiResponse,
                    );
            }else{
                $errorMsg = new MessageBag((array)$hapiResponse->error);
                $response = array(
                    'error'     => $errorMsg->all(),
                    'success'   => false,
                    );
            }
        }
        return response()->json($response);
    }

    public function printDocument($documentType, $orderId) {
        $response = json_decode($this->postGuzzleClient(array(), 'print/' . $documentType . '/' . $orderId)->getBody()->getContents(), true);

        $return['success'] = $response['success'];
        if($response['success'])
        {
            $return['media_url'] = $response['url'];
        }
        $return['message'] = !empty($response['error'])?$response['error']:'Unknown error';
        return $return;
    }

    public function printOrderSheet($orderId) {
        $data = (array) $this->orderRepo->getOrderSheetInfo($orderId);
        return view('orders.order_sheet', $data);
    }

    public function printReturnSlip($orderId) {
        $data = (array) $this->orderRepo->getReturnSlipInfo($orderId);
        return view('orders.return_slip', $data);
    }

    public function updateItemStatus(Request $request, $order_id){
        $postData = array();
        $postData['orderItemId'] = $request->input('order-item-id');

        $rules = [
            'orderItemId' => 'required',
        ];

        $messages = [
            'orderItemId.required'     => 'The order item ID is missing.',
        ];

        $v = \Validator::make($postData, $rules, $messages);

        if ($v->fails()) {
            $errors = $v->errors()->all();
            $response = array(
                'error'      => $errors,
                'success'    => false, 
                );
        }else{
            // post data to HAPI
            $hapiResponse = json_decode($this->postGuzzleClient($postData, 'orders/'.$order_id.'/item/updateStatus')->getBody()->getContents());
            if(empty($hapiResponse->error)){
                $response = array(
                    'success'   => true,
                    'response'  => $hapiResponse,
                    );
            }else{
                $errorMsg = new MessageBag((array)$hapiResponse->error);
                $response = array(
                    'error'     => $errorMsg->all(),
                    'success'   => false,
                    );
            }
        }
        return response()->json($response);
    }
}
