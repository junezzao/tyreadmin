<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DataSheetRepositoryContract as DataSheetRepositoryInterface;
use App\Models\DataSheet;

class DataSheetRepository extends Repository implements DataSheetRepositoryInterface
{
    protected $model;

    protected $skipCriteria = true;

    protected $user_id;

    public function __construct()
    {
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
}
