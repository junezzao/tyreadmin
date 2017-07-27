<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\MerchantRepository as MerchantRepo;
use App\Repositories\Eloquent\ChannelRepository as ChannelRepo;
use App\Models\Merchant;
use Validator;
use Form;
use Log;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Helpers\Helper;
use Excel;
use DateInterval;
use DatePeriod;
use DateTime;

class ReportsController extends AdminController
{

    use GuzzleClient;

    protected $admin;
    protected $channelRepo;

    public function __construct(ChannelRepo $channelRepo)
    {
        $this->middleware('permission:view.reports');
        $this->middleware('permission:view.generatereport');
        $this->channelRepo = $channelRepo;

        $this->admin = \Auth::user();
    }

    public function index()
    {
        $merchantList = $this->getMerchantList(true);
        $reportTypes = array('inventory', 'returns', 'Sales', 'channel_inventory');
        $reportDurations = array(
            0   => 'Daily', 
            6   => 'Weekly', 
            30  => 'Monthly'
        );

        $data['reportTypes'] = $reportTypes;
        $data['merchantList'] = $merchantList;
        $data['reportDurations'] = $reportDurations;

        return view('admin.reports.index', $data);
    }

    public function search(Request $request){

        //dd($this->admin->timezone);
        $reports = array();

        $s3Instance = \AWS::createClient('s3');

        $s3Bucket = env('AWS_S3_BUCKET');

        $filterByDuration = false;

        //check report type
        if($request->get('report-type') != '' ){ 
            $reportTypes[] = $request->get('report-type');
        }else{
            $reportTypes = array('inventory', 'returns', 'Sales', 'channel_inventory');
        }

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
        }

        if($request->get('report-duration') != ''){
            $filterByDuration = true;
        }
        foreach($reportTypes as $reportType){
            $objects = $s3Instance->getIterator('ListObjects', array(
                "Bucket" => $s3Bucket,
                "Prefix" => 'reports/'.$reportType.'/' //must have the trailing forward slash "/"
            ));
            foreach($objects as $object){
                $reportKey = explode('/', $object['Key']);
                $reportDetails = explode('_', $reportKey[4]);

                if($reportType == 'inventory'){
                    $createDateIndex = 3;
                    $reportDateIndex = 2;
                }elseif($reportType == 'Sales'){
                    $createDateIndex = 3;
                    $reportDateIndex = 2;
                }elseif($reportType == 'channel_inventory'){
                    $createDateIndex = 4;
                    $reportDateIndex = 3;
                }elseif($reportType == 'returns'){
                    if(sizeof($reportDetails) == 4){
                        // is a summary returns report
                        $createDateIndex = 3;
                        $reportDateIndex = 2;
                    }else{
                        $createDateIndex = 4;
                        $reportDateIndex = 3;
                    }
                }

                // get report start date
                $reportCycleDate = explode('-', $reportDetails[$reportDateIndex]);
                $reportStartDate = Carbon::createFromFormat('Ymd', $reportCycleDate[0]);
                $reportStartDate->hour = 00;
                $reportStartDate->minute = 00;
                $reportStartDate->second = 00;

                $reportEndDate = Carbon::createFromFormat('Ymd', $reportCycleDate[1]);
                $reportEndDate->hour = 23;
                $reportEndDate->minute = 59;
                $reportEndDate->second = 59;


                // \Log::info(print_r($reportStartDate, true));

                if($request->get('report-date-range') != ''){
                    // check if report date is in between selected dates
                    if($reportStartDate->between($startDate, $endDate)){
                        $valid = true;
                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = true;
                }
                // \Log::info($valid);
                if($valid && $filterByDuration){
                    $reportDuration = $reportStartDate->diffInDays($reportEndDate);
                    if ($request->get('report-duration') == 30) {
                        $totalDayOfMonth = Carbon::createFromFormat('Y-m-d H:i:s', $reportEndDate)->daysInMonth;
                        $totalDay = $totalDayOfMonth-1;
                    }else {
                        $totalDay = $request->get('report-duration');
                    }

                    if($reportDuration == $totalDay){
                        $valid = true;
                    }else{
                        $valid = false;
                    }
                }
                // \Log::info($valid);
                if($valid && $reportType == 'returns' && $request->get('merchant-slug') != ''){
                    if($reportDetails[0] == $request->get('merchant-slug')){
                        $valid = true;
                    }else{
                        $valid = false;
                    }
                }
                // \Log::info($valid);
                if($valid){
                    // to remove file type extension
                    $url = $s3Instance->getObjectUrl($s3Bucket, $object['Key']);
                    $createdDate = explode('.', $reportDetails[$createDateIndex]);
                    $createdDate = Carbon::createFromFormat('YmdHis', $createdDate[0]);
                    $createdDate = Helper::convertTimeToUserTimezone($createdDate->format('Y-m-d H:i:s'), $this->admin->timezone, 'd/m/Y H:i:s');
                    $report = array(
                        'key'           => $object['Key'],
                        'label'         => $reportKey[4],
                        'type'          => ucfirst($reportType),
                        'created_date'  => $createdDate,
                        'start_date'    => $reportStartDate->format('d/m/Y'),
                        'end_date'      => $reportEndDate->format('d/m/Y'),
                        'url'           => $url,
                    );

                    $reports[] = $report;
                }
            }
        }
        return response()->json($reports);
    }

