<?php 

namespace App\Http\Controllers;

use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

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
                    'serialNo2' => $index == 0 ? $serialNo : '',
                    'fitting'   => $fitting
                ];
            }
        }

        return json_encode(['data' => $return]);
    }

    public function odometerAnalysis(Request $request)
    {
        return view('reports.odometer-analysis', $request->all());
    }

    public function odometerAnalysisLoadMissing(Request $request)
    {
        $data = json_decode($this->getGuzzleClient($request->all(), 'reports/'.$this->user->id.'/odometer_analysis/missing')->getBody()->getContents(), true);
        return json_encode(['data' => $data]);
    }

    public function odometerAnalysisLoadLess()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/odometer_analysis/less')->getBody()->getContents(), true);

        $return = array();
        foreach($data as $vehicle => $readings) {
            foreach($readings as $index => $reading) {
                $return[] = [
                    'vehicle'   => $vehicle,
                    'vehicle2'  => $index == 0 ? $vehicle : '',
                    'reading'   => $reading
                ];
            }
        }

        return json_encode(['data' => $return]);
    }

    public function tyreRemovalMileage()
    {
        return view('reports.tyre-removal-mileage');
    }

    public function tyreRemovalMileageLoad()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/tyre_removal_mileage')->getBody()->getContents(), true);
        
        $return = array();
        foreach($data as $serialNo => $fittings) {
            $totalMileage = 0;
            foreach($fittings as $index => $fitting) {
                if(is_numeric($fitting['mileage'])) $totalMileage += $fitting['mileage'];

                $return[] = [
                    'serialNo'      => $serialNo,
                    'serialNo2'     => $index == 0 ? $serialNo : '',
                    'tyre'          => $fitting['tyre'],
                    'tyre_retread'  => $fitting['tyre_retread'],
                    'in_out'        => $fitting['in_out'],
                    'remark'        => $fitting['remark'],
                    'date'          => $fitting['date'],
                    'jobsheet'      => $fitting['jobsheet'],
                    'vehicle'       => $fitting['vehicle'],
                    'position'      => $fitting['position'],
                    'odometer'      => $fitting['odometer'],
                    'mileage'       => $fitting['mileage'],
                ];
            }

            $return[] = [
                'serialNo'      => $serialNo,
                'serialNo2'      => '',
                'tyre'          => '',
                'tyre_retread'  => '',
                'in_out'        => '',
                'remark'        => '',
                'date'          => '',
                'jobsheet'      => '',
                'vehicle'       => '',
                'position'      => '',
                'odometer'      => '<b>Total Mileage</b>',
                'mileage'       => $totalMileage,
            ];
        }

        return json_encode(['data' => $return]);
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
                    'vehicle2'  => $index == 0 ? $vehicle : '',
                    'info'      => $fitting['info'],
                    'remark'    => $fitting['remark']
                ];
            }
        }

        return json_encode(['data' => $return]);
    }

    public function truckTyreCost(Request $request)
    {
        return view('reports.truck-tyre-cost', $request->all());
    }

    public function truckTyreCostLoad(Request $request)
    {
        $data = json_decode($this->getGuzzleClient($request->all(), 'reports/'.$this->user->id.'/truck_tyre_cost')->getBody()->getContents(), true);
        
        $return = array();
        $lastCustomer       = '';
        $lastVehicleType    = '';
        foreach($data as $customer => $customerData) {
            foreach($customerData['costs'] as $vehicleType => $costs) {
                foreach($costs as $index => $cost) {
                    $return[] = [
                        'customer'      => $customer,
                        'customer2'     => '<b>'.($customer != $lastCustomer ? $customer : '').'</b>',
                        'date'          => ($customer != $lastCustomer ? $customerData['from'].' till '.$customerData['to'] : ''),
                        'vehicleType'   => '<b>'.($vehicleType != $lastVehicleType ? strtoupper($vehicleType) : '').'</b>',
                        'rank'          => $index + 1,
                        'vehicleNo'     => $cost['vehicleNo'],
                        'cost'          => $cost['cost'],
                    ];
                    $lastCustomer       = $customer;
                    $lastVehicleType    = $vehicleType;
                }
            }
        }

        return json_encode(['data' => $return]);
    }

    public function truckServiceRecord()
    {
        return view('reports.truck-service-record');
    }

    public function truckServiceRecordLoad()
    {
        $data = json_decode($this->getGuzzleClient(array(), 'reports/'.$this->user->id.'/truck_service_record')->getBody()->getContents(), true);
        
        $return = array();
        $lastCustomer       = '';
        $lastVehicleType    = '';
        $lastVehicleNo      = '';
        foreach($data as $customer => $customerData) {
            foreach($customerData as $vehicleType => $vehicleData) {
                foreach($vehicleData as $vehicleNo => $positionData) {
                    foreach($positionData as $position => $jobs) {
                        foreach($jobs as $index => $job) {
                            $return[] = [
                                'customer'      => $customer,
                                'vehicleNo'     => $vehicleNo,
                                'customer2'     => '<b>'.($customer != $lastCustomer ? $customer : '').'</b>',
                                'vehicleType'   => '<b>'.($vehicleType != $lastVehicleType ? strtoupper($vehicleType) : '').'</b>',
                                'vehicleNo2'    => '<b>'.($vehicleNo != $lastVehicleNo ? $vehicleNo : '').'</b>',
                                'position'      => $position,
                                'info'          => $job['info'],
                                'in'            => $job['in'],
                                'out'           => $job['out']
                            ];
                            $lastCustomer       = $customer;
                            $lastVehicleType    = $vehicleType;
                            $lastVehicleNo      = $vehicleNo;
                        }
                    }
                }
            }
        }

        return json_encode(['data' => $return]);
    }
}
