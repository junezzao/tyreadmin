<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrdersController;
use App\Http\Traits\GuzzleClient;
use App\Helpers\Helper;
use Config;
use App\Models\User;
use Illuminate\Support\MessageBag;
use PDF;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;


class ManifestController extends Controller
{
    use GuzzleClient;

    protected $user;
    protected $ordersController;

    public function __construct()
    {
        $this->user = \Auth::user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['channelTypes'] = json_decode($this->getGuzzleClient([], 'channels/admin/channel_type/get_manifest_active_channels')->getBody()->getContents());
        return view('admin.manifests.index', $data);
    }


    // search picking manifests
    public function search(Request $request,$type = 0) {
        $request->merge(['type'=>$type]);
        // \Log::info(print_r($request->all(), true));
        $response = json_decode($this->getGuzzleClient($request->all(), 'fulfillment/manifest/search')->getBody()->getContents());
        $data['draw'] = $request->get('draw');
        $data['recordsTotal'] = $response->recordsTotal;
        $data['recordsFiltered'] = $response->recordsFiltered;
        $data['data'] = array();

        $WEs = User::WE();
        $weDropdown = '<span class="weDropdown"><select name="weUser" class="form-control select2 we-dropdown">';
        foreach($WEs as $we){
            $weDropdown .= '<option value="'.$we['id'].'">'.$we['first_name'].'</option>';
        }
        $weDropdown .= '</select></span>';
        $spinner = '<span class="pm-spinner"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-aqua"></i></span>';

        if (isset($response->manifests)) {
            // \Log::info(print_r($response->manifests,true));
            foreach($response->manifests as $m) {
                // assign.pickingmanifestuser
                if($this->user->can('assign.pickingmanifestuser') && $m->status != 'Completed'){
                    $assignUrl = route('admin.fulfillment.manifests.assignUser', [$m->id]);
                    if($m->user_id == 0){
                        $adminName = '<a class="assign-user-link" data-url="'.$assignUrl.'" data-user-id="'.$m->user_id.'" data-id="'.$m->id.'" href="#">Unassigned</a>   '.$spinner.$weDropdown;
                    }else{
                        $adminName = '<a class="assign-user-link" data-url="'.$assignUrl.'" data-user-id="'.$m->user_id.'" data-id="'.$m->id.'" href="#">'.$m->user_name.'</a>   '.$spinner.$weDropdown;
                    }
                }else{
                    if($m->user_id == 0){
                        $adminName = 'Unassigned';
                    }else{
                        $adminName = $m->user_name;
                    }
                }

                $viewButton = '<a href="'.route('admin.fulfillment.manifests.show', [$m->id]).'" class="btn btn-sm">'.trans('admin/fulfillment.manifest_btn_view').'</a>';
                // pick up manifest
                $pickUpButton = '<button class="btn btn-xs pick-up" data-id='.$m->id.'>'.trans('admin/fulfillment.manifest_btn_pick_up').'</button>';
                $tooltip = '';
                if (!empty($m->priority)) {
                    $channelTypes = explode(", ", $m->priority);
                    $channelTypeString = '';
                    foreach($channelTypes as $type) {
                        $channelTypeString .= config("globals.channel_type.$type"). ', ';
                    }
                    $channelTypeString = substr($channelTypeString, 0, -2);

                    $tooltip = "&nbsp;<i><span class='glyphicon glyphicon-info-sign' data-toggle='tooltip' data-placement='bottom' title='Priority Channel Types: $channelTypeString'></span></i>";
                }

                $data['data'][] = array(
                    "id" => (empty($m->priority))? $m->id: $m->id . $tooltip,
                    "status" => $m->status,
                    "admin_name" => $adminName,
                    "pickup_date" => ($m->user_id!=0)?$m->pickup_date:'',
                    "created_at" => $m->created_at,
                    "creator_name" => $m->creator_name,
                    "updated_at" => ($m->status=="Completed")?$m->updated_at:'',
                    "actions" => ($m->status=="New")?$pickUpButton.$viewButton:$viewButton
                );
            }
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Generate picking manifest
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest')->getBody()->getContents());
        /*$return['success'] = true;
        if ($response->success){
            $return['success'] = false;
        }
        $return['response'] = $response;
        */return response()->json($response);
    }

    /**
     * Generate picking manifest
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pickUpManifest(Request $request)
    {
        //
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/pickup')->getBody()->getContents());
        /*$return['success'] = true;
        if ($response->success){
            $return['success'] = false;
        }
        $return['response'] = $response;
        */return response()->json($response);
    }

