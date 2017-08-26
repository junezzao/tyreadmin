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
        $this->middleware('permission:view.report', ['except' => ['index']]);
        $this->user = \Auth::user();
    }

    public function index()
    {
        $data = array();
        $data['no_access'] = $this->user->category == 'Lite' ? true : false;

        return view('reports.index', $data);
    }

    public function serialNoAnalysis()
    {
        return view('reports.serial-no-analysis');
    }

    public function serialNoAnalysisLoadMissing()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/serial_no_analysis/missing')->getBody()->getContents(), true);
        return json_encode(['data' => $data]);
    }

    public function serialNoAnalysisLoadRepeated()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/serial_no_analysis/repeated')->getBody()->getContents(), true);

        $return = array();
        foreach($data as $serialNo => $fittings) {
            foreach($fittings as $index => $fitting) {
                $return[] = [
                    'serialNo'  => $serialNo,
                    'fitting'   => $fitting
                ];
            }
        }

        return json_encode(['data' => $return]);
    }

    public function odometerAnalysis(Request $request)
    {
        return view('reports.odometer-analysis');
    }

    public function odometerAnalysisLoadMissing(Request $request)
    {
        $data = json_decode($this->getGuzzleClient($request->all(), 'reports/'.$this->user->id.'/odometer_analysis/missing')->getBody()->getContents(), true);
        return json_encode(['data' => $data]);
    }

    public function odometerAnalysisLoadLess(Request $request)
    {
        $data = json_decode($this->getGuzzleClient($request->all(), 'reports/'.$this->user->id.'/odometer_analysis/less')->getBody()->getContents(), true);

        $return = array();
        foreach($data as $vehicle => $readings) {
            foreach($readings as $index => $reading) {
                $return[] = [
                    'vehicle'  => $vehicle,
                    'reading'   => $reading
                ];
            }
        }

        return json_encode(['data' => $return]);
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
        return view('reports.tyre-removal-record');
    }

    public function tyreRemovalRecordLoadOnlyIn()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/tyre_removal_record/only_in')->getBody()->getContents(), true);
        return json_encode(['data' => $data]);
    }

    public function tyreRemovalRecordLoadOnlyOut()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/tyre_removal_record/only_out')->getBody()->getContents(), true);
        return json_encode(['data' => $data]);
    }

    public function tyreRemovalRecordLoadConflict()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/tyre_removal_record/conflict')->getBody()->getContents(), true);
        
        $return = array();
        foreach($data as $vehicle => $fittings) {
            foreach($fittings as $index => $fitting) {
                $return[] = [
                    'vehicle'   => $vehicle,
                    'info'      => $fitting['info'],
                    'remark'    => $fitting['remark']
                ];
            }
        }

        return json_encode(['data' => $return]);
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
