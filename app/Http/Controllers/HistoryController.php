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
        $data['truckPositionData'] = json_decode($this->getGuzzleClient(array(), 'data/'.$this->user->id.'/view/truck_position')->getBody()->getContents(), true);
        $data['truckServiceData'] = json_decode($this->getGuzzleClient(array(), 'data/'.$this->user->id.'/view/truck_service')->getBody()->getContents(), true);
        $data['tyreBrand'] = json_decode($this->getGuzzleClient(array(), 'data/'.$this->user->id.'/view/tyre_brand')->getBody()->getContents(), true);
        
        // \Log::info('data... '.print_r($data['truckPositionData'], true));
        return view('history.index', $data);
    }
}
