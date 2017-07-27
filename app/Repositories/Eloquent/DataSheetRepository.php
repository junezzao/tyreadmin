<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DataSheetRepositoryContract as DataSheetRepositoryInterface;
use App\Models\DataSheet;
use App\Exceptions\ValidationException as ValidationException;
// use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Activity;

class DataSheetRepository extends Repository implements DataSheetRepositoryInterface
{
    protected $model;

    protected $skipCriteria = true;

    protected $user_id;

    public function __construct()
    {
        //parent::__construct();
        $this->model = new DataSheet;
        $this->user_id = \Auth::user()->id;
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\DataSheet';
    }

    public function distinctCustomerName($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.customer_name')->groupBy('data.customer_name')->get();
        return $rows;
    }

    public function distinctJobSheetNo($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.jobsheet_no')->groupBy('data.jobsheet_no')->get();
        return $rows;
    }

    public function distinctTruckNo($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.truck_no')->groupBy('data.truck_no')->get();
        return $rows;
    }

    public function distinctPmNo($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.pm_no')->groupBy('data.pm_no')->get();
        return $rows;
    }

    public function distinctTrailerNo($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.trailer_no')->groupBy('data.trailer_no')->get();
        return $rows;
    }

    public function distinctAttrNt($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.in_attr')->where('data.in_attr', 'NT')->get();
        return $rows;
    }

    public function distinctAttrStk($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.in_attr')->where('data.in_attr', 'STK')->get();
        return $rows;
    }

    public function distinctAttrCoc($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.in_attr')->where('data.in_attr', 'COC')->get();
        return $rows;
    }

    public function distinctAttrUsed($sheetId)
    {
        $rows = $this->model::find($sheetId)->data()->select('data.in_attr')->where('data.in_attr', 'USED')->get();
        return $rows;
    }

    public function getSummary($sheetId)
    {  
        $summary = array(
            'customer' => count($this->distinctCustomerName($sheetId)),
            'jobsheet' => count($this->distinctJobSheetNo($sheetId)),
            'truck' => count($this->distinctTruckNo($sheetId)),
            'pm' => count($this->distinctPmNo($sheetId)),
            'trailer' => count($this->distinctTrailerNo($sheetId)),
            'nt' => count($this->distinctAttrNt($sheetId)),
            'stk' => count($this->distinctAttrStk($sheetId)),
            'coc' => count($this->distinctAttrCoc($sheetId)),
            'used' => count($this->distinctAttrUsed($sheetId)),
        );

        return $summary;
    }

    public function getSheetByUser($userId) {
        $sheet = $this->model->where('user_id', $userId)->first();
        if(!empty($sheet)) $sheet['health'] = $sheet->health();
        return $sheet;
    }

    public function getDataByUser($userId) {
        $sheet = $this->model->where('user_id', $userId)->first();
        return !empty($sheet) ? $sheet->data : array();
    }

    public function viewTruckPosition($userId) {
        $sheet = $this->model->where('user_id', $userId)->first();;

        $return = array();
        if(!empty($sheet)) {
            $distinctCustomers = $this->distinctCustomerName($sheet->id);
        }

        foreach($distinctCustomers as $customer) {
            $customerName = empty($customer->customer_name) ? '(empty)' : $customer->customer_name;

            $return[$customerName] = array();
            $return[$customerName]['Truck']     = $this->getTruckPositionData($sheet->id, $customer->customer_name, 'Truck');
            $return[$customerName]['PM']        = $this->getTruckPositionData($sheet->id, $customer->customer_name, 'PM');
            $return[$customerName]['Trailer']   = $this->getTruckPositionData($sheet->id, $customer->customer_name, 'Trailer');

            if(empty($return[$customerName]['Truck'])) unset($return[$customerName]['Truck']);
            if(empty($return[$customerName]['PM'])) unset($return[$customerName]['PM']);
            if(empty($return[$customerName]['Trailer'])) unset($return[$customerName]['Trailer']);
        }

        return $return;
    }

