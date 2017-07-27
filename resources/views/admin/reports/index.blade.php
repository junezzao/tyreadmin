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
                            @lang('admin/reports.box_header_search_reports')
                        </h3>
                        
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <form id="filter-form">
                            <fieldset class="search-filters">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('admin/reports.form_label_search_reports_type')</label>
                                            <select id="report-type-select" name="report-type" class="form-control select2-nosearch">
                                                <option value="">@lang('admin/reports.form_label_placeholder_search_reports_type')</option>
                                                @foreach($reportTypes as $type)
                                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('admin/reports.form_label_search_reports_start_date')</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input name="report-date-range" placeholder="@lang('admin/reports.form_label_placeholder_search_reports_start_date')" type="text" class="form-control pull-right" id="report-date" onkeydown="if (event.keyCode == 13) return false;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('admin/reports.form_label_search_reports_date_range')</label>
                                            <select id="report-duration-select" name="report-duration" class="form-control select2-nosearch">
                                                <option value="">@lang('admin/reports.form_label_placeholder_search_reports_date_range')</option>
                                                @foreach($reportDurations as $index => $type)
                                                    <option value="{{ $index }}">{{ ucfirst($type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 merchant-filter">
                                        <div class="form-group">
                                            <label>@lang('admin/reports.form_label_search_reports_merchants')</label>
                                            <select id="merchant-select" name="merchant-slug" class="form-control" disabled>
                                                <option value="">@lang('admin/reports.form_label_placeholder_search_reports_merchants')</option>
                                                @foreach($merchantList as $merchant)
                                                    <option value="{{ $merchant['slug'] }}">{{ $merchant['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Report Generated Date:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input name="report-create-date-range" placeholder="Select date range" type="text" class="form-control pull-right" id="generated-date" onkeydown="if (event.keyCode == 13) return false;">
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button id="search-reports-btn" type="button" class="btn btn-info pull-right">
                                            <span>
                                                <i class="fa fa-search"></i> @lang('admin/reports.btn_label_placeholder_search')
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                        <span id="reportrange">
                            <span></span>
                        </span>
                        <div class="table-div">
                            <table id="reports-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <!--
                                        <th style="width: 5%"></th>
                                        -->
                                        <th style="width: 45%">@lang('admin/reports.table_label_reports_name')</th>
                                        <th style="width: 15%">@lang('admin/reports.table_label_reports_type')</th>
                                        <th style="width: 22%">@lang('admin/reports.table_label_reports_cycle_date')</th>
                                        <th style="width: 17%">@lang('admin/reports.table_label_reports_create_date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Reports List -->
                                </tbody>
                            </table>
                        </div>
                        <!--
                        <button type="button" class="btn btn-success pull-right">
                            <span>
                                <i class="fa fa-download"></i> Download
                            </span>
                        </button>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS', false) )}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS', false) )}}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<style>
    .box-body .row{
        margin-left: 0;
        margin-right: 0;
    }
    .merchant-filter{
        display: none;
    }
    .search-filters{
        background-color: #f7f7f9;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .table-div{
        padding: 5px;
        padding-top: 20px;
    }
</style>
<script type="text/javascript">
    //var reportKeys = new Array;

    function displayAjaxError(msg){
        var msg = msg || 'An error has occured, please try again later.';
        var errorDiv = '<div class="dialog-remove-on-hide">'+msg+'</div>';
        $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
        $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
        $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
        $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
    }

    function generateReportsRow(data){
        var output = {
            //'checkbox' : '<input class="key-checkbox" name="report-key[]" value="'+data.key+'" type="checkbox">',
            'name' : '<a href="'+data.url+'" target="_blank">'+data.label+'</a>',
            'type' : data.type,
            'cycleDate' : data.start_date+' - '+data.end_date,
            'createdDate' : data.created_date,
        };
        return output;
    }

    /*
    function attachCheckboxEvnt(){
        $('.key-checkbox').change(function() {
            var key = $(this).val();
            if($(this).is(":checked")) {
                reportKeys.push(key);
            }else{
                reportKeys = $.grep(reportKeys, function(value) {
                    return value != key;
                });
            }    
        });
    }
    */

    $(document).ready(function() {
        var reportsTable = $('#reports-table').DataTable({
            "lengthMenu": [[30, 50, 100], [30, 50, 100]],
            "pageLength": 30,
            "bFilter": false,
            "columns": [
                { "data": "name", "name": "name", "targets": 0 },
                { "data": "type", "name": "type", "targets": 1 },
                { "data": "cycleDate", "name": "cycleDate", "targets": 2 },
                { "data": "createdDate", "name": "createdDate", "targets": 3 },
            ],
            "drawCallback": function( settings ) {
                //attachCheckboxEvnt();
            },
        });

        //Date range as a button
        $('#report-date').daterangepicker(
            {
                format: 'DD/MM/YYYY',
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
                    $('#report-date').val(start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY'));
                }
            }
        );

        // To clear daterangepicker
        $('input[name="report-date-range"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /*
        $('#generated-date').daterangepicker(
            {
                format: 'DD/MM/YYYY',
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function (start, end) {
                $('#generated-date').val(start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY'));
            }
        );
        */
        //attachCheckboxEvnt();

        $("#report-type-select").change(function () {
            var value = this.value;
            if(value == "returns"){
                $('.merchant-filter').show();
                $('#merchant-select').prop("disabled", false);
            }else{
                $('.merchant-filter').hide();
                $('#merchant-select').prop("disabled", true);
            }
        });

        $('#search-reports-btn').on('click', function(){
            waitingDialog.show('Retrieving reports list...', {dialogSize: 'sm'});
            reportsTable.clear().draw();
            reportKeys = new Array;
            $.ajax({
                'url': "{{route('admin.reports.search')}}",
                'method': 'POST',
                'data': $('#filter-form').serialize(),
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    var tableOutput = '';
                    $.each(response, function(index, data){
                        reportsTable.row.add(generateReportsRow(data));
                    });
                    $('#reports-table tbody').html(tableOutput);
                    waitingDialog.hide();
                    reportsTable.draw();
                }
            });
        });
    });
</script>
@append