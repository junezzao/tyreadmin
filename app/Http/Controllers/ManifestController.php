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
use App\Http\Controllers\Admin\ManifestController as MainController;

/**
**  Goods Stock Takeout - Manifest
**/

class ManifestController extends MainController
{
    use GuzzleClient;

    protected $admin;
    protected $users;
    protected $channels;
    protected $merchants;
    protected $merchantList;
    protected $channelList;
    protected $userList;

    
    public function index()
    {
        $data = [];
        return view('product-management.manifests.index', $data);
    }

    // search picking manifests
    public function search(Request $request,$type = 1) {
        $request->merge(['type'=>$type]);
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
                    $assignUrl = route('products.manifests.assignUser', [$m->id]);
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

                $viewButton = '<a href="'.route('products.manifests.show', [$m->id]).'" class="btn btn-sm">'.trans('admin/fulfillment.manifest_btn_view').'</a>';
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $response = json_decode($this->getGuzzleClient(array('type'=>1), 'fulfillment/manifest/'.$id)->getBody()->getContents());
        $data = array();
        $data['user'] = $this->user;
        $data['manifest_id'] = $id;
        // $data['manifest'] = $response;
        $data['readyToComplete'] = $response->readyToComplete;
        $data['manifest_status'] = $response->manifest_status;
        $data['manifest']       = $response->manifest;

        return view('product-management.manifests.show', $data);
    }

    public function pickUpManifest(Request $request)
    {
        $request->merge(['type'=>1]);  
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/pickup')->getBody()->getContents());
        return response()->json($response);
    }

    // show picking items
    public function pickingItems($id) {
        $items = json_decode($this->getGuzzleClient(array('type'=>1), 'fulfillment/manifest/'.$id.'/items')->getBody()->getContents());
        $data['data'] = array();
        foreach($items as $item) {
            
            if($item->picked != $item->quantity)
                $status = 'Picking';
            else
                $status = 'Picked';
            
            
            $data['data'][] = array(
                "hubwire_sku" => $item->hubwire_sku,
                "product_name" => $item->name,
                "coordinates" => $item->channel_sku_coordinates,
                // "order_no" => "<a target='_blank' href='".route('order.show', $item->item->do_id)."'>".$item->item->do_id."</a>",
                "item_id" => $item->id,
                // "tp_order_date" => Helper::convertTimeToUserTimezone($item->item->order->tp_order_date, $this->user->timezone),
                "status" => '<span class="picking">'.$status.' ('.$item->picked.'/'.$item->quantity.')</span>',
                "actions" => '',
                "sku_id" => $item->sku_id,
                "picking_status" => $item->picked.'/'.$item->quantity,
                "picked" => $item->picked,
                "total" => $item->quantity 
            );
        } 
        
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    // function for handling scanning hubwire sku
    public function pickItem(Request $request, $id) {  
        $request->merge(['type'=>1]);  
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/'.$id.'/items/pick')->getBody()->getContents());
        return response()->json($response);
    }

    // to inform the system that the picking item is out of stock
    public function outOfStock(Request $request, $id) {
        $request->merge(['type'=>1]);  
        $response = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/manifest/'.$id.'/outofstock')->getBody()->getContents());
        return response()->json($response);
    }

    // Mark manifest as complete
    public function completed($id) {
        $response = json_decode($this->postGuzzleClient(['type'=>1], 'fulfillment/manifest/'.$id.'/completed')->getBody()->getContents());
        
        return response()->json($response);
    }

    // Cancel the manifest
    public function cancel($id) {
        $response = json_decode($this->postGuzzleClient(['type'=>1], 'fulfillment/manifest/'.$id.'/cancel')->getBody()->getContents());
        return response()->json($response);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    public function export($id)
    {
        $response = json_decode($this->getGuzzleClient(array('type'=>1), 'fulfillment/manifest/'.$id.'/orders')->getBody()->getContents());
        $stock_transfer_id = $response[0]->id;
        return redirect()->route('products.stock_transfer.export',['id'=>$stock_transfer_id]);

    }

    public function assignUser(Request $request, $id)
    {
        $request->merge(['type'=>1]);
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