    public function getMerchantList($s3 = false){
        if($s3 == true) {
            $s3Instance = \AWS::createClient('s3');
            $s3Bucket = env('AWS_S3_BUCKET');
            $merchantList = array();
            //$merchant = $merchantRepo->findBy('slug', $slug);

            $objects = $s3Instance->getIterator('ListObjects', array(
                "Bucket" => $s3Bucket,
                "Prefix" => 'reports/returns/' //must have the trailing forward slash "/"
            ));
            foreach($objects as $object){
                $reportKey = explode('/',$object['Key']);
                $reportDetails = explode('_', $reportKey[4]);

                if(sizeof($reportDetails) == 4){
                    // is a summary returns report
                    $valid = false;
                }else{
                    $valid = true;
                }

                if($valid){
                    $merchantSlug = $reportDetails[0];
                    $merchantRepo = new MerchantRepo(new Merchant);
                    $merchant = $merchantRepo->findBy('slug', $merchantSlug);
                    if($merchant){
                        $content = array('name'=>$merchant->name , 'slug'=>$merchant->slug);
                        $merchantList[$merchant->id] = $content;
                    }
                }
            }
        }else{
            $merchantList = array();
            $merchantRepo = new MerchantRepo(new Merchant);
            $merchants = $merchantRepo->all();
            foreach($merchants as $merchant){
                 $content = array('name'=>$merchant->name , 'id'=>$merchant->id);
                 $merchantList[$merchant->id] = $content;
            }
        }
        return $merchantList;
    }

    public function getChannelList(){
        $channelList = array();
        $channels = $this->channelRepo->all();
        foreach($channels as $channel){
            if ($channel->channel_type->name != 'Warehouse') {
                $content = array('name'=>$channel->name , 'id'=>$channel->id);
                $channelList[$channel->id] = $content;
            }
             
        }
        return $channelList;
    }

    public function generateReportIndex()
    {
        $data = $this->getGenerateReportSearchFilters(); 

        return view('admin.reports.generate-report-index', $data);
    }

    public function generateReport(Request $request)
    {
        $this->validate($request, [
            'report-type' => 'required',
            'report-date-range' => 'required',
        ]);
        // add in handle for validation error

        $data = $this->getGenerateReportSearchFilters();
        
        if ($request->input('report-type') == 'sales') {
            $data['data'] = $this->generateSalesReport($request);
            return view('admin.reports.sales_report', $data);
        } elseif($request->input('report-type') == 'merchant') {
            $data = $this->generateMerchantReport($request);
            // dd($data['hwSkus']);
            // return merchant report view
            return  view($data['template'], $data);
        }
    }

    public function getGenerateReportSearchFilters(){
        $data['merchantList'] = $this->getMerchantList();
        $data['channelList'] = $this->getChannelList();
        $data['reportTypes'] = config('globals.generate_report_type');

        return $data;
    }

