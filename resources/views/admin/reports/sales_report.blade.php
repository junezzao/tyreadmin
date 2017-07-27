@extends('layouts.master')

@section('header_scripts')
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css', env('HTTPS', false)) }}">
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
@append

@section('title')
    @lang('admin/reports.page_title_reports')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="errors"></div>
    <section class="content-header">
        <h1>@lang('admin/reports.content_header_reports')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('admin/reports.box_header_generate_report')
                        </h3>

                    </div><!-- /.box-header -->
                    <div class="box-body">
                        @include('admin.reports.partial.generate-report-filter')
                    </div>
                    <div class="col-md-12">
                        <h4 class="pull-left">Sales Report {{ $data['duration'] }}</h4>
                        <div class="pull-left" style="margin-left: 20px;">{!! Form::open(['url' => '/admin/generate-report/export', 'method' => 'POST', 'id' => 'export']) !!}
                            {!! Form::hidden('data', htmlspecialchars(json_encode($data['sales']), ENT_QUOTES, 'UTF-8')) !!}
                            {!! Form::hidden('type', 'sales') !!}
                            {!! Form::hidden('duration', $data['duration']) !!}
                            {!! Form::submit('Export', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                        </div>
                    </div>
                    @if(isset($data['showCharts']) && $data['showCharts']==false)
                    <div class="row">
                        <div class="col-xs-offset-3 col-xs-6">
                            <div style="padding: 25px; text-align: center;">
                                <h4>Date range exceeded 3 months, only export function is available.</h4>
                            </div>
                        </div>
                    </div>
                    @elseif(!isset($data['showCharts']))
                    <div id="report-statistics" class="col-md-12">
                        <div class="report-summary col-md-4 col-xs-12">
                            <p><strong>Total Sales :</strong> RM {{ number_format($data['salesValue'], 2, '.', ',') }}</p>
                            <p><strong>Total Orders :</strong> {{ $data['ordersCount'] }}</p>
                            <p><strong>Total Sales Items:</strong> RM {{ number_format($data['salesItemsValue'], 2, '.', ',') }}</p>
                            <p><strong>Total Order Items :</strong> {{ $data['orderItemsCount'] }}</p>
                            <p><strong>Total Returns (quantity) :</strong> {{ $data['returns']['intransitReturns'] }} <span class="small">(In Transit)</span>, {{ $data['returns']['completedReturns'] }} <span class="small">(Completed)</span></strong></p>
                            <p><strong>Total Returns (value) :</strong> RM {{ number_format($data['returns']['intransitReturnsValue'], 2, '.', ',') }} <span class="small">(In Transit)</span>, RM {{ number_format($data['returns']['completedReturnsValue'], 2, '.', ',') }} <span class="small">(Completed)</span></strong></p>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <canvas id="sales-chart" width="100%" height="50%"></canvas>
                        </div>
                    </div>
                        <div class="table-div" style="padding: 10px;">
                            <table id="sales-report-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Merchant</th>
                                        <th style="width: 15%">Channel</th>
                                        <th style="width: 22%">Third Party Order Date</th>
                                        <th style="width: 17%">Order Completed Date</th>
                                        <th style="width: 17%">Order ID</th>
                                        <th style="width: 17%">Third Party Order ID</th>
                                        <th style="width: 17%">Hubwire SKU</th>
                                        <th style="width: 17%">Category</th>
                                        <th style="width: 17%">Quantity</th>
                                        <th style="width: 17%">Retail Price <span class="small">(inclusive GST)</span></th>
                                        <th style="width: 17%">Listing Price <span class="small">(inclusive GST)</span></th>
                                        <th style="width: 17%">Total Sales <span class="small">(exclusive GST)</span></th>
                                    </tr>
                                    <tr>
                                        <td data-index="0" class="search-col-text">Merchant</td>
                                        <td data-index="1" class="search-col-text">Channel</td>
                                        <td data-index="2" class="search-col-text">Third Party Order Date</td>
                                        <td data-index="3" class="search-col-text">Order Completed Date</td>
                                        <td data-index="4" class="search-col-text">Order ID</td>
                                        <td data-index="5" class="search-col-text">Third Party Order ID</td>
                                        <td data-index="6" class="search-col-text">Hubwire SKU</td>
                                        <td data-index="7" class="search-col-text">Category</td>
                                        <td data-index="8" class="search-col-text">Quantity</td>
                                        <td data-index="9" class="search-col-text">Retail Price</td>
                                        <td data-index="10" class="search-col-text">Listing Price</td>
                                        <td data-index="11" class="search-col-text">Total Sales</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['sales'] as $sale)
                                        <tr>
                                            <td>{{ $sale->Merchant }}</td>
                                            <td>{{ $sale->Channel }}</td>
                                            <td>{{ $sale->{'Third Party Order Date'} }}</td>
                                            <td>{{ $sale->{'Order Completed Date'} }}</td>
                                            <td><a href="{{ route('order.show', $sale->{'Order No'}) }}">{{ $sale->{'Order No'} }}</a></td>
                                            <td>{{ $sale->{'Third Party Order No'} }}</td>
                                            <td>{{ $sale->{'Hubwire SKU'} }}</td>
                                            <td>{{ $sale->{'Product Category'} }}</td>
                                            <td>{{ $sale->Quantity }}</td>
                                            <td>{{ $sale->{'Currency'} }} {{ $sale->{'Retail Price (Incl. GST)'} }}</td>
                                            <td>{{ $sale->{'Currency'} }} {{ $sale->{'Listing Price (Incl. GST)'} }}</td>
                                            <td>{{ $sale->{'Currency'} }} {{ $sale->{'Total Sales (Excl. GST)'} }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer_scripts')
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/chartjs/Chart.min.js', env('HTTPS', false)) }}"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    @if(!isset($data['showCharts']))
    // sales chart
    var ctx = $("#sales-chart");
    // template
    var myChart = new Chart(ctx, {
        type: 'bar',
        axisX:{
            intervalType: "month"
        },
        data: {
            
            labels: [ @foreach($data['dateRange'] as $datetype) "{{ $datetype }}", @endforeach ],
            
            datasets: [
                @foreach($data['top5'] as $channel => $datas){
                        label: "{{ $channel }}",
                        backgroundColor: "{{ $datas['color'] }}",
                        borderWidth: 1,
                        data: [ @foreach($datas['data'] as $date => $data) {{ $data }}, @endforeach ],
            
                    },
                @endforeach
            ]
            
        },
        options: {
            //stacked: true,
            scales: {
                xAxes: [{
                    stacked: true,
                    scaleLabel: {
                        display:true,
                        labelString: "Day"
                    },
                    intervalType: "week",
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    },
                    stacked: true,
                    scaleLabel: {
                        display:true,
                        labelString: "Quantity"
                    }
                }]
            },
        }
    });

    // Setup - add a text input to each footer cell
    $('#sales-report-table thead tr td.search-col-text').each(function(index, value){
        var title = $(this).text();
        $(this).html('<input id="search-col-'+$(this).data('index')+'" style="width:100%" type="text"/>');
    } );

    var table = $('#sales-report-table').DataTable({
        "dom": '<"pull-left"l><"pull-right"f><"clearfix"><"pull-right"p><"clearfix">t<"pull-left"i><"pull-right"p><"clearfix">',
        "lengthMenu": [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "orderCellsTop": true,
        "bSort": true,
    });
    
    table.columns().every(function (){
        var that = this;
        $('#search-col-'+this.index()).on('keyup change', function (){
            if (that.search() !== this.value){
                that
                    .search(this.value)
                    .draw();
            }
        });
    });
    @endif

    $('#report-date').daterangepicker({
        format: 'YYYY-MM-DD',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        },
    },
    function (start, end) {
        if(start._isValid && end._isValid)
        {
            $('#report-date').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        }
    }
    );

    // To clear daterangepicker
    $('input[name="report-date-range"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});
</script>
@append