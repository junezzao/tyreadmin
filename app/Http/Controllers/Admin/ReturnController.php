<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Form;
use Log;

class ReturnController extends AdminController
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:view.return', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.return', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.return', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.return', ['only' => ['destroy']]);

        $this->admin = \Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_id=null)
    {
        $reasons = config('globals.reject.reasons');
        return view('admin.returns.list', ['channel_id'=>$channel_id,'reasons'=>$reasons]);
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
        $action = $request->input('action', '');
        $return = json_decode($this->getGuzzleClient(array(), 'fulfillment/return/' . $id)->getBody()->getContents());
        $reasons = config('globals.reject.reasons');
        $reason = $request->input('remark','');
        if(is_numeric($reason)) $reason = $reasons[$reason];

        if (strcasecmp($return->status, 'In Transit') == 0) {
            $status = (strcasecmp($action, 'restock') == 0) ? 'Restocked' : ((strcasecmp($action, 'reject') == 0) ? 'Rejected' : 'In Transit');
            $response = $this->putGuzzleClient(array('status' => $status,'remark'=>$reason, 'completed_at'=>Carbon::now()->format('Y-m-d H:i:s')), 'fulfillment/return/' . $id);

            if ($response->getStatusCode() == 200) {
                flash()->success('Order item ' . $return->order_item_id . ' from order ' . $return->order_id . ' has been ' . $action . 'ed.');
            }
            else {
                flash()->error('Fail to process ' . $action . '.');
            }

            return redirect()->route('admin.fulfillment.return.index');
        }
        else {
            flash()->error('Only returns that are "In Transit" can be restocked or rejected.');
            return back();
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
    }

    public function search(Request $request) {
        $returnsList = json_decode($this->postGuzzleClient($request->all(), 'fulfillment/return/search')->getBody()->getContents());

        $data = array();

        foreach ($returnsList as $return) {
            $editURL = route('admin.fulfillment.return.update', [$return->id]);
            
            $restockBtn = Form::open(array('url'=> $editURL, 'method' => 'put', 'class' => 'form-inline')) . Form::hidden('action', 'restock') . '<button type=submit class="restock btn btn-link no-padding">' . trans('admin/fulfillment.button_restock') . '</button>' . Form::close();
            $rejectBtn = Form::open(array('url'=> $editURL, 'method' => 'put', 'class' => 'form-inline')) . Form::hidden('action', 'reject') . '<button type="button" data-id="'.$return->id.'" class="btn btn-link no-padding btn-reject" rel="popover">'.trans('admin/fulfillment.button_reject').'</button>'. Form::close();

            $columns = array(
                'hubwire_sku'   => $return->hubwire_sku,
                'item_name'     => ($return->quantity > 1) ? ($return->product_name . ' (' . $return->quantity . ')') : $return->product_name,
                'order_id'      => $return->order_id,
                'created_at'    => $return->created_at,
                'completed_at'  => $return->completed_at,
                'status'        => $return->status
            );

            if($this->admin->can('edit.return')) {
                $columns['actions'] = $restockBtn . ' | ' . $rejectBtn;
            } else {
                $columns['actions'] = '';
            }

            $data[] = $columns;
        }

        return json_encode(array("data" => $data));
    }

    public function reject($id)
    {
        $reasons = config('globals.reject.reasons');
        return view('admin.returns.reject', ['id'=>$id,'reasons' => $reasons]);
    }
}