    public function generateMerchantReport(Request $request)
    {
        $postData = array();
        $reportData = array();
        $colours = config('globals.dashboard_color_codes');
        $multiMerchant = false;

        if(!$request->get('merchant')){
            $postData['merchant'] = array();
            $multiMerchant = true;
        }else{
            $postData['merchant'] = $request->get('merchant');
            if(count($postData['merchant']) > 1){
                $multiMerchant = true;
            }else{
                $multiMerchant = false;
            }
        }
        // dd($postData);
        if($multiMerchant){
            // post to merchant summary
            $postData['report-type'] = 'merchant';
            $postData['report-date-range'] = $request->get('report-date-range');

            $response = json_decode($this->postGuzzleClient($postData, 'reports/generate')->getBody()->getContents());

            // dd($response);

            $reportData = $this->getGenerateReportSearchFilters();
            $reportData['merchants'] = json_decode( json_encode($response), true);;
            $reportData['template'] = 'admin.reports.merchant_summary_report';
            $reportData['top10Data'] = array();

            // hide UI charts and tables if date range exceeded 3 months
            $dateRange = explode(' - ', $request->get('report-date-range'));
            $startDate = Carbon::createFromFormat('Y-m-d', $dateRange[0]);
            $endDate = Carbon::createFromFormat('Y-m-d', $dateRange[1]);

            $dateDuration = $startDate->diffInDays($endDate);
            // \Log::info($dateDuration);
            // detect if more than 3 months
            if($dateDuration > 91){
                $reportData['showCharts'] = false;
            }else{
                $reportData['showCharts'] = true;

                if(count($reportData['merchants']) > 10){
                    $qty = 0;
                    $value = 0;
                    $top10Merchants = array_slice($reportData['merchants'], 0, 9);
                    $otherMerchants = array_slice($reportData['merchants'], 9);
                    foreach($otherMerchants as $merchant){
                        $qty += $merchant['sold'];
                        $value += $merchant['gmv'];
                    }
                    $otherMerchant = array();
                    $otherMerchant['name'] = 'Others';
                    $otherMerchant['sold'] = $qty;
                    $otherMerchant['gmv'] = $value;
                    $top10Merchants[] = $otherMerchant;
                }else{
                    $top10Merchants = $reportData['merchants'];
                }

                // set colour codes
                foreach($top10Merchants as $index => $merchant){
                    $top10Merchants[$index]['colourCode'] = $colours[$index];
                }

                $reportData['top10'] = $top10Merchants;
            }
            $reportData['selectedMerchants'] = $request->get('merchant');
        }else{
            // post to merchant breakdown

            // get from and to date
            $dateRange = explode(' - ', $request->get('report-date-range'));
            $postData['from_date'] = $dateRange[0];
            $postData['to_date'] = $dateRange[1];

            $response = json_decode($this->getGuzzleClient($postData, 'reports/merchant/'.$postData['merchant'][0].'/breakdown')->getBody()->getContents());

            $reportData = $this->getGenerateReportSearchFilters();

            // hide UI charts and tables if date range exceeded 3 months
            $dateRange = explode(' - ', $request->get('report-date-range'));
            $startDate = Carbon::createFromFormat('Y-m-d', $dateRange[0]);
            $endDate = Carbon::createFromFormat('Y-m-d', $dateRange[1]);

            $dateDuration = $startDate->diffInDays($endDate);
            // \Log::info($dateDuration);

            $reportData['hwSkus'] = $response->table;
            $reportData['template'] = 'admin.reports.merchant_breakdown_report';

            if($dateDuration > 91){
                $reportData['showCharts'] = false;
            }else{
                $reportData['summaryData'] = $response->summary;
                $reportData['summaryData']->totalStockInHand = 0;
                // dd($response->table);

                // calculate total qty in hand
                foreach($response->table as $hwSku){
                    // add quantity in hand
                    if($hwSku->quantity_in_hand >= 0)
                        $reportData['summaryData']->totalStockInHand += $hwSku->quantity_in_hand;
                }

                usort($reportData['summaryData']->num_and_value_items_sold_per_channel, array($this, "sortByChannelGMV"));

                // set colour codes for channels
                foreach($reportData['summaryData']->num_and_value_items_sold_per_channel as $index => $channels){
                    $reportData['summaryData']->num_and_value_items_sold_per_channel[$index]->colourCode = $colours[$index];
                }

                $reportData['showCharts'] = true;
            }



            // $reportData['top10'] = $top10Products;
            $reportData['selectedMerchants'] = $postData['merchant'][0];

            // dd($reportData['summaryData']);
        }
        $reportData['selectedReport'] = $request->get('report-type'); 
        $reportData['selectedDate'] = $request->get('report-date-range');

        return $reportData;
    }

    public function generateSalesReport(Request $request)
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', '600');

        $input = $request->except('_token');

        $salesData = json_decode($this->postGuzzleClient($input, 'reports/generate')->getBody()->getContents()); 
        $sales = $salesData->saleMasterData;