    public function getTruckPositionData($sheetId, $customerName, $vehicleType) {
        $return = array();

        $vehicles = array(
            'Truck' => 'truck_no',
            'PM' => 'pm_no',
            'Trailer' => 'trailer_no'
        );
        $field = $vehicles[$vehicleType];

        $distinctVehicles = \DB::table('data')->select($field)
                        ->where('sheet_id', $sheetId)
                        ->where('customer_name', $customerName)
                        ->where($field, '<>', '')
                        ->whereNotNull($field)
                        ->groupBy($field)
                        ->get();
        foreach($distinctVehicles as $vehicle) {
            $vehicleNo = empty($vehicle->$field) ? '(empty)' : $vehicle->$field;
            $return[$vehicleNo] = array();

            $distinctPositions = \DB::table('data')->select('position')
                                ->where('sheet_id', $sheetId)
                                ->where('customer_name', $customerName)
                                ->where($field, $vehicle->$field)
                                //->whereNotNull('position')
                                ->groupBy('position')
                                ->get();
            foreach($distinctPositions as $position) {
                $pos = 'Pos '.(empty($position->position) ? '(empty)' : $position->position);
                $return[$vehicleNo][$pos] = array();

                $distinctDates = \DB::table('data')->select('jobsheet_date')
                                ->where('sheet_id', $sheetId)
                                ->where('customer_name', $customerName)
                                ->where($field, $vehicle->$field)
                                ->where('position', $position->position)
                                //->whereNotNull('jobsheet_date')
                                ->groupBy('jobsheet_date')
                                ->get();
                foreach($distinctDates as $date) {
                    $jobsheetDate = empty($date->jobsheet_date) ? '(empty)' : $date->jobsheet_date;
                    $return[$vehicleNo][$pos][$jobsheetDate] = array();

                    $distinctJobsheets = \DB::table('data')->select('jobsheet_no', 'inv_no', 'inv_amt')
                                    ->where('sheet_id', $sheetId)
                                    ->where('customer_name', $customerName)
                                    ->where($field, $vehicle->$field)
                                    ->where('position', $position->position)
                                    ->where('jobsheet_date', $date->jobsheet_date)
                                    ->groupBy('jobsheet_no', 'inv_no', 'inv_amt')
                                    ->get();
                    foreach($distinctJobsheets as $jobsheet) {
                        $jobsheetNo = empty($jobsheet->jobsheet_no) ? '(empty)' : $jobsheet->jobsheet_no;
                        $invoiceNo = empty($jobsheet->inv_no) ? '(empty)' : $jobsheet->inv_no;
                        $invoiceAmt = 'RM'.(empty($jobsheet->inv_amt) ? '0.00' : number_format($jobsheet->inv_amt, 2));

                        $return[$vehicleNo][$pos][$jobsheetDate][$invoiceAmt.' @ '.$jobsheetNo.' / '.$invoiceNo] = array();
                    
                        $distinctJobs = \DB::table('data')
                                        ->where('sheet_id', $sheetId)
                                        ->where('customer_name', $customerName)
                                        ->where($field, $vehicle->$field)
                                        ->where('position', $position->position)
                                        ->where('jobsheet_date', $date->jobsheet_date)
                                        ->where('jobsheet_no', $jobsheet->jobsheet_no)
                                        ->where('inv_no', $jobsheet->inv_no)
                                        ->where('inv_amt', $jobsheet->inv_amt)
                                        ->get();
                        foreach($distinctJobs as $index=>$job) {
                            $return[$vehicleNo][$pos][$jobsheetDate][$invoiceAmt.' @ '.$jobsheetNo.' / '.$invoiceNo][$index]['IN'] = array(
                                    'attr' => $job->in_attr,
                                    'brand' => $job->in_brand,
                                    'pattern' => $job->in_pattern,
                                    'size' => $job->in_size,
                                    'serial_no' => $job->in_serial_no
                                );

                            $return[$vehicleNo][$pos][$jobsheetDate][$invoiceAmt.' @ '.$jobsheetNo.' / '.$invoiceNo][$index]['OUT'] = array(
                                    'reason' => $job->out_reason,
                                    'brand' => $job->out_brand,
                                    'pattern' => $job->out_pattern,
                                    'size' => $job->out_size,
                                    'serial_no' => $job->out_serial_no
                                );
                        }
                    }
                }
            }
        }

        return $return;
    }

