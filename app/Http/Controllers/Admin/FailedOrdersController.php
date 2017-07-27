<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Traits\GuzzleClient;

class FailedOrdersController extends AdminController
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:view.failedorders', ['only' => ['index', 'getTableData', 'discard', 'createOrder']]);
        $this->admin = \Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_id=null)
    {   
        return view('admin.failed-orders.list', ['channel_id'=>$channel_id]);
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
    public function edit($id)
    {
        //
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
        //
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

    public function discard($id)
    {
        $response = json_decode($this->getGuzzleClient(array(), 'fulfillment/failed_orders/'.$id.'/discard')->getBody()->getContents());

        if(!isset($response->error)){
            flash()->success('Successfully discarded record.');
        }else{
            flash()->error($response->error);
        }

        return redirect()->route('admin.fulfillment.failed_orders.index');
    }

    public function createOrder($id)
    {
        // post to hapi to update status to pending
        // redirect to manual order page
        $response = json_decode($this->getGuzzleClient(array(), 'fulfillment/failed_orders/'.$id.'/pending')->getBody()->getContents());

        if(!isset($response->errors)){
            
            return redirect()->route('orders.create', ['tpid' => $response->tp_order_id, 'cid' => $response->channel_id]);
        }else{
            flash()->error($response->errors);

            return redirect()->route('admin.fulfillment.failed_orders.index');
        }
    }

    public function getTableData ()
    {
        $response = json_decode($this->getGuzzleClient(request()->all(), 'fulfillment/failed_orders')->getBody()->getContents());

        $channelTypesResponse = json_decode($this->getGuzzleClient(request()->all(), 'channels/admin/channel_type/get_mo_enabled')->getBody()->getContents());

        $allowMOChannelTypes = array();

        foreach($channelTypesResponse as $channelTypes){
            $allowMOChannelTypes[] = $channelTypes->id;
        }

        $data = array();
        if(!empty($response)){
            foreach ($response->failedOrders as $failedOrder) {

                if(is_null($failedOrder->user)){
                    $name = 'N/A';
                }else{
                    $name = $failedOrder->user->first_name . ' ' . $failedOrder->user->last_name;
                }

                if(is_null($failedOrder->tp_order_date)){
                    $failedOrder->tp_order_date = 'N/A';
                }

                if(is_null($failedOrder->order_id)){
                    $failedOrder->order_id = 'N/A';
                }else{
                    $failedOrder->order_id = "<a target='_blank' href='".route('order.show', $failedOrder->order_id)."'>".$failedOrder->order_id."</a>";
                }
                
                if(in_array($failedOrder->channel->channel_type_id, $allowMOChannelTypes)){
                    $actionBtn = '<a target="_blank" href="'.route('admin.fulfillment.failed_orders.create_order', $failedOrder->failed_order_id).'" class="btn btn-info">Create Order</a><a href="#" data-link="'.route('admin.fulfillment.failed_orders.discard', $failedOrder->failed_order_id).'" class="btn btn-danger discard-btn">Discard</a>';
                }else{
                    $actionBtn = '<a href="#" data-link="'.route('admin.fulfillment.failed_orders.discard', $failedOrder->failed_order_id).'" class="btn btn-danger discard-btn">Discard</a>';
                }

                if(($failedOrder->status == 'New' || $failedOrder->status == 'Pending')){
                    $actions = '<div class="centered">'.$actionBtn.'</div>';
                }else{
                    $actions = '';
                }

                $data[] = [
                        "tp_order_id"     => $failedOrder->tp_order_id,
                        "channel"         => $failedOrder->channel->name,
                        "created_at"      => $failedOrder->created_at,
                        "error"           => $failedOrder->error,
                        "tp_order_date"   => $failedOrder->tp_order_date,
                        "resolved_by"     => $name,
                        "actions"         => $actions,
                        "status"          => $failedOrder->status,
                        "order_id"        => $failedOrder->order_id,
                  ];
            }
        }
        return json_encode(array("data" => $data));
    }
}
