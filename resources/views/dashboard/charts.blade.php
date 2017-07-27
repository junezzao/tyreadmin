@extends('layouts.master')

@section('title')
    @lang('auth.page_title_dashboard')
@endsection

@section('header_scripts')
<!--link href="{{ asset('css/signin.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css"-->
<!-- Morris chart -->
<link rel="stylesheet" href="{{ asset('plugins/morris/morris.css',env('HTTPS',false)) }}">
@append

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            @lang('dashboard.content_header_dashboard') <small><i>@lang('dashboard.content_header_last_updated_at'): {{ $lastUpdatedAt }}</i></small>
        </h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        @include('dashboard.counters')
        <div class="channel-data">
            <div class="row">
                <div class="col-lg-9 col-xs-12">
                    <div class="channel-type-select-label">
                        Filter by channel types:
                    </div>
                    <div class="channel-type-select-div">
                        {!! Form::select('channelTypes[]', $channelTypes, NULL, array('id'=> 'channel-type-select', 'class' => 'form-control select2', 'multiple'=>'multiple')) !!}
                        <button class="btn bg-purple margin" type="button" id="update-graph-btn">Update</button>
                        <div class="small">By default, all channel types are selected.</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <!-- Custom Tabs (Pulled to the right) -->
                    <div class="nav-tabs-custom" id="daily-sales-div">
                        <ul class="nav nav-tabs pull-right">
                            <li><a href="#daily-sales-items-30-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_30_days')</a></li>
                            <li class="active"><a href="#daily-sales-items-7-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_7_days')</a></li>
                            <li class="pull-left header"><i class="fa fa-inbox"></i> @lang('dashboard.nav_tab_daily_sales_order_items')</li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="daily-sales-items-30-days-tab">
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <div class="chart line-chart" id="daily-sales-items-30-days"></div>
                                    </div>
                                    <div class="col-xs-12 col-md-4 daily-sales-pie-div">
                                        <div class="row">
                                            <span class="donut-chart-label">@lang('dashboard.nav_tab_dsoi_channel_types')</span>
                                            <div class="col-xs-12 col-md-7">
                                                <div class="chart-responsive">
                                                    <div class="chart donut-chart" id="daily-sales-items-30-days-donut-chart"></div>
                                                </div><!-- ./chart-responsive -->
                                            </div><!-- /.col -->
                                            <div class="col-xs-12 col-md-5">
                                                <ul id="donut-30-legend" class="chart-legend clearfix">
                                                    @if(!empty($totalChannelTypeCounts30))
                                                        @foreach($totalChannelTypeCounts30 as $channelType)
                                                            <li><i class="fa fa-circle" style="color: {{ $channelType['colorCode'] }};"></i> {{ $channelType['label'] }}</li>
                                                        @endforeach
                                                    @else
                                                        <li><i class="fa fa-circle text-gray"></i> No data</li>
                                                    @endif
                                                </ul>
                                            </div><!-- /.col -->
                                        </div>
                                        <div class="row pie-chart-legend-div">
                                            <span class="donut-chart-label">@lang('dashboard.nav_tab_dsoi_channels')</span>
                                            <div class="col-xs-12">
                                                <ul id="monthly-top-channels" class="nav nav-pills nav-stacked pie-chart-legend">
                                                    @if(isset($topChannelsInSale['monthly']['all']) &&count($topChannelsInSale['monthly']['all']) > 0)
                                                        @foreach($topChannelsInSale['monthly']['all'] as $channel)
                                                            <li><a href="#">{{ $channel['label'] }} <span class="pull-right">{{ $channel['value'] }}</span></a></li>
                                                        @endforeach
                                                    @else
                                                        <li><a href="#">No Data <span class="pull-right"></span></a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane active" id="daily-sales-items-7-days-tab">
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <div class="chart line-chart" id="daily-sales-items-7-days"></div>
                                    </div>
                                    <div class="col-xs-12 col-md-4 daily-sales-pie-div">
                                        <div class="row">
                                            <span class="donut-chart-label">@lang('dashboard.nav_tab_dsoi_channel_types')</span>
                                            <div class="col-xs-12 col-md-7">
                                                <div class="chart-responsive">
                                                    <div class="chart donut-chart" id="daily-sales-items-7-days-donut-chart"></div>
                                                </div><!-- ./chart-responsive -->
                                            </div><!-- /.col -->
                                            <div class="col-xs-12 col-md-5">
                                                <ul id="donut-7-legend" class="chart-legend clearfix">
                                                    @if(!empty($totalChannelTypeCounts7))
                                                        @foreach($totalChannelTypeCounts7 as $channelType)
                                                            <li><i class="fa fa-circle" style="color: {{ $channelType['colorCode'] }};"></i> {{ $channelType['label'] }}</li>
                                                        @endforeach
                                                    @else
                                                        <li><i class="fa fa-circle text-gray"></i> No data</li>
                                                    @endif
                                                </ul>
                                            </div><!-- /.col -->
                                        </div>
                                        <div class="row pie-chart-legend-div">
                                            <span class="donut-chart-label">@lang('dashboard.nav_tab_dsoi_channels')</span>
                                            <div class="col-xs-12">
                                                <ul id="weekly-top-channels" class="nav nav-pills nav-stacked pie-chart-legend">
                                                    @if(isset($topChannelsInSale['weekly']['all']) && count($topChannelsInSale['weekly']['all']) > 0)
                                                        @foreach($topChannelsInSale['weekly']['all'] as $channel)
                                                            <li><a href="#">{{ $channel['label'] }} <span class="pull-right">{{ $channel['value'] }}</span></a></li>
                                                        @endforeach
                                                    @else
                                                        <li><a href="#">No Data <span class="" s="pull-right"></span></a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.tab-content -->
                    </div><!-- nav-tabs-custom -->
                </div><!-- /.col -->
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="nav-tabs-custom" id="gmv-div">
                        <ul class="nav nav-tabs pull-right">
                            <li><a href="#gmv-30-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_30_days')</a></li>
                            <li class="active"><a href="#gmv-7-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_7_days')</a></li>
                            <li class="pull-left header"><i class="fa fa-inbox"></i> @lang('dashboard.nav_tab_gmv')</li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="gmv-30-days-tab">
                                <div class="chart line-chart" id="gmv-30-days"></div>
                            </div>
                            <div class="tab-pane active" id="gmv-7-days-tab">
                                <div class="chart line-chart" id="gmv-7-days"></div>
                            </div>
                        </div><!-- /.tab-content -->
                    </div><!-- nav-tabs-custom -->
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="nav-tabs-custom" id="daily-sales-order-div">
                        <ul class="nav nav-tabs pull-right">
                            <li><a href="#daily-sales-order-90-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_90_days')</a></li>
                            <li class="active"><a href="#daily-sales-order-30-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_30_days')</a></li>
                            <li><a href="#daily-sales-order-7-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_7_days')</a></li>
                            <li class="pull-left header"><i class="fa fa-inbox"></i> @lang('dashboard.nav_tab_daily_sales_orders')</li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="daily-sales-order-90-days-tab">
                                <div class="chart line-chart" id="daily-sales-order-90-days"></div>
                            </div>
                            <div class="tab-pane active" id="daily-sales-order-30-days-tab">
                                <div class="chart line-chart" id="daily-sales-order-30-days"></div>
                            </div>
                            <div class="tab-pane" id="daily-sales-order-7-days-tab">
                                <div class="chart line-chart" id="daily-sales-order-7-days"></div>
                            </div>
                        </div><!-- /.tab-content -->
                    </div><!-- nav-tabs-custom -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $totalMerchants }}</h3>
                                <p>@lang('dashboard.widget_box_total_merchants')</p>
                            </div>
                            <div class="icon">
                                <i class="fa ion-ios-people"></i>
                            </div>
                            <a href="{{ route('admin.merchants.index') }}" class="small-box-footer">
                                @lang('dashboard.widget_box_more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ $totalNewMerchantsSignup }}</h3>
                                <p>@lang('dashboard.widget_box_total_new_signups')</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <a href="{{ route('admin.merchants.index', ['filterBy'=>'newSignUps']) }}" class="small-box-footer">
                                @lang('dashboard.widget_box_more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3>{{ $totalActiveMerchants7 }}</h3>
                                <p>@lang('dashboard.widget_box_total_merchants_live_7')</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-pie-chart"></i>
                            </div>
                            <a href="{{ route('admin.merchants.index', ['filterBy'=>'liveByWeek']) }}" class="small-box-footer">
                                @lang('dashboard.widget_box_more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{ $totalActiveMerchants30 }}</h3>
                                <p>@lang('dashboard.widget_box_total_merchants_live_30')</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-pie-chart"></i>
                            </div>
                            <a href="{{ route('admin.merchants.index', ['filterBy'=>'liveByMonth']) }}" class="small-box-footer">
                                @lang('dashboard.widget_box_more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="col-xs-12 col-md-12 no-padding">
                    <div class="nav-tabs-custom" id="merchants-signed-div">
                        <ul class="nav nav-tabs pull-right">
                            <li><a href="#merchants-signed-30-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_30_days')</a></li>
                            <li class="active"><a href="#merchants-signed-7-days-tab" data-toggle="tab">@lang('dashboard.nav_tab_tab_7_days')</a></li>
                            <li class="pull-left header"><i class="fa fa-inbox"></i> @lang('dashboard.nav_tab_total_merchants_signed')</li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="merchants-signed-30-days-tab">
                                <div class="chart line-chart" id="merchants-signed-30-days"></div>
                            </div>
                            <div class="tab-pane active" id="merchants-signed-7-days-tab">
                                <div class="chart line-chart" id="merchants-signed-7-days"></div>
                            </div>
                        </div><!-- /.tab-content -->
                    </div><!-- nav-tabs-custom -->
                </div>
            </div>
        </div>
    </section>
    
@endsection

@section('footer_scripts')
<!-- Morris -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.1/raphael.min.js"></script>
<script src="{{ asset('plugins/morris/morris.min.js',env('HTTPS',false)) }}"></script>
<style>
.small-box h3{
    font-size: 25px;
    margin-bottom: 20px;
}

.small-box p{
    margin-bottom: 5px;
}

.small-box .icon{
    font-size: 66px;
}

.small-box:hover .icon{
    font-size: 75px;
}

.line-chart{
    width: 100%;
    height: 300px;
}

.line-chart svg{
    overflow: visible !important;
}

.donut-chart{
    width: 100%;
    height: 150px;
}

.daily-sales-pie-div{
    padding-top: 5px;
}

.pie-chart-legend-div{
    padding-top: 20px;
}

.pie-chart-legend{
    border-top: 1px solid #f4f4f4;
}

.pie-chart-legend.nav-stacked>li{
    border-bottom: 1px solid #f4f4f4;
}

.donut-chart-label{
    float: left;
    padding-bottom: 10px;
    font-size: 15px;
    font-weight: 500;
}

.channel-data{
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 20px;
    background-color: #f4f4f4;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    border-radius: 3px;
    border: 1px solid #D6D6D6;
}

.channel-type-select-div{
    margin-bottom: 15px;
}

.channel-type-select-div .small{
    color: #999;
}

#channel-type-select{
    width: 70%;
}

.channel-type-select-label{
    font-size: 16px;
    margin-bottom: 15px;
}

.small-box > .inner{
    height: 130px;
    padding: 15px;
}

.small-box > .small-box-footer{
    position: absolute;
    width: 100%;
}

.small-box .icon{
    padding-top: 10px;
}

.small-box{
    height: 170px;
}

</style>
<script type="text/javascript">
    function toCurrencyFormat(x) {
        x = parseFloat(Math.round(x * 100) / 100).toFixed(2);
        return 'MYR ' + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $(document).ready(function(){
        var weeklyChannelData = [];
        var monthlyChannelData = [];
        var trimonthlyChannelData = [];

        var allChannelTypes = {!! json_encode(array_keys($channelTypes)) !!};

        $('#channel-type-select').val(allChannelTypes).trigger('change');

        // Setup line charts data according to Morris chart 
        @foreach($weekly as $index => $datas)
            @if($index != 'merchants_signed')
                @foreach($datas as $chartType => $values)
                    @if($chartType != 'returned_order_items_count')
                        weeklyChannelData.{{ $chartType.'_'.$index }} = [];
                        weeklyChannelData.{{ $chartType.'_empty' }} = [];
                        @foreach($values as $date => $data)
                            @if($chartType == 'gmv')
                                weeklyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', gmv: {{ $data['value'] }} });
                                weeklyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', gmv: 0 });
                            @elseif($chartType == 'order_items_count')
                                weeklyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', orderItems: {{ $data['value'] }},  returnedItems: @if(isset($weekly[$index]['returned_order_items_count'][$date]['value'])){{ $weekly[$index]['returned_order_items_count'][$date]['value'] }} @else 0 @endif});
                                weeklyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', orderItems: 0,  returnedItems: 0});
                            @elseif($chartType == 'successful_orders_count')
                                weeklyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', sales: {{ $data['value'] }} });
                                weeklyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', sales: 0 });
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        @foreach($monthly as $index => $datas)
            @if($index != 'merchants_signed')
                @foreach($datas as $chartType => $values)
                    @if($chartType != 'returned_order_items_count')
                        monthlyChannelData.{{ $chartType.'_'.$index }} = [];
                        monthlyChannelData.{{ $chartType.'_empty' }} = [];
                        @foreach($values as $date => $data)
                            @if($chartType == 'gmv')
                                monthlyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', gmv: {{ $data['value'] }} });
                                monthlyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', gmv: 0 });
                            @elseif($chartType == 'order_items_count')
                                monthlyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', orderItems: {{ $data['value'] }},  returnedItems: @if(isset($monthly[$index]['returned_order_items_count'][$date]['value'])){{ $monthly[$index]['returned_order_items_count'][$date]['value'] }} @else 0 @endif});
                                monthlyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', orderItems: 0,  returnedItems: 0});
                            @elseif($chartType == 'successful_orders_count')
                                monthlyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', sales: {{ $data['value'] }} });
                                monthlyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', sales: 0 });
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        @foreach($trimonthly as $index => $datas)
            @if($index != 'merchants_signed')
                @foreach($datas as $chartType => $values)
                    @if($chartType != 'returned_order_items_count')
                        trimonthlyChannelData.{{ $chartType.'_'.$index }} = [];
                        trimonthlyChannelData.{{ $chartType.'_empty' }} = [];
                        @foreach($values as $date => $data)
                            @if($chartType == 'gmv')
                                trimonthlyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', gmv: {{ $data['value'] }} });
                                trimonthlyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', gmv: 0 });
                            @elseif($chartType == 'order_items_count')
                                trimonthlyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', orderItems: {{ $data['value'] }},  returnedItems: @if(isset($trimonthly[$index]['returned_order_items_count'][$date]['value'])){{ $trimonthly[$index]['returned_order_items_count'][$date]['value'] }} @else 0 @endif});
                                trimonthlyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', orderItems: 0,  returnedItems: 0});
                            @elseif($chartType == 'successful_orders_count')
                                trimonthlyChannelData['{{ $chartType.'_'.$index }}'].push({y: '{{ $date }}', sales: {{ $data['value'] }} });
                                trimonthlyChannelData['{{ $chartType.'_empty' }}'].push({y: '{{ $date }}', sales: 0 });
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        // setup data for top channels
        var channelSales = [];
        @foreach($topChannelsInSale as $dateType => $data)
            @foreach($data as $channelTypeId => $channels)
                channelSales.{{$dateType.'_'.$channelTypeId}} = [];
                @foreach($channels as $channel)
                    channelSales['{{$dateType.'_'.$channelTypeId}}'].push({ label: '{{$channel['label']}}', value: {{$channel['value']}} });
                @endforeach
            @endforeach
        @endforeach

        // setup data for channels donut chart
        var channelDonutChartData = [];
        channelDonutChartData.monthly_all = [];
        channelDonutChartData.weekly_all = [];
        channelDonutChartData.monthly_empty = { label: 'No data', value: 0 };
        channelDonutChartData.weekly_empty = { label: 'No data', value: 0 };

        @foreach($totalChannelTypeCounts30 as $channelTypeId => $channelType)
            channelDonutChartData.{{'monthly_'.$channelTypeId}} = { label: '{{$channelType['label']}}', value: {{$channelType['value']}} };
            channelDonutChartData['monthly_all'].push({ label: '{{$channelType['label']}}', value: {{$channelType['value']}} });
        @endforeach

        @foreach($totalChannelTypeCounts7 as $channelTypeId => $channelType)
            channelDonutChartData.{{'weekly_'.$channelTypeId}} = { label: '{{$channelType['label']}}', value: {{$channelType['value']}} };
            channelDonutChartData['weekly_all'].push({ label: '{{$channelType['label']}}', value: {{$channelType['value']}} });
        @endforeach

        if(channelDonutChartData['monthly_all'].length < 1){
            channelDonutChartData['monthly_all'].push({ label: 'No data', value: 0 });
        }

        if(channelDonutChartData['weekly_all'].length < 1){
            channelDonutChartData['weekly_all'].push({ label: 'No data', value: 0 });
        }
        
        var colourCodes = {!! json_encode($colours); !!};
        
        // console.log(colourCodes);

        // Sales chart
        var dailySales30Days = new Morris.Line({
            element: 'daily-sales-items-30-days',
            resize: true,
            data: monthlyChannelData['order_items_count_all'],
            xkey: 'y',
            ykeys: ['orderItems', 'returnedItems'],
            labels: ['Order Items', 'Returned Items'],
            lineColors: ['#3c8dbc', '#dd4b39'],
            gridIntegers: true,
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #3c8dbc'>\
                    Order Items: "+ data.orderItems +"<br>\
                </div>\
                <div class='morris-hover-point' style='color: #dd4b39'>\
                    Returned Items: "+ data.returnedItems +"<br>\
                </div>";
                return(html);
            },
        });

        // daily sales order 7 days chart/donut
        var dailySales7Days = new Morris.Line({
            element: 'daily-sales-items-7-days',
            resize: true,
            data: weeklyChannelData['order_items_count_all'],
            xkey: 'y',
            ykeys: ['orderItems', 'returnedItems'],
            labels: ['Order Items', 'Returned Items'],
            lineColors: ['#3c8dbc', '#dd4b39'],
            gridIntegers: true,
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #3c8dbc'>\
                    Order Items: "+ data.orderItems +"<br>\
                </div>\
                <div class='morris-hover-point' style='color: #dd4b39'>\
                    Returned Items: "+ data.returnedItems +"<br>\
                </div>";
                return(html);
            },
        });

        //Donut Chart
        var dailySales30DaysDonut = new Morris.Donut({
            element: 'daily-sales-items-30-days-donut-chart',
            resize: true,
            colors: [
                @if(!empty($colours))
                    @foreach($colours as $data)
                        "{{ $data }}",
                    @endforeach
                @else
                    "#d2d6de",
                @endif
            ],
            data: channelDonutChartData['monthly_all'],
            hideHover: 'auto'
        });

        var dailySales7DaysDonut = new Morris.Donut({
            element: 'daily-sales-items-7-days-donut-chart',
            resize: true,
            colors: [
                @if(!empty($colours))
                    @foreach($colours as $data)
                        "{{ $data }}",
                    @endforeach
                @else
                    "#d2d6de",
                @endif
            ],
            data: channelDonutChartData['weekly_all'],
            hideHover: 'auto'
        });

        // merchants signed 7 days chart
        var merchantsSigned7Days = new Morris.Line({
            element: 'merchants-signed-7-days',
            resize: true,
            data: [
                @if(!empty($weekly))
                @foreach($weekly['merchants_signed'] as $date => $data)
                    {y: '{{ $date }}', merchants: {{ $data['value'] }} },
                @endforeach
                @endif
            ],
            xkey: 'y',
            ykeys: ['merchants'],
            labels: ['Merchants'],
            lineColors: ['#00a65a'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #00a65a'>\
                    Merchants:\
                    "+ data.merchants +"\
                </div>";
                return(html);
            },
        });

        // merchants signed 30 days chart
        var merchantsSigned30Days = new Morris.Line({
            element: 'merchants-signed-30-days',
            resize: true,
            data: [
                @if(!empty($monthly))
                @foreach($monthly['merchants_signed'] as $date => $data)
                    {y: '{{ $date }}', merchants: {{ $data['value'] }} },
                @endforeach
                @endif
            ],
            xkey: 'y',
            ykeys: ['merchants'],
            labels: ['Merchants'],
            lineColors: ['#00a65a'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #00a65a'>\
                    Merchants:\
                    "+ data.merchants +"\
                </div>";
                return(html);
            },
        });

        // GMV 7 days chart
        var gmv7Days = new Morris.Line({
            element: 'gmv-7-days',
            resize: true,
            data: weeklyChannelData['gmv_all'],
            xkey: 'y',
            ykeys: ['gmv'],
            labels: ['GMV'],
            lineColors: ['#605ca8'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return toCurrencyFormat(y != Math.round(y)?'':y); },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #605ca8'>\
                    GMV:\
                    "+ toCurrencyFormat(data.gmv) +"\
                </div>";
                return(html);
            },
        });

        // GMV 30 days chart
        var gmv30Days = new Morris.Line({
            element: 'gmv-30-days',
            resize: true,
            data: monthlyChannelData['gmv_all'],
            xkey: 'y',
            ykeys: ['gmv'],
            labels: ['GMV'],
            lineColors: ['#605ca8'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return toCurrencyFormat(y != Math.round(y)?'':y); },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #605ca8'>\
                    GMV:\
                    "+ toCurrencyFormat(data.gmv) +"\
                </div>";
                return(html);
            },
        });

        // Daily sales orders 7 days chart
        var dso7Days = new Morris.Line({
            element: 'daily-sales-order-7-days',
            resize: true,
            data: weeklyChannelData['successful_orders_count_all'],
            xkey: 'y',
            ykeys: ['sales'],
            labels: ['Sales'],
            lineColors: ['#39CCCC'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #39CCCC'>\
                    Sales:\
                    "+ data.sales +"\
                </div>";
                return(html);
            },
        });

        // Daily sales orders 30 days chart
        var dso30Days = new Morris.Line({
            element: 'daily-sales-order-30-days',
            resize: true,
            data: monthlyChannelData['successful_orders_count_all'],
            xkey: 'y',
            ykeys: ['sales'],
            labels: ['Sales'],
            lineColors: ['#39CCCC'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #39CCCC'>\
                    Sales:\
                    "+ data.sales +"\
                </div>";
                return(html);
            },
        });

        // Daily sales orders 90 days chart
        var dso90Days = new Morris.Line({
            element: 'daily-sales-order-90-days',
            resize: true,
            data: trimonthlyChannelData['successful_orders_count_all'],
            xkey: 'y',
            ykeys: ['sales'],
            labels: ['Sales'],
            lineColors: ['#39CCCC'],
            hideHover: 'auto',
            smooth: false,
            xLabelFormat: function (x) { 
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = IndexToMonth[ x.getMonth() ];
                return x.getDate() + ' ' + month;
            },
            yLabelFormat: function(y){ return y != Math.round(y)?'':y; },
            hoverCallback: function(index, options, content) {
                var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var data = options.data[index];
                var date = new Date(data.y);
                var month = IndexToMonth[ date.getMonth() ];
                var html = "\
                <div class='morris-hover-row-label'>\
                    "+ date.getDate() +" "+ month +"\
                </div>\
                <div class='morris-hover-point' style='color: #39CCCC'>\
                    Sales:\
                    "+ data.sales +"\
                </div>";
                return(html);
            },
        });

        // to render secondary tab charts when changing tabs
        $('#daily-sales-div ul.nav a').on('shown.bs.tab', function (e) {
            //renderDailySales7DayCharts();
            dailySales7Days.redraw();
            dailySales30Days.redraw();
            dailySales30DaysDonut.redraw();
            dailySales7DaysDonut.redraw();
        });

        $('#merchants-signed-div ul.nav a').on('shown.bs.tab', function (e) {
            //renderDailySales7DayCharts();
            merchantsSigned7Days.redraw();
            merchantsSigned30Days.redraw();
        });


        $('#gmv-div ul.nav a').on('shown.bs.tab', function (e) {
            //renderDailySales7DayCharts();
            gmv7Days.redraw();
            gmv30Days.redraw();
        });

        $('#daily-sales-order-div ul.nav a').on('shown.bs.tab', function (e) {
            //renderDailySales7DayCharts();
            dso7Days.redraw();
            dso30Days.redraw();
            dso90Days.redraw();
        });

        var graphTypes = ['order_items_count', 'gmv', 'successful_orders_count'];

        // filter data based on selected channel types
        $('#update-graph-btn').on('click', function(){
            var selected = $('#channel-type-select').val();
            if(selected == null){
                setChannelTypeData();
                setTopSellingChannels();
                setDonutData();
            }else{
                setGraphData(selected);
                setTopSellingChannels(selected);
                setDonutData(selected);
            }
            redrawChannelTypesGraph();
            // console.log($(this).val());
        });

        function setTopSellingChannels(channelIds){
            var channelIds = channelIds || 'all';
            var monthlyData = [];
            var weeklyData = [];
            if(channelIds == 'all'){
                if(channelSales['monthly_all'] !== undefined){
                    $.each(channelSales['monthly_all'], function(value, data){
                        monthlyData.push(data);
                    });
                }

                if(channelSales['weekly_all'] !== undefined){
                    $.each(channelSales['weekly_all'], function(value, data){
                        weeklyData.push(data);
                    });
                }
            }else{
                $.each(channelIds, function(index, channelId){
                    if(channelSales['monthly_chnl_'+channelId] !== undefined){
                        $.each(channelSales['monthly_chnl_'+channelId], function(value, data){
                            monthlyData.push(data);
                        });
                    }

                    if(channelSales['weekly_chnl_'+channelId] !== undefined){
                        $.each(channelSales['weekly_chnl_'+channelId], function(value, data){
                            weeklyData.push(data);
                        });
                    }
                });
            }

            var monthlyDataHtml = '';
            var weeklyDataHtml = '';

            monthlyData.sort(SortByValue);
            monthlyData = monthlyData.slice( 0, 3 );

            weeklyData.sort(SortByValue);
            weeklyData = weeklyData.slice( 0, 3 );

            if(monthlyData.length > 0){
                $.each(monthlyData, function(index, data){
                    monthlyDataHtml += '<li><a href="#">'+data.label+' <span class="pull-right">'+data.value+'</span></a></li>';
                });
            }else{
                monthlyDataHtml = '<li><a href="#">No Data <span class="pull-right"></span></a></li>';
            }

            if(weeklyData.length > 0){
                $.each(weeklyData, function(index, data){
                    weeklyDataHtml += '<li><a href="#">'+data.label+' <span class="pull-right">'+data.value+'</span></a></li>';
                });
            }else{
                weeklyDataHtml = '<li><a href="#">No Data <span class="pull-right"></span></a></li>';
            }

            // update UI
            $('#weekly-top-channels').html(weeklyDataHtml);
            $('#monthly-top-channels').html(monthlyDataHtml);
        }

        function setDonutData(channelIds){
            var channelIds = channelIds || 'all';
            var donut7Data = [];
            var donut30Data = [];

            if(channelIds != 'all'){
                $.each(channelIds, function(index, channelId){
                    if(channelDonutChartData['weekly_'+channelId] !== undefined)
                        donut7Data.push(channelDonutChartData['weekly_'+channelId]);

                    if(channelDonutChartData['monthly_'+channelId] !== undefined)
                        donut30Data.push(channelDonutChartData['monthly_'+channelId]);
                });
            }else{
                donut30Data = channelDonutChartData['monthly_all'];
                donut7Data = channelDonutChartData['weekly_all'];
            }

            if(donut7Data.length < 1)
                donut7Data.push({ label: 'No data', value: 0 });

            if(donut30Data.length < 1)
                donut30Data.push({ label: 'No data', value: 0 });

            dailySales30DaysDonut.setData(donut30Data);
            dailySales7DaysDonut.setData(donut7Data);

            // update legend data
            var donut7Legend = '';
            var donut30Legend = '';
            $.each(donut30Data, function(index, data){
                // colourCodes
                donut30Legend += '<li><i class="fa fa-circle" style="color: '+colourCodes[index]+';"></i> '+data.label+'</li>';
            });

            $.each(donut7Data, function(index, data){
                donut7Legend += '<li><i class="fa fa-circle" style="color: '+colourCodes[index]+';"></i> '+data.label+'</li>';
            });

            $('#donut-30-legend').html(donut30Legend);
            $('#donut-7-legend').html(donut7Legend);
        }

        function setGraphData(channelIds){
            // Initializes data with empty data (has every date but with zero records.)
            // Loops through each of the channel IDs and check if there are data attached to it. If there isn't, skip it, if there is, add up all the data and set the graph to that data.

            // Only perform loops and checking if there are more than 1 channel types selected.
            if(channelIds.length > 1){
                // Uses $.extend to avoid updating the reference variables.
                var ds7Data = $.extend(true, [], weeklyChannelData['order_items_count_empty']);
                var ds30Data = $.extend(true, [], monthlyChannelData['order_items_count_empty']);
                var gmv7Data = $.extend(true, [], weeklyChannelData['gmv_empty']);
                var gmv30Data = $.extend(true, [], monthlyChannelData['gmv_empty']);
                var dso7Data = $.extend(true, [], weeklyChannelData['successful_orders_count_empty']);
                var dso30Data = $.extend(true, [], monthlyChannelData['successful_orders_count_empty']);
                var dso90Data = $.extend(true, [], trimonthlyChannelData['successful_orders_count_empty']);

                $.each(channelIds, function(index, channelId){
                    if(weeklyChannelData['order_items_count_'+channelId] !== undefined){
                        $.each(weeklyChannelData['order_items_count_'+channelId], function(objIndex, value){
                            ds7Data[parseInt(objIndex)].orderItems += value.orderItems;
                            ds7Data[parseInt(objIndex)].returnedItems += value.returnedItems;
                        });
                    }
                    dailySales7Days.setData(ds7Data);

                    if(monthlyChannelData['order_items_count_'+channelId] !== undefined){
                        $.each(monthlyChannelData['order_items_count_'+channelId], function(objIndex, value){
                            ds30Data[parseInt(objIndex)].orderItems += value.orderItems;
                            ds30Data[parseInt(objIndex)].returnedItems += value.returnedItems;
                        });
                    }
                    dailySales30Days.setData(ds30Data);

                    if(weeklyChannelData['gmv_'+channelId] !== undefined){
                        $.each(weeklyChannelData['gmv_'+channelId], function(objIndex, value){
                            gmv7Data[parseInt(objIndex)].gmv += value.gmv;
                        });
                    }
                    gmv7Days.setData(gmv7Data);

                    if(monthlyChannelData['gmv_'+channelId] !== undefined){
                        $.each(monthlyChannelData['gmv_'+channelId], function(objIndex, value){
                            gmv30Data[parseInt(objIndex)].gmv += value.gmv;
                        });
                    }
                    gmv30Days.setData(gmv30Data);

                    if(weeklyChannelData['successful_orders_count_'+channelId] !== undefined){
                        $.each(weeklyChannelData['successful_orders_count_'+channelId], function(objIndex, value){
                            dso7Data[parseInt(objIndex)].sales += value.sales;
                        });
                    }
                    dso7Days.setData(dso7Data);

                    if(monthlyChannelData['successful_orders_count_'+channelId] !== undefined){
                        $.each(monthlyChannelData['successful_orders_count_'+channelId], function(objIndex, value){
                            dso30Data[parseInt(objIndex)].sales += value.sales;
                        });
                    }
                    dso30Days.setData(dso30Data);

                    if(trimonthlyChannelData['successful_orders_count_'+channelId] !== undefined){
                        $.each(trimonthlyChannelData['successful_orders_count_'+channelId], function(objIndex, value){
                            dso90Data[parseInt(objIndex)].sales += value.sales;
                        });
                    }
                    dso90Days.setData(dso90Data);
                });
            }
            // If only 1 channel type is selected, straight set the data from that channel.
            else if(channelIds.length == 1){
                setChannelTypeData(channelIds[0]);
            }
                
        }

        function setChannelTypeData(channelId){
            // Will use all channel type data if no channel ID is given.
            var channelId = channelId || 'all';
            if(weeklyChannelData['order_items_count_'+channelId] === undefined){
                dailySales7Days.setData(weeklyChannelData['order_items_count_empty']);
            }
            else{
                dailySales7Days.setData(weeklyChannelData['order_items_count_'+channelId]);
            }
            
            if(monthlyChannelData['order_items_count_'+channelId] === undefined){
                dailySales30Days.setData(monthlyChannelData['order_items_count_empty']);
            }
            else{
                dailySales30Days.setData(monthlyChannelData['order_items_count_'+channelId]);
            }

            if(weeklyChannelData['gmv_'+channelId] === undefined){
                gmv7Days.setData(weeklyChannelData['gmv_empty']);
            }
            else{
                gmv7Days.setData(weeklyChannelData['gmv_'+channelId]);
            }

            if(monthlyChannelData['gmv_'+channelId] === undefined){
                gmv30Days.setData(monthlyChannelData['gmv_empty']);
            }
            else{
                gmv30Days.setData(monthlyChannelData['gmv_'+channelId]);
            }

            if(weeklyChannelData['successful_orders_count_'+channelId] === undefined){
                dso7Days.setData(weeklyChannelData['successful_orders_count_empty']);
            }
            else{
                dso7Days.setData(weeklyChannelData['successful_orders_count_'+channelId]);
            }
            
            if(monthlyChannelData['successful_orders_count_'+channelId] === undefined){
                dso30Days.setData(monthlyChannelData['successful_orders_count_empty']);
            }
            else{
                dso30Days.setData(monthlyChannelData['successful_orders_count_'+channelId]);
            }
                
            if(trimonthlyChannelData['successful_orders_count_'+channelId] === undefined){
                dso90Days.setData(trimonthlyChannelData['successful_orders_count_empty']);
            }
            else{
                dso90Days.setData(trimonthlyChannelData['successful_orders_count_'+channelId]);
            }
        }

        function redrawChannelTypesGraph(){
            dailySales7Days.redraw();
            dailySales30Days.redraw();
            gmv7Days.redraw();
            gmv30Days.redraw();
            dso7Days.redraw();
            dso30Days.redraw();
            dso90Days.redraw();
        }

        function SortByValue(a, b){
            var aName = a.value;
            var bName = b.value; 
            return ((aName > bName) ? -1 : ((aName < bName) ? 1 : 0));
        }

        // console.log(weeklyChannelData);
        // console.log(monthlyChannelData);
        // console.log(trimonthlyChannelData);
        
    });
</script>

@append

