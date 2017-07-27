<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Validator;
use Form;
use Log;
use Session;
use Carbon\Carbon;

class DashboardController extends Controller
{

    use GuzzleClient;

    protected $admin;

    protected $userRepo;

    public function __construct()
    {
        $this->middleware('auth');
        $this->admin = \Auth::user();
    }   

    public function index()
    {
        if($this->admin->can('view.dashboardcharts')){
            $data = $this->generateDashboardData();
            $data['counters'] = json_decode($this->getGuzzleClient(array(), 'admin/dashboard_counters')->getBody()->getContents(), true);
            return view('dashboard.charts', $data);
        }else{
            
            return view('dashboard.index');
        }    
    }

    public function generateDashboardData(){
        $data = array();
        $lineCharts = array('returned_order_items_count', 'order_items_count', 'gmv', 'merchants_signed', 'successful_orders_count');
        $donutCharts = array('order_items_channel_type_count');
        // get array of colours for donut chart
        $colours = config('globals.dashboard_color_codes');

        $response = json_decode($this->getGuzzleClient(array(), 'admin/dashboard')->getBody()->getContents(), true);

        $channelTypes = array();

        // dd($response);

        if(empty($response['last_updated'])){
            $response['last_updated'] = date('Y-m-d H:i:s');
        }
        $lastUpdatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $response['last_updated']);

        // to create empty date range array to comply with empty response
        $oneWeekAgoDate = Carbon::createFromFormat('Y-m-d H:i:s', $response['last_updated'], $this->admin->timezone)->setTimezone('UTC')->subDays(6);
        $oneMonthAgoDate = Carbon::createFromFormat('Y-m-d H:i:s', $response['last_updated'], $this->admin->timezone)->setTimezone('UTC')->subDays(30);
        $threeMonthsAgoDate = Carbon::createFromFormat('Y-m-d H:i:s', $response['last_updated'], $this->admin->timezone)->setTimezone('UTC')->subMonths(3);

        $lastReportDay = clone $lastUpdatedAt;
        $lastReportDay = $lastReportDay->subDays(1);

        $oneWeekAgoDateRange = parent::generateDateRange($oneWeekAgoDate, $lastReportDay);
        $oneMonthAgoDateRange = parent::generateDateRange($oneMonthAgoDate, $lastReportDay);
        $threeMonthsAgoDate = parent::generateDateRange($threeMonthsAgoDate, $lastReportDay);

        $oneWeekEmpty = array();
        $oneMonthEmpty = array();
        $threeMonthsEmpty = array();

        foreach($oneWeekAgoDateRange as $date){
            $dateLabel = Carbon::createFromFormat('Y-m-d', $date)->format('jS M');
            $oneWeekEmpty[$date] = array('value'=>0);
        }

        foreach($oneMonthAgoDateRange as $date){
            $dateLabel = Carbon::createFromFormat('Y-m-d', $date)->format('jS M');
            $oneMonthEmpty[$date] = array('value'=>0);
        }

        // dd($oneMonthEmpty);
        // \Log::info($oneMonthEmpty);
        // \Log::info($oneWeekEmpty);

        foreach($threeMonthsAgoDate as $date){
            $dateLabel = Carbon::createFromFormat('Y-m-d', $date)->format('jS M');
            $threeMonthsEmpty[$date] = array('value'=>0);
        }

        if(!empty($response['weekly']))
        {
            // formatting response data [weekly] (for line charts)
            foreach($response['weekly'] as $index => $datas){
                if(in_array($index, $lineCharts)){
                    // is line chart data values
                    $data['weekly']['all'][$index] = $oneWeekEmpty;
                    if($index != 'merchants_signed'){
                        foreach($datas as $channelId => $values){
                            // dd($values);
                            $channelTypes[$channelId] = $channelId;
                            $data['weekly'][$channelId][$index] = $oneWeekEmpty;
                            foreach($values as $value){
                                // dd($value);
                                $data['weekly']['all'][$index][$value['date']]['value'] += $value['count'];
                                $data['weekly'][$channelId][$index][$value['date']]['value'] = $value['count'];
                            }
                        }
                    }else{
                        $data['weekly'][$index] = $oneWeekEmpty;
                        foreach($datas as $value){
                            $data['weekly'][$index][$value['date']]['value'] = $value['count'];
                        }
                    }
                }
            }
        }

        if(!empty($response['monthly']))
        {
            // formatting response data [monthly] (for line charts)
            foreach($response['monthly'] as $index => $datas){
                if(in_array($index, $lineCharts)){
                    // is line chart data values
                    if($index != 'merchants_signed'){
                        $data['monthly']['all'][$index] = $oneMonthEmpty;
                        foreach($datas as $channelId => $values){
                            // dd($values);
                            $channelTypes[$channelId] = $channelId;
                            $data['monthly'][$channelId][$index] = $oneMonthEmpty;
                            foreach($values as $value){
                                // dd($value);
                                $data['monthly']['all'][$index][$value['date']]['value'] += $value['count'];
                                $data['monthly'][$channelId][$index][$value['date']]['value'] = $value['count'];
                            }
                        }
                    }else{
                        $data['monthly'][$index] = $oneMonthEmpty;
                        foreach($datas as $value){
                            $data['monthly'][$index][$value['date']]['value'] = $value['count'];
                        }
                    }
                }
            }
        }

