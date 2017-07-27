<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\MerchantRepository as MerchantRepo;
use App\Repositories\Eloquent\ChannelRepository as ChannelRepo;
use App\Repositories\Eloquent\BrandRepository as BrandRepo;
use App\Models\Merchant;
use App\Models\Channel;
use App\Models\Brand;
use Validator;
use Form;
use Log;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Helpers\Helper;

class GenerateReportsController extends AdminController
{

    protected $admin;
    use GuzzleClient;


    public function __construct()
    {
        $this->middleware('permission:view.generatereport');

        $this->admin = \Auth::user();
    }

    public function index()
    {
        $merchantLists = $this->getMerchantList();
        $channelList = $this->getChannelList();
        //$brandList = $this->getBrandList();
        $reportTypes = array('Brands', 'Sales');
        $data['reportTypes'] = $reportTypes;
        $data['merchantList'] = $merchantLists;
        $data['channelList'] = $channelList;
        //$data['brandList'] = $brandList;

        return view('admin.generate-report.index', $data);
    }

    public function search(Request $request){

        //dd($request->request);
        
        
        $reportTypes = $request->get('report-type');
        $channel = $request->get('channel');
        $merchant = $request->get('merchant');
        //$brand[] = $request->get('brand');
        $reports = array(
                        'reportTypes'     => $reportTypes,
                        'channel'     => $channel,
                        'merchant'     => $merchant,
                        //'brand'     => $brand,
                        );

        if($request->get('report-date-range') != ''){
            // get selected date
            $dateRange = str_replace(' ', '', $request->get('report-date-range'));
            $dateRange = explode('-', $dateRange);
            $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0]);
            $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1]);

            $startDate->hour = 00;
            $startDate->minute = 00;
            $startDate->second = 00;

            $endDate->hour = 23;
            $endDate->minute = 59;
            $endDate->second = 59;
            $totalDate = $startDate->diffFiltered(CarbonInterval::hour(), function(Carbon $date) { return $date->hour === 0; }, $endDate);

            $reports['startDate'] = $dateRange[0];
            $reports['endDate'] = $dateRange[1];
            $reports['totalDate'] = $totalDate;

        }else if($request->get('report-date-range') == ''){
            $startDate = null;
            $endDate = null;
            $totalDate = 180;   //6 months
            $reports['startDate'] = $dateRange[0];
            $reports['endDate'] = $dateRange[1];
            $reports['totalDate'] = $totalDate;

        }

        $getDataTable = json_decode($this->postGuzzleClient($reports, 'admin/generate-report/search')->getBody()->getContents());
        dd($getDataTable);
        return response()->json($getDataTable);;

    }

    public function getChannelList(){
        $channelList = array();
        $channelRepo = new ChannelRepo(new Channel);
        $channels = $channelRepo->all();
        foreach($channels as $channel){
             $content = array('name'=>$channel->name , 'id'=>$channel->id);
             $channelList[$channel->id] = $content;
        }
        return $channelList;
    }

    public function getMerchantList(){
        
        $merchantList = array();
        $merchantRepo = new MerchantRepo(new Merchant);
        $merchants = $merchantRepo->all();
        foreach($merchants as $merchant){
             $content = array('name'=>$merchant->name , 'slug'=>$merchant->slug);
             $merchantList[$merchant->id] = $content;
        }
        return $merchantList;
    }

    //public function getBrandList(){
    //    $brandList = array();
    //    $brandRepo = new BrandRepo(new Brand);
    //    $brands = $brandRepo->all();
    //    foreach($brands->brands as $brand){
    //         $content = array('name'=>$brand->name , 'prefix'=>$brand->prefix);
    //         $brandList[$brand->id] = $content;
    //    }
    //    return $brandList;
    //}

}