    public function viewTruckService($userId) {
        $sheet = $this->model->where('user_id', $userId)->first();;

        $return = array();
        if(!empty($sheet)) {
            $distinctCustomers = $this->distinctCustomerName($sheet->id);
        }

        foreach($distinctCustomers as $customer) {
            $customerName = empty($customer->customer_name) ? '(empty)' : $customer->customer_name;

            $return[$customerName] = array();
            $return[$customerName]['Truck']     = $this->getTruckServiceData($sheet->id, $customer->customer_name, 'Truck');
            $return[$customerName]['PM']        = $this->getTruckServiceData($sheet->id, $customer->customer_name, 'PM');
            $return[$customerName]['Trailer']   = $this->getTruckServiceData($sheet->id, $customer->customer_name, 'Trailer');

            if(empty($return[$customerName]['Truck'])) unset($return[$customerName]['Truck']);
            if(empty($return[$customerName]['PM'])) unset($return[$customerName]['PM']);
            if(empty($return[$customerName]['Trailer'])) unset($return[$customerName]['Trailer']);
        }

        return $return;
    }

    public function getTruckServiceData($sheetId, $customerName, $vehicleType) {
        $return = array();

        $vehicles = array(
            'Truck'     => 'truck_no',
            'PM'        => 'pm_no',
            'Trailer'   => 'trailer_no'
        );
        $field = $vehicles[$vehicleType];

        $distinctVehicles = \DB::table('data')->select($field)
                        ->where('sheet_id', $sheetId)
                        ->where('customer_name', $customerName)
                        ->where($field, '<>', '')
                        ->whereNotNull($field)
                        ->groupBy($field)
                        ->get();
        foreach($distinctVehicles as $vehicle) {
            $vehicleNo = empty($vehicle->$field) ? '(empty)' : $vehicle->$field;
            $return[$vehicleNo] = array();

            $distinctDates = \DB::table('data')->select('jobsheet_date')
                                ->where('sheet_id', $sheetId)
                                ->where('customer_name', $customerName)
                                ->where($field, $vehicle->$field)
                                //->whereNotNull('jobsheet_date')
                                ->groupBy('jobsheet_date')
                                ->get();
            foreach($distinctDates as $date) {
                $jobsheetDate = empty($date->jobsheet_date) ? '(empty)' : $date->jobsheet_date;
                $return[$vehicleNo][$jobsheetDate] = array();

                $distinctJobsheets = \DB::table('data')->select('jobsheet_no', 'inv_no', 'inv_amt',  \DB::raw('sum(in_price) as total_price'))
                                ->where('sheet_id', $sheetId)
                                ->where('customer_name', $customerName)
                                ->where($field, $vehicle->$field)
                                ->where('jobsheet_date', $date->jobsheet_date)
                                ->groupBy('jobsheet_no', 'inv_no', 'inv_amt')
                                ->get();
                foreach($distinctJobsheets as $jobsheet) {
                    $jobsheetNo = empty($jobsheet->jobsheet_no) ? '(empty)' : $jobsheet->jobsheet_no;
                    $invoiceNo = empty($jobsheet->inv_no) ? '(empty)' : $jobsheet->inv_no;
                    $invoiceAmt = 'RM'.(empty($jobsheet->inv_amt) ? '0.00' : number_format($jobsheet->inv_amt, 2));
                    $totalPrice = 'RM'.number_format($jobsheet->total_price, 2);

                    $return[$vehicleNo][$jobsheetDate][$jobsheetNo.': '.$totalPrice.' / '.$invoiceNo.', '.$invoiceAmt] = array();

                    $distinctPositions = \DB::table('data')->select('position')
                                    ->where('sheet_id', $sheetId)
                                    ->where('customer_name', $customerName)
                                    ->where($field, $vehicle->$field)
                                    ->where('jobsheet_date', $date->jobsheet_date)
                                    ->where('jobsheet_no', $jobsheet->jobsheet_no)
                                    ->where('inv_no', $jobsheet->inv_no)
                                    ->where('inv_amt', $jobsheet->inv_amt)
                                    //->whereNotNull('position')
                                    ->groupBy('position')
                                    ->get();
                    foreach($distinctPositions as $position) {
                        $pos = 'Pos '.(empty($position->position) ? '(empty)' : $position->position);
                        $return[$vehicleNo][$jobsheetDate][$jobsheetNo.': '.$totalPrice.' / '.$invoiceNo.', '.$invoiceAmt][$pos] = array();
                    
                        $distinctJobs = \DB::table('data')
                                        ->where('sheet_id', $sheetId)
                                        ->where('customer_name', $customerName)
                                        ->where($field, $vehicle->$field)
                                        ->where('jobsheet_date', $date->jobsheet_date)
                                        ->where('jobsheet_no', $jobsheet->jobsheet_no)
                                        ->where('inv_no', $jobsheet->inv_no)
                                        ->where('inv_amt', $jobsheet->inv_amt)
                                        ->where('position', $position->position)
                                        ->get();
                        foreach($distinctJobs as $index=>$job) {
                            $return[$vehicleNo][$jobsheetDate][$jobsheetNo.': '.$totalPrice.' / '.$invoiceNo.', '.$invoiceAmt][$pos][$index]['IN'] = array(
                                    'attr' => $job->in_attr,
                                    'brand' => $job->in_brand,
                                    'pattern' => $job->in_pattern,
                                    'size' => $job->in_size,
                                    'serial_no' => $job->in_serial_no,
                                    'price' => 'RM'.number_format($job->in_price, 2)
                                );

                            $return[$vehicleNo][$jobsheetDate][$jobsheetNo.': '.$totalPrice.' / '.$invoiceNo.', '.$invoiceAmt][$pos][$index]['OUT'] = array(
                                    'reason' => $job->out_reason,
                                    'brand' => $job->out_brand,
                                    'pattern' => $job->out_pattern,
                                    'size' => $job->out_size,
                                    'serial_no' => $job->out_serial_no
                                );
                        }
                    }
                }
            }
        }

        return $return;
    }