        //count the number of date range
        $getDate = $salesData->dateRange;
        $getStartDate = explode(' ', $getDate[0]);
        $getEndDate = explode(' ', $getDate[1]);
        $dates[0] = $getStartDate[0];
        $dates[1] = $getEndDate[0];

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod(Carbon::createFromFormat('Y-m-d', $dates[0]), $interval, Carbon::createFromFormat('Y-m-d', $dates[1]));
        
        $dateLabels = array();
        foreach ( $period as $dt ){
            $dateLabels[] = $dt->addDay()->format('Y-m-d');
        }
        $interval = count($dateLabels);

        if ($interval > 91) {
           $returnData = array('sales' => $sales, 'duration' => $request->input('report-date-range'), 'showCharts' => false);
        }else{
            //get return value
            $input['report-type'] = 'returns';
            $returnsData = json_decode($this->postGuzzleClient($input, 'reports/generate')->getBody()->getContents());
            $ordersCount = $salesData->totalOrders;
            $salesValue = $salesData->totalOrdersValue;
            $orderItemsCount = $salesData->totalOrderItems;
            $salesItemsValue = $salesData->totalOrderItemsValue;
            $counters = array();

            foreach ($sales as $sale) {
                $createdDate = Carbon::createFromFormat('d/m/Y H:i:s', $sale->{'Order Completed Date'})->toDateString();
                
                if (array_key_exists($sale->Channel, $counters)) {
                    if (isset($counters[$sale->Channel][$createdDate])) {
                        $counters[$sale->Channel][$createdDate] = $counters[$sale->Channel][$createdDate] + 1;
                        
                    }else{
                        $counters[$sale->Channel][$createdDate] = 1;
                    }
                }
                else {
                    $counters[$sale->Channel][$createdDate] = 1;
                }
            }
        
            $intransitReturns = 0;
            $completedReturns = 0;
            $intransitReturnsValue = 0;
            $completedReturnsValue = 0;
            
            foreach ($returnsData as $status => $returns) {
                if($status == 'pending'){
                    foreach ($returns as $return) {
                         $intransitReturns+=1;
                        $intransitReturnsValue = $intransitReturnsValue + $return->amount;
                    }
                } 
                elseif($status == 'completed'){
                    foreach ($returns as $return) {
                        $completedReturns+=1;   
                        $completedReturnsValue = $completedReturnsValue + $return->amount; 
                   }   
                }
            }
            $returnsData = ['intransitReturns' => $intransitReturns, 'intransitReturnsValue' => $intransitReturnsValue, 'completedReturns' => $completedReturns, 'completedReturnsValue' => $completedReturnsValue];

            
            $d = 0;
            $chartData = [];
            $dateAndCount = array();
            foreach ($counters as $channel => $data) {
                $count = 0;
                $dateIndex = array();
                foreach ($data as $date => $value) {
                    $count += $value;
                    $dateIndex[$date] = $value;
                    for($i = 0; $i < $interval; $i++){
                        //\Log::info('loop '.$i);
                        if (!isset($dateAndCount[$dateLabels[$i]])) {
                            $dateAndCount[$dateLabels[$i]] = 0;
                        }
                        if ($dateLabels[$i]==$date) {
                            $dateAndCount[$dateLabels[$i]] += $value;
                        } 
                    }
                }
                $chartData[$channel] = [ 'date' => $dateIndex, 'count' => $count,'dateAndCount' => $dateAndCount];
                $d++;
            }

            $color = array();
            $colours = config('globals.dashboard_color_codes');
            //sort top five
            foreach($chartData as $channel){ 
                foreach($channel as $key=>$value){ 
                    if(!isset($sortArray[$key])){ 
                        $sortArray[$key] = array(); 
                    } 
                    $sortArray[$key][] = $value; 
                } 
            } 
            $orderby = "count";
            if(!isset($input['merchant']) && !empty($chartData)){
                array_multisort($sortArray[$orderby],SORT_DESC,$chartData); 
            }

            $dateRange=[];

            //dd($chartData);

            //find the totalday in one month by compare the start day month and end day month
            $totalDayOfMonth = 30;
            $getMonth = explode('-', $input['report-date-range']);
            if ($getMonth[1] == $getMonth[4]) {
                $totalDayOfMonth = cal_days_in_month(CAL_GREGORIAN, $getMonth[1], $getMonth[0]);
            }

            $top5 =[];
            //daily, weekly, monthly
            if($interval < 8){
                //daily
                for($j = 0; $j < $interval; $j++){
                    $dateRange[] = $dateLabels[$j];
                } 
                for($i = 0; $i < $interval; $i++){
                    //compess chart data with top 5
                    $x = 0;
                    $value = 0;
                    foreach ($chartData as $channel => $data) {
                        if($x<5){
                            foreach ($data['date'] as $date => $num) {
                                for($i = 0; $i < $interval; $i++){
                                    
                                    if (!isset($top5[$channel]['data'][$dateLabels[$i]])) {
                                        $top5[$channel]['data'][$dateLabels[$i]] = 0;
                                    }
                                    if ($dateLabels[$i]==$date) {
                                        $top5[$channel]['data'][$dateLabels[$i]] += $num;
                                    } 
                                    //$top5[$channel]['test'] = collect($top5[$channel]['day'])->sum();
                                    
                                }
                            }$top5[$channel]['color'] = $colours[$x];
                            
                        }else if ($x>=5){
                            foreach ($data['date'] as $date => $num) {
                                for($i = 0; $i < $interval; $i++){
                                    
                                    if (!isset($top5['other']['data'][$dateLabels[$i]])) {
                                        $top5['other']['data'][$dateLabels[$i]] = 0;
                                    }
                                    if ($dateLabels[$i]==$date) {
                                        $top5['other']['data'][$dateLabels[$i]] += $num;
                                    } 
                               
                                }
                            } $top5['other']['color'] = $colours[$x];
                        }

                        $x++;
                    }
                }
                
            }else if(8 <= $interval && $interval <= $totalDayOfMonth){
                //week
                $week = 1;
                $weekend = Carbon::createFromFormat('Y-m-d H', $dateLabels[0].' 0');
                $lastDayOri = Carbon::createFromFormat('Y-m-d H', $dateLabels[$interval-1].' 0');
                for ($k=0; $k < $interval; $k++) { 
                    $firstDay = $weekend; 
                    Carbon::setTestNow($firstDay);
                    $weekend = new Carbon('next monday');
                    $lastDay = ($weekend->diffInDays($lastDayOri, false)<=0)?$lastDayOri:(clone $weekend)->subDay();
                    $dayInWeek = $lastDay->diffInDays($firstDay);
                    $k=$k-1+$dayInWeek; //-1 beacause k++
                    if ($k<$interval) {
                        $weekLabels[$week] = $firstDay->toDateString().' - '.$lastDay->toDateString();
                        $dayNumberPerWeek[$week] = $dayInWeek+1;
                    }
                    $week++;
                }

                $week = 1;
                foreach ($weekLabels as $weekLabel) {
                    $dateRange[$week] = $weekLabel;
                    $week++;
                }

                    //compess chart data with top 5
                    $x = 0;
                    $value = 0;
                    foreach ($chartData as $channel => $data) {
                        //create empty date array
                        for($j = 0; $j < $interval; $j++){
                            $dateEmpty[$dateLabels[$j]] = 0;
                        }
                        foreach ($data['date'] as $date1 => $num1) {
                            foreach ($dateEmpty as $date2 => $num2) {
                                if($date1 == $date2){
                                    $dateEmpty[$date1] = $num1;
                                }
                            }
                        }

                        if($x<5){
                             $y = 1;
                             $z = 0;
                            foreach ($dateEmpty as $date2 => $num2) {
                                if ($dayNumberPerWeek[$y]>$z) {\Log::info($date2);
                                    if (!isset($top5[$channel]['data']['week'.(int)$y])) {
                                        $top5[$channel]['data']['week'.(int)$y] = 0;
                                    } 
                                    $top5[$channel]['data']['week'.(int)$y] += $num2;
                                }else if ($dayNumberPerWeek[$y]>=$z){
                                    \Log::info($date2);
                                    if (!isset($top5[$channel]['data']['week'.(int)$y])) {
                                        $top5[$channel]['data']['week'.(int)$y] = 0;
                                    } 
                                    $top5[$channel]['data']['week'.(int)$y] += $num2;
                                    $y++;
                                    $z=-1;
                                }
                                $z++;
                            } 

                        $top5[$channel]['color'] = $colours[$x];;
                            
                        }else if ($x>=5){
                             $y = 1;
                             $z = 0;
                            foreach ($dateEmpty as $date2 => $num2) {
                                if ($dayNumberPerWeek[$y]>$z) {\Log::info($date2);
                                    if (!isset($top5['other']['data']['week'.(int)$y])) {
                                        $top5['other']['data']['week'.(int)$y] = 0;
                                    } 
                                    $top5['other']['data']['week'.(int)$y] += $num2;
                                }else if ($dayNumberPerWeek[$y]>=$z){
                                    \Log::info($date2);
                                    if (!isset($top5['other']['data']['week'.(int)$y])) {
                                        $top5['other']['data']['week'.(int)$y] = 0;
                                    } 
                                    $top5['other']['data']['week'.(int)$y] += $num2;
                                    $y++;
                                    $z=-1;
                                }
                                $z++;
                            } 

                            $top5['other']['color'] = $colours[10];
                        }

                        $x++;
                    }
                
                
            }else if($interval > $totalDayOfMonth){
                //month
               for($i = 0; $i < $interval; $i++){
                    //compess chart data with top 5
                    $x = 0;
                    $value = 0;
                    foreach ($chartData as $channel => $data) {
                        if($x<5){
                            foreach ($data['date'] as $date => $num) {
                                $y = explode('-', $date);
                                $dateRange[$y[1]] = date("F", mktime(0, 0, 0, $y[1], 10));
                                for($i = 0; $i < $interval; $i++){
                                    
                                    if (!isset($top5[$channel]['data']['month'.$y[1]])) {
                                        $top5[$channel]['data']['month'.$y[1]] = 0;
                                    }
                                    if ($dateLabels[$i]==$date) {
                                        $top5[$channel]['data']['month'.$y[1]] += $num;
                                    } 
                                   
                                } 
                            }$top5[$channel]['color'] = $colours[$x];
                            
                        }else if ($x>=5){
                            foreach ($data['date'] as $date => $num) {
                                $y = explode('-', $date);
                                $dateRange[$y[1]] = date("F", mktime(0, 0, 0, $y[1], 10));
                                for($i = 0; $i < $interval; $i++){
                                    
                                    if (!isset($top5['other']['data']['month'.$y[1]])) {
                                        $top5['other']['data']['month'.$y[1]] = 0;
                                    }
                                    if ($dateLabels[$i]==$date) {
                                        $top5['other']['data']['month'.$y[1]] += $num;
                                    } 
                                
                                }
                            }
                            $top5['other']['color'] = $colours[10];
                        }

                        

                        $x++;
                    }
                }
            }
            //dd($top5);
            $returnData = array('sales' => $sales, 'returns' => $returnsData, 'top5' => $top5, 'dateRange' => $dateRange, 'ordersCount' => $ordersCount, 'orderItemsCount' => $orderItemsCount, 'counters' => $chartData, 'salesValue' => $salesValue, 'salesItemsValue' => $salesItemsValue, 'duration' => $request->input('report-date-range'));
        }

        
        return $returnData;
    }

    public function generateReportExport(Request $request)
    {   
        $data = json_decode($request->input('data'), true);

        // exclude "Product Category" column from sales report csv file
        foreach ($data as &$row) {
            // merchant report
            if (array_key_exists('category_name', $row)) {
                $category = $row['category_name'];
                if (!is_null($category))
                    $category = explode('/', $category);
                
                // place categories right after the product_name index
                $row = array_slice($row, 0, 4, true) +
                        array('main_category' => isset($category[0])? $category[0] : '',
                               'sub_category' => isset($category[1])? $category[1] : '',
                               'sub-sub_category' => isset($category[2])? $category[2] : '',
                             ) +
                        array_slice($row, 4, count($row) - 1, true) ;

                unset($row['category_name']);
            }
            // sales report
            else {
                unset($row['Product Category']);
            }
        }
        
        $duration = $request->input('duration');
        $reportType = str_replace(' ', '_', strtolower($request->input('type')));
        $filename = $reportType.'_report_'.$duration; 

        $excel = Excel::create($filename, function($excel) use($data, $reportType, $duration) {

            $excel->sheet('Master List', function($sheet) use($data, $reportType, $duration) {

                $sheet->fromArray($data, null, 'A1', true);

                $sheet->prependRow( array('') );
                $sheet->prependRow(
                            array(ucwords(str_replace('_', ' ', $reportType)).' Report ('. $duration .')')
                        );
            });

            $excel->setActiveSheetIndex(0);
        })->download('csv');
    }

    private function sortByChannelGMV($a, $b)
    {
        $t1 = $a->sum;
        $t2 = $b->sum;
        return $t2 - $t1;
    } 
}