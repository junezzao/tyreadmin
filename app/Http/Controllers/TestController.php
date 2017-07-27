<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\MediaService as MediaService;

use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Http\Traits\GuzzleClient;
use Input;
use Log;

class TestController extends Controller
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('role:superadministrator');
        $this->admin = \Auth::user();
    }

    public function index()
    {
        return view('test.index');
    }

    public function runSyncs() {
        $this->postGuzzleClient([], 'admin/testing/run_syncs');
        return redirect()->route('admin.testing.index');
    }

    public function getHapiLogs() {
        $log = $this->getGuzzleClient(['l' => Input::get('l', ''), 'c' => Input::get('c', '')], 'admin/testing/get_hapi_logs')->getBody()->getContents();
        return $log;
    }

    public function upload(Request $request)
    {
        //if(Input::file('testuploadfile')) {
        if ($request->hasFile('testuploadfile')) {
            $mediaService = new MediaService();
            $response = $mediaService->uploadFile($request->file('testuploadfile'), 'testuploadfile', array(), 10000);
            //$url = $mediaService->checkFile('test/tesadsadast.png');
            if (!isset($response->errors)) {
                \Log::info(print_r($response, true));
                return Redirect::to('/testupload')->with('msg', 'image uploaded');
            } else {
                return Redirect::to('/testupload')->with('msg', $response->errors);
            }
        } else {
            return view('test.uploadtest', ['msg' => 'no files uploaded']);
        }
    }

    public function uploads(Request $request)
    {
        if ($request->hasFile('testuploadfiles')) {
            //dd($request->file('testuploadfiles'));
            $msg = '';
            $mediaService = new MediaService(false, false);
            $response = $mediaService->uploadFiles($request->file('testuploadfiles'), 'testuploadfiles', array('jpg', 'jpeg'));
            if (isset($response->errors)) {
                foreach ($response->errors as $error) {
                    $msg .= $error;
                }
            }
            if (isset($response->results)) {
                foreach ($response->results as $result) {
                    $msg .= 'successfully uploaded file ' . $result->filename;
                }
            }
            return Redirect::to('/testupload')->with('msg', $msg);
        }
    }

    public function deleteMedia(Request $request)
    {
        $mediaService = new MediaService();
        $msg = '';
        $response = $mediaService->deleteFile($request->input('deletemediaid'));
        \Log::info('response: ' . print_r($response, true));
        if (isset($response->success) && $response->success) {
            $msg .= 'successfully deleted media ' . $response->media_id;
        }
        if (isset($response->errors)) {
            foreach ($response->errors as $error) {
                $msg .= $error;
            }
        }
        return Redirect::to('/testupload')->with('msg', $msg);
    }

    public function generateDashboardStats(Request $request)
    {
        $this->getGuzzleClient([], 'admin/testing/generate_stats')->getBody()->getContents();
        return redirect()->route('main.dashboard');
    }

    public function showPhpinfo()
    {
        return phpinfo();
    }

    public function getStockMovements($by, $id) {
        $response = json_decode($this->getGuzzleClient([], 'admin/stock_movements/' . $by . '/' . $id)->getBody()->getContents(), true);

        $summary = '<h3>Summary</h3>
                    <p>Stock In Hand: ' . $response['summary']['stock_in_hand'] .'</p>
                    <p>Stock Transfer In Transit: ' . $response['summary']['do_in_transit_stock'] .'</p>
                    <p>Reserved Quantity: ' . $response['summary']['reserved_stock'] .'</p>
                    <p>Available Stock: ' . $response['summary']['available_stock'] .'</p>';

        $table = "<table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($response['stock_movements'] as $movement) {
            $table .= "<tr>";

            foreach ($movement as $info) {
                 $table .= "<td>" . $info . "</td>";
            }

            $table .= "</tr>";
        }

        $table .= "</tbody></table>";

        $html = $summary . '<h3>Stock Movement</h3>' . $table;

        return $html;
    }

}
