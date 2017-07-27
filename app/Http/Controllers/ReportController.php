<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Admin\AdminController;
use Form;
use Excel;

class ReportController extends AdminController
{
    use GuzzleClient;

    protected $user;

    public function __construct()
    {
        $this->user = \Auth::user();
    }

    public function index()
    {
        return view('reports.index');
    }

    public function serialNoAnalysis()
    {
        $data = array();
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/serial_no_analysis')->getBody()->getContents(), true);
        // \Log::info('data... '.print_r($data, true));

        return view('reports.serial-no-analysis', $data);
    }

    public function odometerAnalysis(Request $request)
    {
        $data = array();
        $data = json_decode($this->getGuzzleClient($request->all(), 'reports/'.$this->user->id.'/odometer_analysis')->getBody()->getContents(), true);
        // \Log::info(print_r( $request->all(), true));

        return view('reports.odometer-analysis', $data);
    }

    public function tyreRemovalMileage()
    {
        $data = array();
        $data['data'] = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/tyre_removal_mileage')->getBody()->getContents(), true);
        // \Log::info('data... '.print_r($data['data'], true));

        return view('reports.tyre-removal-mileage', $data);
    }

    public function tyreRemovalRecord()
    {
        $data = array();
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/tyre_removal_record')->getBody()->getContents(), true);
        // \Log::info('data... '.print_r($data, true));

        return view('reports.tyre-removal-record', $data);
    }

    public function truckTyreCost(Request $request)
    {
        $data = array();
        $data['data'] = json_decode($this->getGuzzleClient($request->all(), 'reports/'.$this->user->id.'/truck_tyre_cost')->getBody()->getContents(), true);
        // \Log::info('data... '.print_r($data, true));

        return view('reports.truck-tyre-cost', $data);
    }

    public function truckServiceRecord()
    {
        $data = array();
        $data['data'] = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/truck_service_record')->getBody()->getContents(), true);
        // \Log::info('data... '.print_r($data, true));

        return view('reports.truck-service-record', $data);
    }
}
