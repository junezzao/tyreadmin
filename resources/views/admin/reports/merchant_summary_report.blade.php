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
                    <div class="box-body generate-report-container">
                        @include('admin.reports.partial.generate-report-filter')

                        <div class="merchant-breakdown-report-data">
                            <div class="row">
                                <h4 class="pull-left">
                                    Merchant Performance Report (<span class="selected-report-date-range"></span>)
                                </h4>
                                <div class="pull-right">
                                    {!! Form::open(['url' => '/admin/generate-report/export', 'method' => 'POST', 'id' => 'export']) !!}
                                        {!! Form::hidden('data', json_encode($merchants)) !!}
                                        {!! Form::hidden('type', 'merchants') !!}
                                        {!! Form::hidden('duration', str_replace(' ', '', $selectedDate)) !!}
                                        {!! Form::submit('Export', ['class' => 'btn btn-primary']) !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            @if($showCharts)
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="chart-responsive">
                                        <div class="chart donut-chart" id="top-10-merchant-chart"></div>
                                    </div>
                                    <div class="donut-chart-label">
                                        <h3>Top Selling Merchants</h3>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <ul class="chart-legend clearfix" style="font-size: larger;">
                                        @if(!empty($top10))
                                            @foreach($top10 as $data)
                                                <li><i class="fa fa-circle" style="color: {{ $data['colourCode'] }};"></i> {{ $data['name'] }}</li>
                                            @endforeach
                                        @else
                                            <li><i class="fa fa-circle text-gray"></i> No data</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                NOTE: Updating the date range will only affect columns Sold, GMV, Returns in-transit, Returns Completed and Cancellation.
                                <div class="table-div">
                                    <table id="reports-table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="max-width:200px;">Merchant</th>
                                                <th>Qty In-Hand</th>
                                                <th>Available Stock</th>
                                                <th>Sold</th>
                                                <th>GMV</th>
                                                <th>Returns In-Transit</th>
                                                <th>Returns Completed</th>
                                                <th>Cancellation</th>
                                            </tr>
                                            <tr>
                                                <td style="max-width:200px;" data-index="0" class="search-col-dd"></td>
                                                <td data-index="1" class="search-col-text"></td>
                                                <td data-index="2" class="search-col-text"></td>
                                                <td data-index="3" class="search-col-text"></td>
                                                <td data-index="4" class="search-col-text"></td>
                                                <td data-index="5" class="search-col-text"></td>
                                                <td data-index="6" class="search-col-text"></td>
                                                <td data-index="7" class="search-col-text"></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($merchants as $data)
                                                <tr>
                                                    <td style="max-width:200px;"><a href="#" class="merchant-breakdown-link" data-id="{{ $data['merchant_id'] }}">{{ $data['name'] }}</a></td>
                                                    <td>{{ $data['quantity_in_hand'] }}</td>
                                                    <td>{{ $data['available_stock'] }}</td>
                                                    <td>{{ $data['sold'] }}</td>
                                                    <td>{{ number_format((float)$data['gmv'], 2, '.', ',') }}</td>
                                                    <td>{{ $data['return_in_transit'] }}</td>
                                                    <td>{{ $data['return_complete'] }}</td>
                                                    <td>{{ $data['cancelled'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-xs-offset-3 col-xs-6">
                                    <div style="padding: 25px; text-align: center;">
                                        <h4>Date range exceeded 3 months, only export function is available.</h4>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.1/raphael.min.js"></script>
<script src="{{ asset('plugins/morris/morris.min.js',env('HTTPS',false)) }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    function toCurrencyFormat(x) {
        x = parseFloat(Math.round(x * 100) / 100).toFixed(2);
        return 'MYR ' + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    @if(isset($selectedDate))
        $('.selected-report-date-range').html('{{ $selectedDate }}');
    @endif

    @if($showCharts)
    //Donut Chart
    var top10MerchantsDonut = new Morris.Donut({
        element: 'top-10-merchant-chart',
        resize: true,
        colors: [
            @if(!empty($top10))
                @foreach($top10 as $data)
                    "{{ $data['colourCode'] }}",
                @endforeach
            @else
                "#d2d6de",
            @endif
        ],
        data: [
            @if(!empty($top10))
                @foreach($top10 as $data)
                    {label: "{!! $data['name'] . ' [' .($data['sold']). ']' !!}", value: {{ $data['gmv'] }} },
                @endforeach
            @else
                {label: "No data", value: 0},
            @endif
        ],
        hideHover: 'true',
        formatter: function (y, data) { return toCurrencyFormat(y) }, 
    });

    $('#reports-table thead tr td.search-col-text').each(function(index, value){
        $(this).html('<input id="search-col-'+$(this).data('index')+'" style="width:100%" type="text"/>');
    });

    var reportsTable = jQuery('#reports-table').DataTable({
        "dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
        "lengthMenu": [[30, 50, 100], [30, 50, 100]],
        "pageLength": 30,
        "order": [[4, "desc"]],
        "orderCellsTop": true,
        "columns": [
            { "data": "merchant", "name": "merchant", "targets": 0 },
            { "data": "qty", "name": "qty", "targets": 1 },
            { "data": "availableQty", "name": "availableQty", "targets": 2 }, 
            { "data": "sold", "name": "sold", "targets": 3 },
            { "data": "gmv", "name": "gmv", "targets": 4 },
            { "data": "returnsInTransit", "name": "returnsInTransit", "targets": 5 },
            { "data": "returnsCompleted", "name": "returnsCompleted", "targets": 6 },
            { "data": "cancelled", "name": "cancelled", "targets": 7 }
        ],
        initComplete: function(){
            this.api().columns().every(function(){
                var column = this;
                if(column.index() == 0){
                    var select = $('<select class="form-control select2" style="max-width: 200px;"><option value="">All</option></select>')
                        .appendTo($('.dataTable thead tr td:nth-child(' + (column.index() + 1) + ')').first().empty())
                        .on('change', function(){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column.search(val ? '^'+val+'$' : '', true, false).draw();
                        });
     
                    column.data().unique().sort().each(function(d, j){
                        select.append('<option value="'+d+'">'+d+'</option>')
                    });
                }
            });
        },
        "fnDrawCallback": function(o){
            $('.merchant-breakdown-link').on('click', function(e){
                e.preventDefault();
                $("#merchant").val($(this).data('id'));
                $("input[name='report-date-range']").val('{{ $selectedDate }}');
                $("select[name='report-type']").val('merchant');
                $('#filter-form').attr('target', '_blank');
                $('#filter-form').submit();
                $('#filter-form').removeAttr('target');;
            });
        }
    });

    // Apply the search
    reportsTable.columns().every(function (){
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
}); 
</script>
@append