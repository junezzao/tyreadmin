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

class HistoryController extends AdminController
{
    use GuzzleClient;

    protected $user;

    public function __construct()
    {
        $this->user = \Auth::user();
    }

    public function index()
    {
        $data = array();
        
        $truckPositionData = json_decode($this->getGuzzleClient(array(), 'data/'.$this->user->id.'/view/truck_position')->getBody()->getContents(), true);
        $truckPosition = array();
        $i = 0;
        foreach ($truckPositionData as $customer => $customerData) {
            $truckPosition[$i]['text'] = $customer;

            $j = 0;
            foreach ($customerData as $vehicle => $vehicleData) {
                $truckPosition[$i]['nodes'][$j]['text'] = $vehicle;

                $k = 0;
                foreach ($vehicleData as $vehicleNo => $vehicleNoData) {
                    $truckPosition[$i]['nodes'][$j]['nodes'][$k]['text'] = $vehicleNo;

                    $l = 0;
                    foreach ($vehicleNoData as $position => $positionData) {
                        $truckPosition[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['text'] = 'Pos '.$position;

                        $m = 0;
                        foreach ($positionData as $index => $job) {
                            $truckPosition[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['text'] = $job['date'];

                            $truckPosition[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][0]['text'] = 'IN: '.$job['in'];
                            $truckPosition[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][1]['text'] = 'OUT: '.$job['out'];

                            $truckPosition[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][0]['nodes'][0]['text'] = $job['invoice'];
                            $m++;
                        }
                        $l++;
                    }
                    $k++;
                }
                $j++;
            }
            $i++;
        }
        
        $truckServiceData = json_decode($this->getGuzzleClient(array(), 'data/'.$this->user->id.'/view/truck_service')->getBody()->getContents(), true);
        $truckService = array();
        $i = 0;
        foreach ($truckServiceData as $customer => $customerData) {
            $truckService[$i]['text'] = $customer;

            $j = 0;
            foreach ($customerData as $vehicle => $vehicleData) {
                $truckService[$i]['nodes'][$j]['text'] = $vehicle;

                $k = 0;
                foreach ($vehicleData as $vehicleNo => $vehicleNoData) {
                    $truckService[$i]['nodes'][$j]['nodes'][$k]['text'] = $vehicleNo;

                    $l = 0;
                    foreach ($vehicleNoData as $jobsheetDate => $jobsheetDateData) {
                        $truckService[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['text'] = $jobsheetDate;

                        $m = 0;
                        foreach ($jobsheetDateData as $jobsheet => $jobsheetData) {
                            $truckService[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['text'] = str_replace('TOTAL_PRICE', 'RM'.number_format($jobsheetData['totalPrice'], 2), $jobsheet);
                            
                            $n = 0;
                            foreach ($jobsheetData['positions'] as $index => $job) {
                                $truckService[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['text'] = 'Pos '.$job['position'];

                                $truckService[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['nodes'][0]['text'] = 'IN: '.$job['in'];
                                $truckService[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['nodes'][1]['text'] = 'OUT: '.$job['out'];

                                $truckService[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['nodes'][0]['nodes'][0]['text'] = $job['invoice'];
                                $n++;
                            }
                            $m++;
                        }
                        $l++;
                    }
                    $k++;
                }
                $j++;
            }
            $i++;
        }

        $tyreBrandData = json_decode($this->getGuzzleClient(array(), 'data/'.$this->user->id.'/view/tyre_brand')->getBody()->getContents(), true);
        $tyreBrand = array(
            'NT'            => $this->generateTyreBrandTree($tyreBrandData['NT']),
            'NT_SUB_CON'    => $this->generateTyreBrandTree($tyreBrandData['NT_SUB_CON']),
            'STK'           => $this->generateTyreBrandTree($tyreBrandData['STK']),
            'STK_SUB_CON'   => $this->generateTyreBrandTree($tyreBrandData['STK_SUB_CON']),
            'COC'           => $this->generateTyreBrandTree($tyreBrandData['COC']),
            'USED'          => $this->generateTyreBrandTree($tyreBrandData['USED']),
            'OTHER'         => $this->generateTyreBrandTree($tyreBrandData['OTHER'])
        );

        $data['truckPosition']              = json_encode(array(array('text'=>'Customer', 'nodes'=>$truckPosition)), true);
        $data['truckService']               = json_encode(array(array('text'=>'Customer', 'nodes'=>$truckService)), true);
        $data['tyreBrand']['NT']            = json_encode(array(array('text'=>'New Tyre (NT)', 'nodes'=>$tyreBrand['NT'])), true);
        $data['tyreBrand']['NT_SUB_CON']    = json_encode(array(array('text'=>'New Tyre Sub Con (NT SUB CON)', 'nodes'=>$tyreBrand['NT_SUB_CON'])), true);
        $data['tyreBrand']['STK']           = json_encode(array(array('text'=>'Stock Retread (STK)', 'nodes'=>$tyreBrand['STK'])), true);
        $data['tyreBrand']['STK_SUB_CON']   = json_encode(array(array('text'=>'Stock Retread Sub Con (STK SUB CON)', 'nodes'=>$tyreBrand['STK_SUB_CON'])), true);
        $data['tyreBrand']['COC']           = json_encode(array(array('text'=>'Customer Own Casing (COC)', 'nodes'=>$tyreBrand['COC'])), true);
        $data['tyreBrand']['USED']          = json_encode(array(array('text'=>'Used Tyre (USED)', 'nodes'=>$tyreBrand['USED'])), true);
        $data['tyreBrand']['OTHER']         = json_encode(array(array('text'=>'Other (OTHER)', 'nodes'=>$tyreBrand['OTHER'])), true);

        // \Log::info(print_r($data['tyreBrand']['NT'], true));
        return view('history.index', $data);
    }

    public function generateTyreBrandTree($data) {
        $tree = array();

        $i = 0;
        foreach ($data as $brand => $brandData) {
            $tree[$i]['text'] = $brand;

            $j = 0;
            foreach ($brandData as $pattern => $patternData) {
                $tree[$i]['nodes'][$j]['text'] = $pattern;

                $k = 0;
                foreach ($patternData as $size => $sizeData) {
                    $tree[$i]['nodes'][$j]['nodes'][$k]['text'] = $size;

                    $l = 0;
                    foreach ($sizeData as $serialNo => $serialNoData) {
                        $tree[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['text'] = $serialNo;

                        $m = 0;
                        foreach ($serialNoData as $customer => $customerData) {
                            $tree[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['text'] = $customer;
                            
                            $n = 0;
                            foreach ($customerData as $vehicleType => $vehicleData) {
                                $tree[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['text'] = $vehicleType;
                                
                                $o = 0;
                                foreach ($vehicleData as $vehicleNo => $positionData) {
                                    $tree[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['nodes'][$o]['text'] = $vehicleNo;
                                    
                                    $p = 0;
                                    foreach ($positionData as $position => $jobs) {
                                        $tree[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['nodes'][$o]['nodes'][$p]['text'] = 'Pos '.$position;
                                        
                                        $q = 0;
                                        foreach ($jobs as $index => $job) {
                                            $tree[$i]['nodes'][$j]['nodes'][$k]['nodes'][$l]['nodes'][$m]['nodes'][$n]['nodes'][$o]['nodes'][$p]['nodes'][$q]['text'] = $job;
                                            $q++;
                                        }
                                        $p++;
                                    }
                                    $o++;
                                }
                                $n++;
                            }
                            $m++;
                        }
                        $l++;
                    }
                    $k++;
                }
                $j++;
            }
            $i++;
        }

        return $tree;
    }
}
