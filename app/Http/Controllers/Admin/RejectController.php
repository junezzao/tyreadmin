<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Traits\GuzzleClient;

class RejectController extends AdminController
{
    use GuzzleClient;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_id=null)
    {
        //$response = json_decode($this->getGuzzleClient([], 'admin/reject')->getBody()->getContents());dd($response->rejects[123]);
        return view('admin.rejects.list', ['channel_id'=>$channel_id]);
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

    public function getTableData ()
    {
        $channel_id = request()->get('channel_id', null);
        $response = json_decode($this->getGuzzleClient(request()->all(), 'admin/reject')->getBody()->getContents());
        //dd($response->rejects);
        $data = array();
        if(!empty($response)){
            foreach ($response->rejects as $reject) {
                if(is_null($reject->sku->product_name)){
                    $productName = 'N/A (Product Deleted)';
                }else{
                    $productName = $reject->sku->product_name->name;
                }
                if(is_null($reject->user)){
                    $name = 'N/A (User Deleted)';
                }else{
                    $name = $reject->user->first_name . ' ' . $reject->user->last_name;
                }
                $data[] = [
                          "id" => $reject->id,
                          "item_name" => $productName,
                          "hubwire_sku" => $reject->sku->hubwire_sku,
                          "reason" => $reject->remarks,
                          "quantity" => $reject->quantity,
                          "channel_name" => $reject->channel->name,
                          'merchant_name' => isset($reject->sku->merchant_name->name)?$reject->sku->merchant_name->name:'',
                          "user_id" => $name,
                          "rejected_at" => $reject->created_at,
                  ];
            }
        }
        //return response()->json($data);
        return json_encode(array("data" => $data));
    }
}