        if(!empty($response['trimonthly']))
        {
            // formatting response data [3 months] (for line charts)
            foreach($response['trimonthly'] as $index => $datas){
                if(in_array($index, $lineCharts)){
                    // is line chart data values
                    if($index != 'merchants_signed'){
                        $data['trimonthly']['all'][$index] = $threeMonthsEmpty;
                        foreach($datas as $channelId => $values){
                            // dd($values);
                            $channelTypes[$channelId] = $channelId;
                            $data['trimonthly'][$channelId][$index] = $threeMonthsEmpty;
                            foreach($values as $value){
                                // dd($value);
                                $data['trimonthly']['all'][$index][$value['date']]['value'] += $value['count'];
                                $data['trimonthly'][$channelId][$index][$value['date']]['value'] = $value['count'];
                            }
                        }
                    }else{
                        $data['trimonthly'][$index] = $threeMonthsEmpty;
                        foreach($datas as $value){
                            $data['trimonthly'][$index][$value['date']]['value'] = $value['count'];
                        }
                    }
                }
            }
        }

        $data['totalChannelTypeCounts7'] = array();
        $data['totalChannelTypeCounts30'] = array();

        // formatting response data [weekly] (for channel type donut charts)
        if(!empty($response['weekly']['order_items_channel_type_count'])){
            $index = 0;
            foreach($response['weekly']['order_items_channel_type_count'] as $chnlId => $channelType){
                $channelTypeData = array(
                    'label'     => $response['weekly']['order_items_channel_type_count'][$chnlId][0]['name'],
                    'value'     => $response['weekly']['order_items_channel_type_count'][$chnlId][0]['count'],
                    'colorCode' => $colours[$index],
                );
                $data['totalChannelTypeCounts7'][$chnlId] = $channelTypeData;
                $index++;
            }
        }

        // formatting response data [monthly] (for channel type donut charts)
        if(!empty($response['monthly']['order_items_channel_type_count'])){
            $index = 0;
            foreach($response['monthly']['order_items_channel_type_count'] as $chnlId => $channelType){
                $channelTypeData = array(
                    'label'     => $response['monthly']['order_items_channel_type_count'][$chnlId][0]['name'],
                    'value'     => $response['monthly']['order_items_channel_type_count'][$chnlId][0]['count'],
                    'colorCode' => $colours[$index],
                );
                $data['totalChannelTypeCounts30'][$chnlId] = $channelTypeData;
                $index++;
            }
        }

        $channelItemsSoldCount = array('weekly'=>array(),'monthly'=>array());

        // formatting response data [weekly] for channel count
        if(!empty($response['weekly']['order_items_channels_count'])){
            foreach($response['weekly']['order_items_channels_count'] as $channelId => $channelData){
                foreach($channelData as $channel){
                    $saleInfo = array(
                        'label'     => $channel['name'],
                        'value'     => $channel['count'],
                    );
                    $channelItemsSoldCount['weekly']['chnl_'.$channelId][] = $saleInfo;
                    $channelItemsSoldCount['weekly']['all'][] = $saleInfo;
                    // $data['totalChannelCounts7'][] = $channelData;
                }
            }
        }

        if(!empty($response['monthly']['order_items_channels_count'])){
            foreach($response['monthly']['order_items_channels_count'] as $channelId => $channelData){
                foreach($channelData as $channel){
                    $saleInfo = array(
                        'label'     => $channel['name'],
                        'value'     => $channel['count'],
                    );
                    $channelItemsSoldCount['monthly']['chnl_'.$channelId][] = $saleInfo;
                    $channelItemsSoldCount['monthly']['all'][] = $saleInfo;
                    // $data['totalChannelCounts7'][] = $channelData;
                }
            }
        }

        if(isset($channelItemsSoldCount['weekly']['all']) && count($channelItemsSoldCount['weekly']['all']) > 0){
            usort($channelItemsSoldCount['weekly']['all'], array($this, "sortByValue"));
            $channelItemsSoldCount['weekly']['all'] = array_slice($channelItemsSoldCount['weekly']['all'], 0, 3);
        }

        if(isset($channelItemsSoldCount['monthly']['all']) && count($channelItemsSoldCount['monthly']['all']) > 0){
            usort($channelItemsSoldCount['monthly']['all'], array($this, "sortByValue"));
            $channelItemsSoldCount['monthly']['all'] = array_slice($channelItemsSoldCount['monthly']['all'], 0, 3);
        }

        $data['topChannelsInSale'] = $channelItemsSoldCount;

        // get channels
        $postChannelTypes = array(
            'channel_type_id' => $channelTypes,
        );
        $channelTypes = json_decode($this->postGuzzleClient($postChannelTypes, 'channels/admin/get-channel-types')->getBody()->getContents(), true);
        $channelTypeResponse = array();
        foreach($channelTypes as $channelType){
            $channelTypesResponse[$channelType['id']] = $channelType['name'];
        }

        $data['channelTypes'] = $channelTypesResponse;
        $data['lastUpdatedAt'] = $lastUpdatedAt->format('d M Y H:i:s');
        $data['totalMerchants'] = !empty($response['monthly']['total_merchants'])?$response['monthly']['total_merchants']:0;
        $data['totalNewMerchantsSignup'] = !empty($response['weekly']['new_signups'])?$response['weekly']['new_signups']:0;
        $data['totalActiveMerchants7'] = !empty($response['weekly']['active_merchants'])?$response['weekly']['active_merchants']:0;
        $data['totalActiveMerchants30'] = !empty($response['monthly']['active_merchants'])?$response['monthly']['active_merchants']:0;
        $data['colours'] = $colours;
        //dd($data['topChannelsInSale']);
        return $data;
    }

    private function sortByValue($a, $b)
    {
        $t1 = $a['value'];
        $t2 = $b['value'];
        return $t2 - $t1;
    } 
}
