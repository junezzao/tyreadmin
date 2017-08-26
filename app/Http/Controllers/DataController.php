<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DataRepositoryContract as DataRepository;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Excel;

class DataController extends Controller
{
    use GuzzleClient;

    protected $user;

    public function __construct(DataRepository $dataRepo)
    {
        $this->dataRepo = $dataRepo;
        $this->user = \Auth::user();
    }

    public function index()
    {
        $data = array();
        $data['templateUrl'] = 'templates/data.xlsx';
        $data['templateFileName'] = 'pro_tyre_admin_excel_template';

        $data['sheet'] = json_decode($this->getGuzzleClient(array(), 'data/sheet/'.$this->user->id)->getBody()->getContents(), true);

        return view('data.index', $data);
    }

    public function loadSheetRemarks()
    {
        $remarks = json_decode($this->getGuzzleClient(array(), 'data/sheet/'.$this->user->id)->getBody()->getContents())->remarks;

        $data = array();
        foreach($remarks as $index=>$remark) {
            $line['no']       = $index + 1;
            $line['remark']   = $remark;
            $data[]           = $line;
        }
    
        return json_encode(["data" => $data]);
    }

    public function show($id)
    {
    }

    public function store(Request $request)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy(Request $request, $id)
    {
    }

    public function downloadTemplate(Request $request)
    {
        $link = $request->get('link');
        $filename = $request->get('filename');
        $ext = 'xlsx';

        if (($handle = fopen($link, "r")) === FALSE) 
            return null;
        
        header('Content-Type: application/' . $ext);
        header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
        header('Pragma: no-cache');
        readfile($link);
    }

    // data sheet upload functions
    public function upload(Request $request)
    {
        if($request->hasFile('data_sheet')){
            set_time_limit(300);

            $file = $request->file('data_sheet');
            $return = array();

            // check file extension
            $allowed = array('xlsx');
            $extension = $file->getClientOriginalExtension();
            if(!$file->isValid() || !in_array($extension, $allowed))
            {
                $return['error']['messages'][] = 'File is invalid! <b>*.'.$extension.'</b>';
            }

            // check file size (MB)
            $fileSize = 5;
            if(($file->getClientSize() / 1024 / 1024) > $fileSize) {
                $return['error']['messages'][] = 'File size must not exceed <b>'.$fileSize.' MB</b>.';
            }

            if(isset($return['error'])) {
                $return['success'] = false;
                return json_encode($return);
            }

            $path = $file->getRealPath();
            $sheetName = 'Sheet1';

            $data = Excel::selectSheets($sheetName)->load($path, function($reader) {
                $reader->setDateColumns(array(
                    'date'
                ))->get();
            })->get();

            $postData = array();
            $postData['filename'] = $file->getClientOriginalName();
            $postData['items'] = array();

            $count = 1;
            if(!empty($data) && $data->count()){
                foreach ($data->toArray() as $key => $v) {
                    if(!empty($v)){
                        // \Log::info('Line '.$count++.'... '.print_r($v, true));

                        $postData['items'][] = array(
                            'line_number'           => trim($v['ref']),
                            // 'jobsheet_date'      => !empty($v['date']) ? $v['date']->format('Y-m-d') : '',
                            'jobsheet_date'         => (array)$v['date'],
                            'jobsheet_no'           => trim($v['jobsheet_no']),
                            'inv_no'                => trim($v['invoice_no']),
                            'inv_amt'               => $v['invoice_amount'],
                            'jobsheet_type'         => trim(strtoupper($v['yardbreakdown'])),
                            'customer_name'         => trim($v['customer']),
                            'truck_no'              => trim($v['truck']),
                            'pm_no'                 => trim($v['pm']),
                            'trailer_no'            => trim($v['trailer']),
                            'odometer'              => trim($v['odometer']),
                            'position'              => trim($v['position']),
                            'in_attr'               => trim(strtoupper($v['tyre_in_attribute'])),
                            'in_price'              => $v['tyre_in_price'],
                            'in_size'               => trim($v['tyre_in_size']),
                            'in_brand'              => trim($v['tyre_in_brand']),
                            'in_pattern'            => trim($v['tyre_in_pattern']),
                            'in_retread_brand'      => trim($v['tyre_in_retread_brand']),
                            'in_retread_pattern'    => trim($v['tyre_in_retread_pattern']),
                            'in_serial_no'          => trim($v['tyre_in_serial_no']),
                            'in_job_card_no'        => trim($v['tyre_in_job_card_no']),
                            'out_reason'            => trim($v['tyre_out_reason']),
                            'out_size'              => trim($v['tyre_out_size']),
                            'out_brand'             => trim($v['tyre_out_brand']),
                            'out_pattern'           => trim($v['tyre_out_pattern']),
                            'out_retread_brand'     => trim($v['tyre_out_retread_brand']),
                            'out_retread_pattern'   => trim($v['tyre_out_retread_pattern']),
                            'out_serial_no'         => trim($v['tyre_out_serial_no']),
                            'out_job_card_no'       => trim($v['tyre_out_job_card_no']),
                            'out_rtd'               => $v['tyre_out_rtd']
                        );
                    }
                } 
            }

            // check sheet or number of data
            if(count($postData['items']) <= 0 || count($postData['items']) == 1 && empty($postData['items'][0]['line_number'])) {
                $return['success'] = false;
                $return['error']['messages'][] = 'No data detected. Please make sure uploaded file contains <b>'.$sheetName.'</b> with data.';
                return json_encode($return);
            }

            if($this->user->category == 'Lite' && count($postData['items']) > 10) {
                return ['success'=>false, 'exceed_limit'=>true];
            }
            
            $itemChunks = array_chunk($postData['items'], 1000, true);
            
            \DB::beginTransaction();
            $chunkSize = count($itemChunks);
            foreach($itemChunks as $index=>$itemChunk) {
                $postData['items'] = $itemChunk;
                $postData['index'] = $index;
                $postData['chunkSize'] = $chunkSize;

                $response = $this->dataRepo->create($postData);
                //$response = json_decode($this->postGuzzleClient($postData, 'data')->getBody()->getContents(), true);
            }
            \DB::commit();
            
            return ['success'=>true];
        }
    }

    public function list(Request $request) {
        $data = array();
        $data['start'] = $request->input('start');
        $data['length'] = $request->input('length');
        $data['draw'] = $request->input('draw');
        $data['order'] = $request->input('order');
        $data['columns'] = $request->input('columns');
        $data['search'] = $request->input('search');

        $results = json_decode($this->getGuzzleClient($data, 'data/'.$this->user->id)->getBody()->getContents(), true);

        $return['draw'] = $data['draw'];
        $return['recordsTotal'] = $return['recordsFiltered'] = $results['total'];
        $return['data'] = $results['data'];

        return json_encode($return);
    }

    public function printDiagnostic() {
        $data = array();

        $data['sheet'] = json_decode($this->getGuzzleClient(array(), 'data/sheet/'.$this->user->id)->getBody()->getContents(), true);

        return view('data.diagnostic', $data);
    }
}