    public function viewTyreBrand($userId) {
        $sheet = $this->model->where('user_id', $userId)->first();

        /*$tyreAttibutes = array(
            'NT'    => 'New Tyre (NT)', 
            'STK'   => 'Stock Retread (STK)', 
            'COC'   => 'Customer Own Casing (COC)', 
            'USED'  => 'Used Tyre (USED)'
        );*/
        
        $return = array();
        $return['NT']   = $this->getTyreBrandNewData($sheet->id, 'NT');
        $return['STK']   = $this->getTyreBrandRetreadData($sheet->id, 'STK');
        $return['COC']   = $this->getTyreBrandRetreadData($sheet->id, 'COC');
        $return['USED'] = $this->getTyreBrandNewData($sheet->id, 'USED');

        return $return;
    }

    public function getTyreBrandNewData($sheetId, $tyreAttribute) {
        $return = array();

        $distinctBrands = \DB::table('data')->select('in_brand')
                        ->where('sheet_id', $sheetId)
                        ->where('in_attr', $tyreAttribute)
                        //->whereNotNull('in_brand')
                        ->groupBy('in_brand')
                        ->get();
        foreach($distinctBrands as $brand) {
            $tyreBrand = empty($brand->in_brand) ? '(empty)' : $brand->in_brand;
            $return[$tyreBrand] = array();

            $distinctPatterns = \DB::table('data')->select('in_pattern')
                                ->where('sheet_id', $sheetId)
                                ->where('in_attr', $tyreAttribute)
                                ->where('in_brand', $brand->in_brand)
                                //->whereNotNull('in_pattern')
                                ->groupBy('in_pattern')
                                ->get();
            foreach($distinctPatterns as $pattern) {
                $tyrePattern = empty($pattern->in_pattern) ? '(empty)' : $pattern->in_pattern;
                $return[$tyreBrand][$tyrePattern] = array();

                $distinctSizes = \DB::table('data')->select('in_size')
                                ->where('sheet_id', $sheetId)
                                ->where('in_attr', $tyreAttribute)
                                ->where('in_brand', $brand->in_brand)
                                ->where('in_pattern', $pattern->in_pattern)
                                //->whereNotNull('in_size')
                                ->groupBy('in_size')
                                ->get();
                foreach($distinctSizes as $size) {
                    $tyreSize = empty($size->in_size) ? '(empty)' : $size->in_size;
                    $return[$tyreBrand][$tyrePattern][$tyreSize] = array();

                    $distinctSerialNos = \DB::table('data')->select('in_serial_no')
                                    ->where('sheet_id', $sheetId)
                                    ->where('in_attr', $tyreAttribute)
                                    ->where('in_brand', $brand->in_brand)
                                    ->where('in_pattern', $pattern->in_pattern)
                                    ->where('in_size', $size->in_size)
                                    //->whereNotNull('in_serial_no')
                                    ->groupBy('in_serial_no')
                                    ->get();
                    foreach($distinctSerialNos as $serialNo) {
                        $tyreSerialNo = empty($serialNo->in_serial_no) ? '(empty)' : $serialNo->in_serial_no;
                        $return[$tyreBrand][$tyrePattern][$tyreSize][$tyreSerialNo] = array();
                    
                        $distinctCustomers = \DB::table('data')->select('customer_name')
                                        ->where('sheet_id', $sheetId)
                                        ->where('in_attr', $tyreAttribute)
                                        ->where('in_brand', $brand->in_brand)
                                        ->where('in_pattern', $pattern->in_pattern)
                                        ->where('in_size', $size->in_size)
                                        ->where('in_serial_no', $serialNo->in_serial_no)
                                        //->whereNotNull('customer_name')
                                        ->groupBy('customer_name')
                                        ->get();
                        foreach($distinctCustomers as $customer) {
                            $customerName = empty($customer->customer_name) ? '(empty)' : $customer->customer_name;
                            $return[$tyreBrand][$tyrePattern][$tyreSize][$tyreSerialNo][$customerName] = array();

                            $vehicles = ['Truck', 'PM', 'Trailer'];
                            foreach($vehicles as $vehicleType) {
                                $return[$tyreBrand][$tyrePattern][$tyreSize][$tyreSerialNo][$customerName][$vehicleType] = $this->getTyreBrandVehicleTypeData($sheetId, $tyreAttribute, $brand->in_brand, $pattern->in_pattern, $size->in_size, $serialNo->in_serial_no, $vehicleType);
                                if(empty($return[$tyreBrand][$tyrePattern][$tyreSize][$tyreSerialNo][$customerName][$vehicleType])) unset($return[$tyreBrand][$tyrePattern][$tyreSize][$tyreSerialNo][$customerName][$vehicleType]);
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

    public function getTyreBrandRetreadData($sheetId, $tyreAttribute) {
        $return = array();

        $distinctBrands = \DB::table('data')->select('in_retread_brand')
                        ->where('sheet_id', $sheetId)
                        ->where('in_attr', $tyreAttribute)
                        //->whereNotNull('in_retread_brand')
                        ->groupBy('in_retread_brand')
                        ->get();
        foreach($distinctBrands as $brand) {
            $tyreBrand = empty($brand->in_retread_brand) ? '(empty)' : $brand->in_retread_brand;
            $return[$tyreBrand] = array();

            $distinctPatterns = \DB::table('data')->select('in_retread_pattern')
                                ->where('sheet_id', $sheetId)
                                ->where('in_attr', $tyreAttribute)
                                ->where('in_retread_brand', $brand->in_retread_brand)
                                //->whereNotNull('in_retread_pattern')
                                ->groupBy('in_retread_pattern')
                                ->get();
            foreach($distinctPatterns as $pattern) {
                $tyrePattern = empty($pattern->in_retread_pattern) ? '(empty)' : $pattern->in_retread_pattern;
                $return[$tyreBrand][$tyrePattern] = array();

                $distinctSizes = \DB::table('data')->select('in_size')
                                ->where('sheet_id', $sheetId)
                                ->where('in_attr', $tyreAttribute)
                                ->where('in_retread_brand', $brand->in_retread_brand)
                                ->where('in_retread_pattern', $pattern->in_retread_pattern)
                                //->whereNotNull('in_size')
                                ->groupBy('in_size')
                                ->get();
                foreach($distinctSizes as $size) {
                    $tyreSize = empty($size->in_size) ? '(empty)' : $size->in_size;
                    $return[$tyreBrand][$tyrePattern][$tyreSize] = array();

                    $distinctJobCards = \DB::table('data')->select('in_job_card_no', 'in_serial_no', 'in_brand', 'in_pattern')
                                    ->where('sheet_id', $sheetId)
                                    ->where('in_attr', $tyreAttribute)
                                    ->where('in_retread_brand', $brand->in_retread_brand)
                                    ->where('in_retread_pattern', $pattern->in_retread_pattern)
                                    ->where('in_size', $size->in_size)
                                    //->whereNotNull('in_serial_no')
                                    ->groupBy('in_job_card_no', 'in_serial_no', 'in_brand', 'in_pattern')
                                    ->get();
                    foreach($distinctJobCards as $jobCard) {
                        $jobCardNumber = empty($jobCard->in_job_card_no) ? '(empty)' : $jobCard->in_job_card_no;
                        $serialNumber = empty($jobCard->in_serial_no) ? '(empty)' : $jobCard->in_serial_no;
                        $brandName = empty($jobCard->in_brand) ? '(empty)' : $jobCard->in_brand;
                        $patternName = empty($jobCard->in_pattern) ? '(empty)' : $jobCard->in_pattern;
                        $return[$tyreBrand][$tyrePattern][$tyreSize][$jobCardNumber.' / '.$serialNumber.' [ '.$brandName.' '.$patternName.' ]'] = array();
                    
                        $distinctCustomers = \DB::table('data')->select('customer_name')
                                        ->where('sheet_id', $sheetId)
                                        ->where('in_attr', $tyreAttribute)
                                        ->where('in_retread_brand', $brand->in_retread_brand)
                                        ->where('in_retread_pattern', $pattern->in_retread_pattern)
                                        ->where('in_size', $size->in_size)
                                        ->where('in_job_card_no', $jobCard->in_job_card_no)
                                        ->where('in_serial_no', $jobCard->in_serial_no)
                                        ->where('in_brand', $jobCard->in_brand)
                                        ->where('in_pattern', $jobCard->in_pattern)
                                        //->whereNotNull('customer_name')
                                        ->groupBy('customer_name')
                                        ->get();
                        foreach($distinctCustomers as $customer) {
                            $customerName = empty($customer->customer_name) ? '(empty)' : $customer->customer_name;
                            $return[$tyreBrand][$tyrePattern][$tyreSize][$jobCardNumber.' / '.$serialNumber.' [ '.$brandName.' '.$patternName.' ]'][$customerName] = array();

                            $vehicles = ['Truck', 'PM', 'Trailer'];
                            foreach($vehicles as $vehicleType) {
                                $return[$tyreBrand][$tyrePattern][$tyreSize][$jobCardNumber.' / '.$serialNumber.' [ '.$brandName.' '.$patternName.' ]'][$customerName][$vehicleType] = $this->getTyreBrandVehicleTypeData($sheetId, $tyreAttribute, $brand->in_retread_brand, $pattern->in_retread_pattern, $size->in_size, $jobCard->in_serial_no, $vehicleType);
                                if(empty($return[$tyreBrand][$tyrePattern][$tyreSize][$jobCardNumber.' / '.$serialNumber.' [ '.$brandName.' '.$patternName.' ]'][$customerName][$vehicleType])) unset($return[$tyreBrand][$tyrePattern][$tyreSize][$jobCardNumber.' / '.$serialNumber.' [ '.$brandName.' '.$patternName.' ]'][$customerName][$vehicleType]);
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

    public function getTyreBrandVehicleTypeData($sheetId, $tyreAttribute, $tyreBrand, $tyrePattern, $tyreSize, $tyreSerialNo, $vehicleType) {
        $return = array();

        $vehicles = array(
            'Truck'     => 'truck_no',
            'PM'        => 'pm_no',
            'Trailer'   => 'trailer_no'
        );
        $field = $vehicles[$vehicleType];

        $distinctVehicles = \DB::table('data')->select($field)
                        ->where('sheet_id', $sheetId)
                        ->where('in_attr', $tyreAttribute)
                        ->where('in_brand', $tyreBrand)
                        ->where('in_pattern', $tyrePattern)
                        ->where('in_size', $tyreSize)
                        ->where('in_serial_no', $tyreSerialNo)
                        ->where($field, '<>', '')
                        ->whereNotNull($field)
                        ->groupBy($field)
                        ->get();
        foreach($distinctVehicles as $vehicle) {
            $vehicleNo = empty($vehicle->$field) ? '(empty)' : $vehicle->$field;
            $return[$vehicleNo] = array();

            $distinctPositions = \DB::table('data')->select('position')
                                ->where('sheet_id', $sheetId)
                                ->where('in_attr', $tyreAttribute)
                                ->where('in_brand', $tyreBrand)
                                ->where('in_pattern', $tyrePattern)
                                ->where('in_size', $tyreSize)
                                ->where('in_serial_no', $tyreSerialNo)
                                ->where($field, $vehicle->$field)
                                //->whereNotNull('position')
                                ->groupBy('position')
                                ->get();
            foreach($distinctPositions as $position) {
                $pos = 'Pos '.(empty($position->position) ? '(empty)' : $position->position);
                $return[$vehicleNo][$pos] = array();

                $distinctDates = \DB::table('data')->select('jobsheet_date')
                                ->where('sheet_id', $sheetId)
                                ->where('in_attr', $tyreAttribute)
                                ->where('in_brand', $tyreBrand)
                                ->where('in_pattern', $tyrePattern)
                                ->where('in_size', $tyreSize)
                                ->where('in_serial_no', $tyreSerialNo)
                                ->where($field, $vehicle->$field)
                                ->where('position', $position->position)
                                //->whereNotNull('jobsheet_date')
                                ->groupBy('jobsheet_date')
                                ->get();
                foreach($distinctDates as $date) {
                    $jobsheetDate = empty($date->jobsheet_date) ? '(empty)' : $date->jobsheet_date;
                    $return[$vehicleNo][$pos][$jobsheetDate] = array();

                    $distinctJobsheets = \DB::table('data')->select('jobsheet_no', 'inv_no', 'inv_amt')
                                    ->where('sheet_id', $sheetId)
                                    ->where('in_attr', $tyreAttribute)
                                    ->where('in_brand', $tyreBrand)
                                    ->where('in_pattern', $tyrePattern)
                                    ->where('in_size', $tyreSize)
                                    ->where('in_serial_no', $tyreSerialNo)
                                    ->where($field, $vehicle->$field)
                                    ->where('position', $position->position)
                                    ->where('jobsheet_date', $date->jobsheet_date)
                                    ->groupBy('jobsheet_no', 'inv_no', 'inv_amt')
                                    ->get();
                    foreach($distinctJobsheets as $index=>$jobsheet) {
                        $jobsheetNo = empty($jobsheet->jobsheet_no) ? '(empty)' : $jobsheet->jobsheet_no;
                        $invoiceNo = empty($jobsheet->inv_no) ? '(empty)' : $jobsheet->inv_no;
                        $invoiceAmt = 'RM'.(empty($jobsheet->inv_amt) ? '0.00' : number_format($jobsheet->inv_amt, 2));

                        $return[$vehicleNo][$pos][$jobsheetDate][$index] = $invoiceAmt.' @ '.$jobsheetNo.' / '.$invoiceNo;
                    }
                }
            }
        }

        return $return;
    }

    
}