    /**
     * View picking manifest
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $response = json_decode($this->getGuzzleClient(array(), 'fulfillment/manifest/'.$id)->getBody()->getContents());
        $data = array();
        $data['manifest_id'] = $id;
        $data['readyToComplete'] = $response->readyToComplete;
        $data['manifest_status'] = $response->manifest_status;

        return view('admin.manifests.show', $data);
    }

    // show picking items
    public function pickingItems($id) {
        $items = json_decode($this->getGuzzleClient(array(), 'fulfillment/manifest/'.$id.'/items')->getBody()->getContents());
        $data['data'] = array();
        foreach($items as $item) {
            // out of stock button
            $oosButton = '';
            switch($item->status) {
                case 'Picking':
                    $oosButton = '<button class="btn btn-xs btn-danger btn-dataTable oos" data-id='.$item->id.'>'.trans('admin/fulfillment.manifest_btn_oos').'</button>';
                    break;
                case 'Out of Stock':
                    $oosButton = '<button disabled class="btn btn-danger btn-xs btn-dataTable">'.trans('admin/fulfillment.manifest_btn_oos').'</button>';
                    break;
                default:
                    break;
            }

            $data['data'][] = array(
                "hubwire_sku" => $item->hubwire_sku,
                "product_name" => $item->name,
                "coordinates" => $item->channel_sku_coordinates,
                "order_no" => "<a target='_blank' href='".route('order.show', $item->order_id)."'>".$item->order_id."</a>",
                "item_id" => $item->item_id,
                "tp_order_date" => Helper::convertTimeToUserTimezone($item->tp_order_date, $this->user->timezone),
                "status" => "<span class='".str_replace(" ", "-", strtolower($item->status))."''>".$item->status."</span>",
                "actions" => $oosButton,
                'sku_id' => $item->sku_id
            );
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    // get unique orders in a manifest
    public function getUniqueOrders($id) {
        $orders = json_decode($this->getGuzzleClient(array(), 'fulfillment/manifest/'.$id.'/orders')->getBody()->getContents());
        $data['data'] = array();

        foreach($orders as $order) {

            $data['data'][] = array(
                "id"                => "<a target='_blank' href='".route('order.show', $order->id)."'>".$order->id."</a>",
                "cancelled_status"  => $order->cancelled_status,
                "name"              => $order->name,
                "tp_order_date"     => Helper::convertTimeToUserTimezone($order->tp_order_date, $this->user->timezone),
                "status"            => $order->status,
            );
        }

        return json_encode($data, JSON_PRETTY_PRINT);
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

    // function for handling scanning hubwire sku
    public function pickItem(Request $request, $id) {
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/'.$id.'/items/pick')->getBody()->getContents());
        return response()->json($response);
    }

    // to inform the system that the picking item is out of stock
    public function outOfStock(Request $request, $id) {
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/'.$id.'/outofstock')->getBody()->getContents());
        return response()->json($response);
    }

    // Mark manifest as complete
    public function completed($id) {
        $response = json_decode($this->postGuzzleClient(array(), 'fulfillment/manifest/'.$id.'/completed')->getBody()->getContents());

        return response()->json($response);
    }

    // count number of new orders that can be picked
    public function count() {
        $response = json_decode($this->getGuzzleClient(array(), 'fulfillment/manifest/count')->getBody()->getContents());

        return response()->json($response);
    }

    public function exportPosLaju($id) {
        $response = json_decode($this->getGuzzleClient(array(), 'fulfillment/manifest/'.$id.'/export_pos_laju')->getBody()->getContents());

        // Create excel file
        if ($response->success) {
            $orders = $response->returns;
            $delimiter = ',';
            //dd($orders);
            $zipper = new \Chumper\Zipper\Zipper;
            $zipper->make(storage_path('app/est.zip'));
            $files = array();
            foreach($orders as $key => $value){
                $filename = 'pos_laju_consignment_picking_manifest_'.$key.'_'.$id.'.csv';
                $files[] = $filename;
                //header('Content-Type: application/csv');
                //header('Content-Disposition: attachment; filename="'.$filename.'";');
                //header('Pragma: no-cache');

                //$fp = fopen('php://output', 'w');
                $fp = fopen(storage_path('app/'.$filename), 'a+');
                foreach ($value as $fields) {
                    fputcsv($fp, $fields, $delimiter);
                }
                fclose($fp);
                $zipper->add(storage_path('app/'.$filename));

            }

            $zipper->close();
            foreach ($files as $file) {
                //$zipper->zip(storage_path('app/est.zip'))->add(storage_path('app/'.$file));
                unlink(storage_path('app/'.$file));
            }
            return response()->download(storage_path('app/est.zip'), 'poslaju_est_manifest_'.$id.'.zip', ['Content-Type' => 'application/csv', 'Content-Disposition' => 'attachment;filename='.storage_path('app/text.zip'), 'Pragma' => 'no-cache'])->deleteFileAfterSend(true);

        }

        else {
            flash()->error($response->message);
            return redirect()->back();
        }
    }

    public function printDocuments($id) {
        $documents = json_decode($this->getGuzzleClient(array(), 'fulfillment/manifest/'.$id.'/print_documents')->getBody()->getContents());
        return view('layouts.bulk_print', (array)$documents);
    }

    public function assignUser(Request $request, $id)
    {
        $updateResponse = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/'.$id.'/assign_user')->getBody()->getContents());

        if(empty($updateResponse->error)){
            $response = array(
                'success'   => true,
                'response'  => $updateResponse,
                );
        }else{
            $errorMsg = new MessageBag((array)$updateResponse->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
        }

        return response()->json($response);
    }
}
