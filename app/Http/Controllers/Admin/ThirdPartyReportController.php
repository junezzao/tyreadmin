<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use App\Repositories\Contracts\OrderRepository;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Carbon\Carbon;
use PDF;
use Helper;

class ThirdPartyReportController extends AdminController
{
    use GuzzleClient;

    protected $admin;
    protected $orderRepo;

    public function __construct(OrderRepository $orderRepo) {
        $this->admin = \Auth::user();
        $this->orderRepo = $orderRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        $data['discard'] = ["Selected"];
        $data['verify'] = ["Selected"];
        $data['moveTo'] = ["Paid by Marketplace", "Completed"];
        $data['remarks'] = config('globals.third_party_report_remarks');
        $data['optionsPendingTPPayment'] = ["Selected", "Tax Invoice Data"];
        $data['optionsPendingPaymentToMerchant'] = config('globals.tp_reports_export_options');
        $data['optionsComplete'] = ["Selected", "Tax Invoice Data"];
        $data['merchants'] = array();

        $data['channelTypes'] = array();
        $data['statuses'] = config('globals.tp_reports_item_status');
        $data['numVerifiedItems'] = $this->verifiedItemsCount();
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants')->getBody()->getContents())->merchants;
        foreach ($merchants as $merchant) {
            $data['merchants'][$merchant->id] = $merchant->name;
        }

        $channel_types = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type')->getBody()->getContents());
        foreach($channel_types as $channel_type) {
            $data['channelTypes'][$channel_type->id] = $channel_type->name;
        }

        $data['channels']['retail'] = "Retail Store";
        $data['channels']['online'] = "Online Store";

        return view('admin.reports.third_party.index', $data);
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
        $tp_details = json_decode($this->getGuzzleClient(array(), 'reports/third_party/show/'.$id)->getBody()->getContents());
        if(isset($tp_details->item->order->status))
            $tp_details->item->order->status = $this->orderRepo->getStatus($tp_details->item->order->status);
        if(!empty($tp_details->logs)){
            $groupedHistory = array();
            foreach($tp_details->logs as $log){
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $log->created_at)->format('j M. Y');
                $groupedHistory[$date][] = $log;
            }

        }
        $paid_status = (isset($tp_details->item->paid_status)&&$tp_details->item->paid_status==1)?"Paid":"Unpaid";
        return view('admin.reports.third_party.show', compact('tp_details', 'groupedHistory', 'paid_status'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tp_details = json_decode($this->getGuzzleClient(array(), 'reports/third_party/show/'.$id)->getBody()->getContents());
        if(isset($tp_details->item->order->status))
            $tp_details->item->order->status = $this->orderRepo->getStatus($tp_details->item->order->status);
        $paymentStatusList = ['Unpaid' => 'Unpaid', 'Pending' => 'Pending', 'Paid' => 'Paid'];
        $paid_status = (isset($tp_details->item->paid_status)&&$tp_details->item->paid_status==1)?"Paid":"Unpaid";
        $tpPaymentStatusList = ['0' => 'Unpaid', '1' => 'Paid'];
        $tpOrderStatus = ['Verified' => 'Verified', 'Cancelled' => 'Cancelled', 'Returned' => 'Returned'];
        $bankList = config('globals.bank_list');
        $paymentMethodList = config('globals.payment_methods');
        $currencyList = config('globals.currency_list');

        return view('admin.reports.third_party.edit', compact('tp_details', 'paymentStatusList', 'tpPaymentStatusList','tpOrderStatus', 'bankList', 'paymentMethodList', 'currencyList', 'paid_status'));
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
        $this->validate($request, [
            // rules
            //'order_item_id' => 'sometimes|required',
            'arc_unit_price' => 'sometimes|required|min:0',
            'tp_unit_price' => 'required|min:0',
            'arc_sale_price' => 'sometimes|required|min:0',
            'tp_sale_price' => 'required|min:0',
            'arc_sold_price' => 'sometimes|required|min:0',
            'tp_sold_price' => 'required|min:0',
            'tp_item_status' => 'required',
            'tax' => 'sometimes|required|min:0',
            'hw_fee' => 'required|min:0',
            'hw_commission' => 'required|min:0',
            'misc_fee' => 'required|min:0',
            'min_guarantee' => 'required|min:0',
            //'item_channel_fee' => 'required|min:0',
            //'channel_mg' => 'required|min:0',
            'net_payout_currency' => 'required',
            'net_payout' => 'required',
            'channel_fee' => 'required',
            'channel_shipping_fees' => 'required',
            'channel_payment_gateway_fees' => 'required',
            'merchant_payout_status' => 'required',
        ]);

        $data = array();
        $data['order_item_id'] = $request->input('order_item_id');
        $data['third_party_payment'] = array(
            'net_payout' => $request->input('net_payout'),
            'net_payout_currency' => $request->input('net_payout_currency'),
            'payment_date' => $request->input('payment_date'),
            'paid_status' => $request->input('paid_status'),
            'tp_payout_ref' => $request->input('tp_payout_ref'),
            'channel_fees' => $request->input('channel_fee'),
            'channel_shipping_fees' => $request->input('channel_shipping_fees'),
            'channel_payment_gateway_fees' => $request->input('channel_payment_gateway_fees'),
        );
        $data['merchant_payment'] = array(
            'merchant_payout_amount' => $request->input('merchant_payout_amount'),
            'merchant_payout_currency' => $request->input('merchant_payout_currency'),
            'merchant_payout_status' => $request->input('merchant_payout_status'),
            'hw_payout_bank' => $request->input('hw_payout_bank'),
            'merchant_payout_date' => $request->input('merchant_payout_date'),
            'merchant_payout_ref' => $request->input('merchant_payout_reference'),
            'merchant_bank' => $request->input('merchant_bank'),
            'merchant_payout_method' => $request->input('merchant_payment_method'),
            'merchant_invoice_no' => $request->input('merchant_invoice_no')
        );
        $data['third_party_item_details'] = array(
            'unit_price'    => $request->input('tp_unit_price'),
            'sale_price'    => $request->input('tp_sale_price'),
            'sold_price'    => $request->input('tp_sold_price'),
            'item_status'   => $request->input('tp_item_status')
        );
        $data['hubwire_item_details'] = array(
            'unit_price'    => $request->input('arc_unit_price'),
            'sale_price'    => $request->input('arc_sale_price'),
            'sold_price'    => $request->input('arc_sold_price'),
            'tax'           => $request->input('tax'),
            'min_guarantee' => $request->input('min_guarantee'),
            'channel_fee'   => $request->input('item_channel_fee'),
            'channel_mg'    => $request->input('channel_mg'),
        );

        $data['hubwire_fee'] = array(
            'hw_fee' => $request->input('hw_fee'),
            'hw_commission' => $request->input('hw_commission'),
            'misc_fee' => $request->input('misc_fee')
        );

        $data['remark'] = trim($request->input('remark'));

        $response = json_decode($this->postGuzzleClient($data, 'reports/third_party/update/'.$id)->getBody()->getContents());

        if ($response->success) {
            $message = 'Order Item ' . $response->item->id.' was successfully updated.';
            flash()->success($message);
        } else {
            $error = array();
            foreach($response->errors as $errorField) {
                foreach($errorField as $errorMsg) {
                    $error[] = $errorMsg;
                }
            }
            flash()->error(json_encode(implode('<br>', $error)));
            return back()->withInput();
        }
        return redirect()->route('admin.tp_reports.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ids)
    {
        if (!is_array($ids)) {
           $ids = [$ids];
        }
        foreach ($ids as $id) {
            $response = json_decode($this->deleteGuzzleClient(array(), 'reports/third_party/destroy/'.$id)->getBody()->getContents());
            if ($response->success) {
                continue;
            } else {
                flash()->error($response->message);
                return redirect()->route('admin.tp_reports.show', $id);
            }
        }
        $message = 'Third party record(s) was successfully deleted.';
        flash()->success($message);

        return redirect()->route('admin.tp_reports.index');
    }

    public function discard(Request $request) {
        if ($request->option == 'Selected') {

            $this->destroy(json_decode($request->ids, true));
        }else{
            $data = [$request->option];
            $response = $this->postGuzzleClient($data, 'reports/third_party/discardChecking')->getBody()->getContents();
            $this->destroy(json_decode($response, true));
        }

        return redirect()->back();
    }

    public function downloadTemplate(Request $request) {
        $s3 = new MediaService();
        $link = $s3->checkFileInS3("templates/third_party_report_upload_sample.csv");

        $this->downloadFile($link, 'third_party_report_upload_sample', 'csv');
    }

    public function upload(Request $request) {
        if($request->hasFile('tp_report')){
            /*
             * Validation
             */
            $file = $request->file('tp_report');
            $return = array();

            if (!$file->isValid()) {
                $return['success'] = false;
                $return['errors'] = array($file->getErrorMessage());
                return json_encode($return);
            }

            if ($file->getClientOriginalExtension() != 'csv') {
                $return['success'] = false;
                $return['errors'] = array('Invalid file! File uploaded must be <b>*.csv</b>');
                return json_encode($return);
            }

            /*
             * Get Massaged Data
             */
            $tfile = $file->getRealPath();
            $data = $this->processThirdPartyReportData($tfile);

            if (!$data['success']) {
                return json_encode($data);
            }

            /*
             * Upload file to S3
             */
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            $fileName = 'third_party_report_' . str_replace([' ', ':', '-'], '_', Carbon::now()->toDateTimeString());

            $mediaService = new MediaService();
            $uploadResponse = $mediaService->uploadFile($file, 'tp_report', ['csv'], 10000, $fileName, 'reports/third_party/uploads/' . $year . '/' . $month);
            // $uploadResponse = $mediaService->uploadFileToS3($tfile, 'reports/third_party/uploads/' . $year . '/' . $month . '/' . $fileName);

            if (!empty($uploadResponse->errors)) {
                $return['errors'] = is_array($uploadResponse->errors) ? $uploadResponse->errors : array($uploadResponse->errors);
                $return['success'] = false;
                return json_encode($return);
            }

            /*
             * Send to HAPI for processing
             */
            $response = json_decode($this->postGuzzleClient(array('data' => $data['data'], 'media' => $uploadResponse), 'reports/third_party/process')->getBody()->getContents(), true);

            if ($response['success']) {
                $return['messages'][] = 'Total order items uploaded: ' . $response['counts']['uploaded'];
                $return['messages'][] = 'Total order items matched: ' . $response['counts']['matched'];

                if ($response['counts']['order_not_found'] > 0) $return['messages'][] = 'Total orders not found in Arc: ' . $response['counts']['order_not_found'];
                if ($response['counts']['item_not_found'] > 0) $return['messages'][] = 'Total order items not found in Arc: ' . $response['counts']['item_not_found'];
                if ($response['counts']['discrepancies'] > 0) $return['messages'][] = 'Total order items with discrepancies: ' . $response['counts']['discrepancies'];

                if (count($response['duplicates']['paid_status_updated']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['paid_status_updated']) . ' item(s)\'s paid status has been updated to paid:<br><ul><li>' . implode('</li><li>', $response['duplicates']['paid_status_updated']) . '</li></ul>';
                if (count($response['duplicates']['channel_fees_updated']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['channel_fees_updated']) . ' item(s)\'s channel fees have been updated: <br><ul><li>' . implode('</li><li>', $response['duplicates']['channel_fees_updated']) . '</li></ul>';
                if (count($response['duplicates']['channel_shipping_fees_updated']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['channel_shipping_fees_updated']) . ' item(s)\'s shipping fees have been updated: <br><ul><li>' . implode('</li><li>', $response['duplicates']['channel_shipping_fees_updated']) . '</li></ul>';
                if (count($response['duplicates']['channel_payment_gateway_fees_updated']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['channel_payment_gateway_fees_updated']) . ' item(s)\'s payment gateway fees have been updated: <br><ul><li>' . implode('</li><li>', $response['duplicates']['channel_payment_gateway_fees_updated']) . '</li></ul>';
                if (count($response['duplicates']['net_payout_updated']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['net_payout_updated']) . ' item(s)\'s net payout fees have been updated: <br><ul><li>' . implode('</li><li>', $response['duplicates']['net_payout_updated']) . '</li></ul>';
                if (count($response['duplicates']['verified']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['verified']) . ' item(s) have been uploaded before and will not be uploaded again:<br><ul><li>' . implode('</li><li>', $response['duplicates']['verified']) . '</li></ul>';
                if (count($response['duplicates']['cancelled_returned']) > 0) $return['messages'][] = 'The following ' . count($response['duplicates']['cancelled_returned']) . ' item(s) was previously cancelled/returned and will not be uploaded again:<br><ul><li>' . implode('</li><li>', $response['duplicates']['cancelled_returned']) . '</li></ul>';
            }
            else {
                $return['errors'] = (!empty($response['errors'])) ? $response['errors'] : array('There was a problem processing the data. Please try again later.');
            }

            $return['success'] = $response['success'];
            return json_encode($return);
        }
    }

    public function processThirdPartyReportData($filePath) {
        $return['success'] = true;
        $return['errors'] = '';
        $return['data'] = array();

        ini_set('auto_detect_line_endings', true);

        if(!file_exists($filePath) || !is_readable($filePath)) {
            $return['success'] = false;
            $return['errors'][] = 'Problem processing file.';
            return $return;
        }

        $headers = config('csv.tp_report_headers');
        $map = config('csv.tp_report_headers_map');

        $data = array();

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            $headerRow = fgetcsv($handle, 1000, ',');
            $headerRow = array_filter($headerRow, 'strlen');

            if(count($headerRow) != count($headers)) {
                $return['success'] = false;
                $return['errors'][] = 'Invalid template format. Please use the template provided.';
                return $return;
            }
            else {
                foreach ($headerRow as $key => $value) {
                    if ($value != $headers[$key]) {
                        $return['success'] = false;
                        $return['errors'][] = 'Invalid template format. Please use the template provided.';
                        return $return;
                    }
                }
            }

            $cols = array_values($map);
            $index = 2;
            while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {
                // check row empty
                $chk = array_filter($row, 'strlen');
                $chk2 = array_filter($chk, 'trim');
                if(empty($chk2)){
                    continue;
                }

                $row = array_map('trim', array_slice($row, 0, count($cols)));
                $data[$index] = array_combine($cols, $row);
                $index++;
            }

            fclose($handle);

            $return['data'] = $data;
        }
        else {
            $return['success'] = false;
            $return['error'] = 'There was a problem opening the file.';
        }

        return $return;
    }

    public function search(Request $request) {
        $response = json_decode($this->getGuzzleClient($request->all(), 'reports/third_party/search')->getBody()->getContents());

        if (isset($response->records) && !empty($response->records)) {
            foreach($response->records as $item) {

                // add tooltip next to checkbox if there are any remarks
                $tooltip  = (isset($item->remarks) && !empty($item->remarks)) ?
                                "&nbsp; <i>
                                            <span class='glyphicon glyphicon-info-sign'
                                                  data-toggle='tooltip'
                                                  data-placement='right'
                                                  data-html='true'
                                                  title='Remarks: <br/>$item->remarks'>
                                            </span>
                                        </i>"
                                : '';

                $item->checkbox = \Form::checkbox('item', $item->id) . $tooltip;
                $item->actions = '';
                $item->mode = '';

                $item->id = '<a href="'.route('admin.tp_reports.show', ['id' => $item->id]).'" target="_blank">'.$item->id.'</a>';

                // columns that are shown/hidden dynamically
                $item->tp_order_date = (isset($item->tp_order_date) && $item->tp_order_date != '0000-00-00 00:00:00') ? Helper::convertTimeToUserTimeZone($item->tp_order_date, $this->admin->timezone) : '';
                $item->shipped_date = (isset($item->shipped_date)) ? Helper::convertTimeToUserTimeZone($item->shipped_date, $this->admin->timezone, 'Y-m-d') : '';

                if (!isset($item->last_attended_by)) $item->last_attended_by = '';
                if (!isset($item->channel_type)) $item->channel_type = '';
                if (!isset($item->merchant_name)) $item->merchant_name = '';
                if (!isset($item->status)) $item->status = '';

                // convert date
                $item->created_at = Helper::convertTimeToUserTimeZone($item->created_at, $this->admin->timezone);
                $item->updated_at = Helper::convertTimeToUserTimeZone($item->updated_at, $this->admin->timezone);
            }
        }

        $data['data'] = $response->records;
        $data['draw'] = $request->get('draw');
        $data['recordsTotal'] = $response->recordsTotal;
        $data['recordsFiltered'] = $response->recordsFiltered;
        $data['counters'] = $response->counters;

        return response()->json($data);
    }

    public function counters(Request $request) {
        $response = json_decode($this->getGuzzleClient($request->all(), 'reports/third_party/counters')->getBody()->getContents());
        return response()->json($response);
    }

    public function completeVerifiedOrderItems(Request $request) {
        $response = json_decode($this->postGuzzleClient([$status=$request->status], 'reports/third_party/complete_verified_order_items')->getBody()->getContents());
        return response()->json($response);
    }

    public function verify($id)
    {
        $response = json_decode($this->postGuzzleClient([], 'reports/third_party/verify/'.$id)->getBody()->getContents());
        if(!empty($response->error)){
            flash()->error('Unable to verify the report. Details as below: <br/>' . json_encode($response->error));
        }else{
            flash()->success('Report verified successfully.');
        }

        return response()->json($response);
    }

    public function bulk_verify(Request $request)
    {
        if ($request->option == 'Selected')
        {
            $ids = json_decode($request->ids);
            foreach($ids as $id)
            {
                $this->verify($id);
            }
        }

        return redirect()->route('admin.tp_reports.index');
    }

    public function bulk_moveTo(Request $request)
    {
        $data['option'] = $request->option;
        $data['ids'] = $request->ids;
        $response = json_decode($this->postGuzzleClient($data, 'reports/third_party/bulk_moveTo')->getBody()->getContents(), true);
        $message = '';
        foreach ($response['create'] as $createId => $create) {
            $message .= '- Order Item Id('.$createId.') record ';
            $message .= $create ? 'was successfully move to '.$request->option.'.<br/>' : 'cannot move.<br/>';
        }
        flash()->message($message);

        return redirect()->route('admin.tp_reports.index');
    }

    public function print($id)
    {
        $tp_details = json_decode($this->getGuzzleClient(array(), 'reports/third_party/show/'.$id)->getBody()->getContents());
        $tp_details->item->order->status = $this->orderRepo->getStatus($tp_details->item->order->status);
        //$pdf = PDF::loadView('admin.reports.third_party.show_print', compact('tp_details'));
        //return $pdf->stream();
        return view('admin.reports.third_party.show_print', compact('tp_details'));
    }

    public function export(Request $request)
    {
        $response = json_decode($this->postGuzzleClient($request->all(), 'reports/third_party/export')->getBody()->getContents());
        //dd($response);
        // Create csv file
        if ($response->success) {
            $orderItems = $response->data;
            $delimiter = ',';
            foreach (config('globals.tax_invoice_file_name') as $index => $name) {
                if ($request->input('tab')==$index) {
                    $file = $name;
                }
            }
            $filename = $request->input('option')."_$file.csv";

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Pragma: no-cache');

            $fp = fopen('php://output', 'w');
            $headers = array_keys((array)$orderItems[0]);
            fputcsv($fp, $headers, $delimiter);
            foreach ($orderItems as $fields) {
                $fields = array_values((array)$fields);
                fputcsv($fp, $fields, $delimiter);
            }
            fclose($fp);
        }

        else {
            flash()->error($response->message);
            return redirect()->back();
        }
    }

    public function verifiedItemsCount() {
        return json_decode($this->getGuzzleClient([], 'reports/third_party/num_verified_items')->getBody()->getContents());
    }

    public function resolveRemark($remarkId) {
        $response = json_decode($this->postGuzzleClient([], 'reports/third_party/remark/'.$remarkId.'/resolve')->getBody()->getContents());
        return response()->json($response);
    }

    public function addRemark(Request $request, $id) {
        $data['id'] = $id;
        $data['remark'] = $request->input('remark');
        $data['userId'] = $this->admin->id;

        $response = json_decode($this->postGuzzleClient($data, 'reports/third_party/'.$id.'/addRemark')->getBody()->getContents());
        return response()->json($response);
    }

    public function generateReport(Request $request) {
        $data['merchant'] = !is_null($request->input('merchant')) ? $request->input('merchant') : '';
        $data['channel'] = !is_null($request->input('channel')) ? $request->input('channel') : '';
        $data['month'] = $request->input('month');
        //dd($data);
        $response = json_decode($this->postGuzzleClient($data, 'reports/third_party/generateReport')->getBody()->getContents());
        if ($response = 'Processing') {
           flash()->success("The report is currently being generated. An email with the link to download the report will be sent to \'reports@hubwire.com\' once the report is completed. The process should take approximately 15-20 minutes");
        }
        return redirect()->route('admin.tp_reports.index');
    }
}
