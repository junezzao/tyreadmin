@extends('layouts.master')

@section('header_scripts')
    <!-- DataTables -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
@append

@section('title')
    @lang('admin/reports.page_title_tp_reports')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="errors"></div>
    <section class="content-header">
        <h1>@lang('admin/reports.content_header_finance')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('admin/reports.box_header_tp_report')
                        </h3>
                        <button type="button" id="export" data-toggle="modal" data-target="#generateReportModal" class="btn btn-default pull-right">Generate Report</button>
                        <button type="button" id="upload" data-toggle="modal" data-target="#uploadModal" class="btn btn-default pull-right" style="margin-right:5px;">@lang('admin/reports.button_upload_tp_report')</button>
                        <a href="{{ route('admin.reports.download_tp_template') }}" class="btn btn-default pull-right" style="margin-right:5px;" target="_blank">@lang('admin/reports.button_download_tp_template')</a>
                    </div><!-- /.box-header -->

                    <div id="summary" class="box-body">
                        <div class="row">
                            <div class="counter col-md-3 col-sm-6 col-xs-12" data-countername="num_orders">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Number of Orders</span>
                                        <span class="info-box-number" id="count_num_orders">0</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="counter col-md-3 col-sm-6 col-xs-12" data-countername="num_order_items">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="ion-ios-pricetags-outline"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Order Items</span>
                                        <span class="info-box-number" id="count_num_order_items">0</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="counter col-md-3 col-sm-6 col-xs-12 hide" data-countername="gmv_uploaded">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="ion ion-arrow-graph-up-right"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Total GMV (Uploaded)</span>
                                        <span class="info-box-number" id="count_gmv_uploaded">0</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="counter col-md-3 col-sm-6 col-xs-12" data-countername="gmv_arc">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="ion ion-social-usd-outline"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Total GMV (ARC)</span>
                                        <span class="info-box-number" id="count_gmv_arc">0</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                        </div>

                        <!-- Nav tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#" id="pending-tp-payment" aria-controls="pending-tp-payment" role="tab" data-toggle="tab">@lang('admin/reports.tab_title_pending_tp_payment') <span class="badge info" id="count-pending-tp">0</span></a></li>
                                <li role="presentation"><a href="#" id="pending-payment-merchant" aria-controls="tp-payment" role="tab" data-toggle="tab">@lang('admin/reports.tab_title_pending_payment_to_merchant') <span class="badge info" id="count-pending-merchant">0</span></a></li>

                                <li role="presentation"><a href="#" id="paid-merchant" aria-controls="tp-payment" role="tab" data-toggle="tab">@lang('admin/reports.tab_title_paid_to_merchant') <span class="badge info" id="count-paid-merchant">0</span></a></li>

                                <li role="presentation"><a href="#" id="completed" aria-controls="completed" role="tab" data-toggle="tab">@lang('admin/reports.tab_title_completed')</a></li>
                            </ul>

                             <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active clearfix" id="tp-payment">
                                    <div class="col-xs-12 no-padding" >
                                        <!-- to be moved to external stylesheet -->
                                        <div id="options" class="row pull-right" style="width: 100%; margin-right: 0px; margin-bottom: 10px;">
                                            <button type="button" id="complete-orders-unpaid" class="hide btn btn-default">
                                                @lang('admin/reports.button_complete_orders') (<span class="num-verified-items-unpaid">{{$numVerifiedItems}}</span>) 
                                            </button>
                                            <button type="button" id="complete-orders-paid" class="hide btn btn-default">
                                                @lang('admin/reports.button_complete_orders') (<span class="num-verified-items-paid">{{$numVerifiedItems}}</span>) 
                                            </button>
                                            <div class="dropdown">
                                                <button type="button" id="discard" class="btn btn-default dropdown-toggle hide" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Discard...</button>
                                                <ul class="dropdown-menu dropdown-menu-right hide discard-options" data-index="1">
                                                    @foreach($discard as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="discard-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                                <ul class="dropdown-menu dropdown-menu-right hide discard-options" data-index="2">
                                                    @foreach($discard as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="discard-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="dropdown">
                                                <button type="button" id="verify" class="btn btn-default dropdown-toggle hide" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Verify...</button>
                                                <ul class="dropdown-menu dropdown-menu-right hide verify-options" data-index="1">
                                                    @foreach($verify as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="verify-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                                <ul class="dropdown-menu dropdown-menu-right hide verify-options" data-index="2">
                                                    @foreach($verify as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="verify-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="dropdown">
                                                <button type="button" id="moveTo" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Move to...</button>
                                                <ul class="dropdown-menu dropdown-menu-right moveTo-options" data-index="0">
                                                    @foreach($moveTo as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="moveTo-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Export...</button>
                                                <ul class="dropdown-menu dropdown-menu-right export-options" data-index="0">
                                                    @foreach($optionsPendingTPPayment as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="export-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                                <ul class="dropdown-menu dropdown-menu-right hide export-options" data-index="1">
                                                    @foreach($optionsPendingPaymentToMerchant as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="export-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                                <ul class="dropdown-menu dropdown-menu-right hide export-options" data-index="2">
                                                    @foreach($optionsPendingPaymentToMerchant as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="export-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                                <ul class="dropdown-menu dropdown-menu-right hide export-options" data-index="3">
                                                    @foreach($optionsComplete as $index=>$option)
                                                        <li><a href="#" data-option="{{$option}}" class="export-option">{{$option}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div id="counters" class="row hide" style="width: 100%; margin: 10px; margin-bottom: 10px;">
                                            <h4>
                                             <label class="label label-info">Not Found <span class="badge badge-info" id="not_found">0</span></label>&nbsp;
                                             <label class="label label-info">Unverified <span class="badge badge-info" id="unverified">0</span></label>&nbsp;
                                             <label class="label label-info">Verified <span class="badge badge-info" id="verified">0</span></label>&nbsp;
                                            </h4>
                                        </div>

                                        <div id="remarks_div" class="row hide">
                                             <div class="col-xs-6">
                                             <label>Filter By Remarks: {!! Form::select('remarks', $remarks, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/reports.remarks_filter'))) !!} </label>
                                             </div>
                                        </div>
                                        
                                        <table id="tp-reports" width="100%" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>{!!Form::checkbox('select-all', '')!!}</th>
                                                <th>{{trans('admin/reports.table_label_id')}}</th>
                                                <th>{{trans('admin/reports.table_label_order_item_id')}}</th>
                                                <th>{{trans('admin/reports.table_label_order_id')}}</th>
                                                <th>{{trans('admin/reports.table_label_tp_item_ref_id')}}</th>
                                                <th>{{trans('admin/reports.table_label_tp_order_id')}}</th>
                                                <th>{{trans('admin/reports.table_label_channel')}}</th>
                                                <th>{{trans('admin/reports.table_label_listing_price')}}</th>
                                                <th>{{trans('admin/reports.table_label_sold_price')}}</th>
                                                <th>{{trans('admin/reports.table_label_merchant')}}</th>
                                                <th>{{trans('admin/reports.table_label_tp_order_date')}}</th>
                                                <th>{{trans('admin/reports.table_label_shipped_date')}}</th>
                                                <th>{{trans('admin/reports.table_label_status')}}</th>
                                                <th>{{trans('admin/reports.table_label_created_at')}}</th>
                                                <th>{{trans('admin/reports.table_label_updated_at')}}</th>
                                                <th>{{trans('admin/reports.table_label_last_attended_by')}}</th>
                                                <th>{{trans('admin/reports.table_label_actions')}}</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            <tr class="search-row"> 
                                                <td></td>
                                                <td class="search-data text">{!! Form::text( 'id', null, ['class' => 'form-control'] ) !!}</td>
                                                <td class="search-data text">{!! Form::text( 'order_item_id', null, ['class' => 'form-control'] ) !!}</td>
                                                <td class="search-data text">{!! Form::text( 'order_id', null, ['class' => 'form-control'] ) !!}</td>
                                                <td class="search-data text">{!! Form::text( 'tp_item_id', null, ['class' => 'form-control'] ) !!}</td>
                                                <td class="search-data text">{!! Form::text( 'tp_order_code', null, ['class' => 'form-control'] ) !!}</td>
                                                <td class="search-data text">{!! Form::select('channel_type', $channelTypes, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/reports.channel_type_placeholder'), 'style'=>'width:100%;')) !!}</td>
                                                <td></td>
                                                <td></td>
                                                <td class="search-data text">{!! Form::select('merchant_name', $merchants, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/reports.merchant_placeholder'), 'style'=>'width:100%;')) !!}</td>
                                                <td class="search-data date">{!! Form::text( 'tp_order_date', null, ['class' => 'form-control filter-date', 'placeholder' => trans('admin/channels.sync_history_table_placeholder_date_range'), 'readonly' => true] ) !!}</td>
                                                <td class="search-data date">{!! Form::text( 'shipped_date', null, ['class' => 'form-control filter-date', 'placeholder' => trans('admin/channels.sync_history_table_placeholder_date_range'), 'readonly' => true] ) !!}</td>
                                                <td class="search-data text">{!! Form::select('status', $statuses, null, array('class' => 'form-control select2', 'placeholder' => 'All Statuses', 'style'=>'width:100%;')) !!}</td>
                                                <td class="search-data date">{!! Form::text( 'created_at', null, ['class' => 'form-control filter-date', 'placeholder' => trans('admin/channels.sync_history_table_placeholder_date_range'), 'readonly' => true] ) !!}</td>
                                                <td class="search-data date">{!! Form::text( 'updated_at', null, ['class' => 'form-control filter-date', 'placeholder' => trans('admin/channels.sync_history_table_placeholder_date_range'), 'readonly' => true] ) !!}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overlay">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                    </div>

                    <div class="overlay">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                    </div>

                    {!! Form::open(array('id'=>'export-form' ,'url' => route('admin.tp_reports.export'), 'method' => 'POST')) !!}
                        {!! Form::hidden('tab'); !!}
                        {!! Form::hidden('option'); !!}
                        {!! Form::hidden('ids'); !!}
                    {!! Form::close() !!}

                    {!! Form::open(array('id'=>'discard-form' ,'url' => route('admin.tp_reports.discard'), 'method' => 'POST')) !!}
                        {!! Form::hidden('tab'); !!}
                        {!! Form::hidden('option'); !!}
                        {!! Form::hidden('ids'); !!}
                    {!! Form::close() !!}

                    {!! Form::open(array('id'=>'verify-form' ,'url' => route('admin.tp_reports.bulk_verify'), 'method' => 'POST')) !!}
                        {!! Form::hidden('tab'); !!}
                        {!! Form::hidden('option'); !!}
                        {!! Form::hidden('ids'); !!}
                    {!! Form::close() !!}

                    {!! Form::open(array('id'=>'moveTo-form' ,'url' => route('admin.tp_reports.bulk_moveTo'), 'method' => 'POST')) !!}
                        {!! Form::hidden('tab'); !!}
                        {!! Form::hidden('option'); !!}
                        {!! Form::hidden('ids'); !!}
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="uploadModal" aria-labelledby="uploadModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">@lang('admin/reports.upload_modal_title_tp_report')</h4>
                </div>
                <div class="modal-body">
                    <div id="log" class="alert alert-danger" role="alert" style="display: none;">
                    </div>
                    <span class="upload-form">
                        <label for="file_upload">@lang('admin/reports.upload_modal_label_upload')</label>
                        <br/>
                        <input id="file_upload" type="file" name="tp_report">
                        <p class="help-block">@lang('admin/reports.upload_modal_help_tp_report')</p>
                    </span>
                    <span class="progress-div">
                        <label>@lang('admin/reports.upload_modal_label_progress')</label>
                        <div class="progress progress-sm">
                            <div id="progress" class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            </div>
                        </div>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('admin/reports.upload_modal_button_close')</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="generateReportModal" aria-labelledby="generateReportModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Generate Payment Report</h4>
                </div>
                <div class="modal-body">
                    <div id="log" class="alert alert-danger" role="alert" style="display: none;">
                    </div>
                    {!! Form::open(array('id'=>'generateReport-form' ,'url' => route('admin.tp_reports.generateReport'), 'method' => 'POST')) !!}
                    <div class="form-group row">
                        <div class="col-xs-3">
                            Select Channel : 
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('channel[]', $channels, null, ['class'=>'select2', 'multiple' => 'multiple', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3">
                            Select Merchant : 
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('merchant[]', $merchants, null, ['class'=>'select2', 'multiple' => 'multiple', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3">
                            @lang('admin/reports.form_label_search_reports_start_date')
                        </div>
                        <div class="col-xs-9">
                            {!! Form::text('month', null, ['id' => 'month', 'class'=>'form-control datepicker search-col', 'placeholder' => 'Select Month', 'data-col'=>'5', 'style' => 'width:200px']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-primary pull-right">Generate Report</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop


@section('footer_scripts')
<style>
    .info-box {
        min-height: 60px;
    }
    .info-box-icon {
        height: 60px; 
        width: 60px;
        line-height: 60px;
    }
    .info-box-content {
        margin-left:60px;
    }
</style>
<script src="{{ asset('js/jquery_ui_widgets.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<link href="{{ asset('packages/blueimp/css/jquery.fileupload.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('packages/blueimp/js/jquery.fileupload.js', env('HTTPS', false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.iframe-transport.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS', false) )}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS', false) )}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // For uploading file
        $('#file_upload').fileupload({
            url: '{{ route("admin.reports.tp_reports.upload") }}',
            dataType: 'json',
            add: function (e, data) {
                $('#log').empty();
                $('#log').hide();
                $('#progress').addClass('active');
                $('#progress').css('width', '0%');

                data.submit();
            },
            done: function (e, data) {
                var result = data.result;
                $('#progress').removeClass('active');            
                if(result.success){
                    $('#log').empty();
                    $('#log').removeClass('alert-danger').addClass('alert-success').show();
                    $.each(result.messages, function (index, message){
                        $('<p/>').html(message).appendTo('#log');
                    });
                    loadCounters();
                    if ($('#pending-payment-merchant').parent().hasClass('active')) 
                        tpOrdersTable.ajax.reload();
                    else
                        $('#pending-payment-merchant').click();
                }
                else{
                    $('#log').empty();
                    $('<p/>').html('<h4>Upload process return error(s):-</h4>').appendTo('#log');

                    $.each(result.errors, function (index, error){
                        $('<p/>').html(error).appendTo('#log');
                    });

                    $('#log').removeClass('alert-success').addClass('alert-danger').show();
                    
                    setTimeout(function(){
                        $('#progress').css('width', '0%');
                    }, 1000);
                }
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress').css('width', progress + '%');
            }
        }).prop('disabled', !$.support.fileInput)

            .parent().addClass($.support.fileInput ? undefined : 'disabled');     

        // get selected tab index on page load
        var selectedTabIndex = $('a[data-toggle="tab"]').parent(".active").index();

        /* 
            CHECKBOXES RELATED CODE
        */
        var selectedItems = {0: [], 1: [], 2: [], 3: []};

        $("input[name='select-all']").on('click', function() {

            var checkboxes = $("input[name='item']");
            if (this.checked) {         
                for (var i = 0; i < checkboxes.length; i++) {                       
                    // tick checkbox
                    $(checkboxes[i]).prop("checked", true);
                    if (selectedItems[selectedTabIndex].indexOf(checkboxes[i].value)==-1)
                        selectedItems[selectedTabIndex].push(checkboxes[i].value);
                }
            }
            else {
                for (var i = 0; i < checkboxes.length; i++) {
                    // untick checkbox
                    $(checkboxes[i]).prop("checked", false);
                    var index = selectedItems[selectedTabIndex].indexOf(checkboxes[i].value);
                    if (selectedItems[selectedTabIndex].indexOf(checkboxes[i].value)!=-1)
                        selectedItems[selectedTabIndex].splice(index, 1);
                }
            }
        });

        $('#tp-reports tbody').on( 'click', 'tr td.check input[name="item"]', function () {
            var index = selectedItems[selectedTabIndex].indexOf(this.value)
            if (index==-1) 
                selectedItems[selectedTabIndex].push(this.value);
            
            else 
                selectedItems[selectedTabIndex].splice(index, 1);
            
            checkSelectAll();
        });

        // determine whether to check the select all checkbox in table header
        function checkSelectAll() {
            // count number of checked boxes on the page
            var checkedCheckboxes = $("input[name='item']:checked").length;
            var totalCheckboxes = $("input[name='item']").length;
            
            $("input[name='select-all']").prop("checked", (checkedCheckboxes==totalCheckboxes && totalCheckboxes!=0) ? true : false);
        }

        var tpOrdersTable = jQuery('#tp-reports').DataTable({
            "dom": '<"clearfix"l><"clearfix"ip>t<"clearfix"ip>',
            "processing": false,
            "ajax": "{{route('admin.tp_reports.search')}}",
            "serverSide": true,
            "lengthMenu": [[100, 200, 500], [100, 200, 500]],
            "pageLength": 100,
            "order": [[10, "asc"]],
            "orderCellsTop": true,
            "scrollX": true,
            "preDrawCallback" : function ( settings ) {
               $('.overlay').removeClass('hide');
            },
            "drawCallback": function(settings){
                $('.overlay').addClass('hide');
                var checkboxes = $("input[name='item']");
                if (selectedItems[selectedTabIndex].length > 0) {
                    for (var i = 0; i < checkboxes.length; i++) {
                        if (selectedItems[selectedTabIndex].indexOf(checkboxes[i].value)!=-1) 
                            $(checkboxes[i]).prop("checked", true);
                    }
                }
                checkSelectAll();

                // update counters
                var data = settings.json;
                console.log(data);
                $.each(data.counters[0], function(key, val) {
                    $("#count_"+key).html((val != null)? val : '0' );
                });
            },
            "columnDefs": [
                {
                    "targets": [1, 12, 13, 14, 15, 16, 17, 18],
                    "visible": false
                },
                {
                    // unsortable columns
                    "targets": [0, 1, 4, 5, 6, 9, 12, 15, 16, 17],
                    "sortable": false
                },
                {
                    "targets": [0, 1, 15, 16],
                    "searchable": false
                },
            ],
            "columns": [
                { "data": "checkbox", "name": "checkbox", className:"check", "targets": 0 },
                { "data": "id", "name": "id", "targets": 1 },
                { "data": "order_item_id", "name": "order_item_id", "targets": 2 }, 
                { "data": "order_id", "name": "order_id", "targets": 3 },
                { "data": "tp_item_id", "name": "tp_item_id", "targets": 4 },
                { "data": "tp_order_code", "name": "tp_order_code", "targets": 5 },
                { "data": "channel_type", "name": "channel_type", "targets": 6 },
                { "data": "sale_price", "name": "sale_price", "targets": 7 },
                { "data": "sold_price", "name": "sold_price", "targets": 8 },
                { "data": "merchant_name", "name": "merchant_name", "targets": 9 },
                { "data": "tp_order_date", "name": "tp_order_date", "targets": 10 },
                { "data": "shipped_date", "name": "shipped_date", "targets": 11 },
                { "data": "status", "name": "status", "targets": 12 },
                { "data": "created_at", "name": "created_at", "targets": 13 },
                { "data": "updated_at", "name": "updated_at", "targets": 14 },
                { "data": "last_attended_by", "name": "last_attended_by", "targets": 15 },
                { "data": "actions", "name": "actions", "targets": 16 },
                { "data": "mode", "name": "mode", "targets": 17 },
                { "data": "remarks", "name": "remarks", "targets": 18 },
            ],
            // populate search fields during init - the length of this array must be equal to the number of columns
            "searchCols": [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                { "search": selectedTabIndex }, // set to search for items pending third party payment on load
            ],
            initComplete: function() {
                $('.overlay').addClass('hide');
            },
        });

        $('#tp-reports').on( 'processing.dt', function ( e, settings, processing ) {
            (processing) ? $('.overlay').removeClass('hide') : $('.overlay').addClass('hide');
        } ).dataTable();


        var dateRanges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };

        var startDate = moment().subtract(29, 'days');
        var endDate = moment();

        //Date range
        $('body').on('focus',".filter-date", function() {
            // prevent the daterangepicker from recreating the daterangepicker object everytime the input is selected
            if (!$(this).hasClass('has-daterangepicker')) {
                $(this).addClass('has-daterangepicker');
                $(this).daterangepicker(
                    {
                        format: 'YYYY-MM-DD',
                        ranges: dateRanges,
                        startDate: startDate,
                        endDate: endDate,
                        maxDate: endDate,
                        autoUpdateInput: false,
                        locale: {
                            cancelLabel: 'Clear',
                        },
                    },
                    function (start, end) {
                        if(start._isValid && end._isValid) {
                            $(this).val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                        }
                    }
                );
            }
        });

        var enter = jQuery.Event( 'keyup', { which: 13, keyCode: 13 } );

        // To clear daterangepicker
        $('body').on('cancel.daterangepicker', '.filter-date', function(ev, picker) {
            $(this).val('');
            tpOrdersTable.columns($(this).attr('name')+':name').search($(this).val());
            tpOrdersTable.draw();
        });

        $('body').on('change', '.filter-date', function () {
            tpOrdersTable.columns($(this).attr('name')+':name').search($(this).val());
            tpOrdersTable.draw();
        });

        $('body').on('change', '.form-control.select2', function() {
            $(this).trigger(enter);
        });



        // when input/select fields in the search form are changed
        $('body').on('keyup', ".form-control", function(e) {
            tpOrdersTable.columns($(this).attr('name')+':name').search($(this).val());

            if (e.keyCode == 13) 
                tpOrdersTable.draw();
            
        });

        // commented out because the .filter-date selector during the onchange event does not select the right attribute name
        /*$('.form-control.select2').select2().on('change', function() {
            $(this).trigger(enter);
        });*/

        /*  
            configure which columns to hide dynamically
            format: {tabIndex: {column name: to hide/not, ...} }
        */
        var settings = {
                            // pending third party payment
                            0: { 
                                "checkbox": true, 
                                "id": false, 
                                "created_at": false, 
                                "updated_at": false, 
                                "last_attended_by": false, 
                                "status": false, 
                                "actions": false
                            },
                            // pending payment ot merchant
                            1: {
                                "checkbox": true, 
                                "id": true, 
                                "created_at": true, 
                                "updated_at": true, 
                                "last_attended_by": true, 
                                "status": true, 
                                "actions": false
                            },
                            //  paid to merchant
                            2: {
                                "checkbox": true, 
                                "id": true, 
                                "created_at": true, 
                                "updated_at": true, 
                                "last_attended_by": true, 
                                "status": true, 
                                "actions": false
                            },
                            // completed
                            3: {
                                "checkbox": true, 
                                "id": true, 
                                "created_at": true, 
                                "updated_at": true, 
                                "last_attended_by": true, 
                                "status": false, 
                                "actions": true
                            }
                        };

        // call search function on tab change
        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            //window.location.hash = '#'+$(this).attr('id');
            selectedTabIndex = $(e.target).parent().index();
            changeTable(settings[selectedTabIndex]);
            loadCounters();
            

            $(".export-options").addClass("hide");
            $(".discard-options").addClass("hide");
            $(".verify-options").addClass("hide");
            $(".moveTo-options").removeClass("hide");
            $(".export-options[data-index='"+selectedTabIndex+"']").removeClass("hide");
            $(".discard-options[data-index='"+selectedTabIndex+"']").removeClass("hide");
            $(".verify-options[data-index='"+selectedTabIndex+"']").removeClass("hide");
            $(".moveTo-options[data-index='"+selectedTabIndex+"']").removeClass("hide");
        });
        
        // onclick tabs
        $("#pending-tp-payment").on('click', function() {
            //hide gmv (uploaded) counter
            $.each($(".counter"), function() {
                $(this).removeClass("hide");
            });
            $('.counter[data-countername="gmv_uploaded"]').addClass("hide");
            $('#options').removeClass("hide");
            $('#counters').addClass("hide");
            $('#discard').addClass("hide");
            $('#verify').addClass("hide");
            $('#moveTo').removeClass("hide");
            $('#complete-orders-unpaid').addClass("hide");
            $('#complete-orders-paid').addClass("hide");
            $('#remarks_div').addClass('hide');
        });

        $("#pending-payment-merchant").on('click', function() {
            // show all counters
            $.each($(".counter"), function() {
                $(this).removeClass("hide");
            });
            $('#options, #complete-orders-paid, #moveTo').addClass("hide");
            $('#options, #complete-orders-unpaid').removeClass("hide");
            $('#options, #discard, #verify').removeClass("hide");
            $('#counters').removeClass("hide");
            $('#remarks_div').removeClass('hide');
        });

        $("#paid-merchant").on('click', function() {
            // show all counters
            $.each($(".counter"), function() {
                $(this).removeClass("hide");
            });
            $('#options, #complete-orders-unpaid, #moveTo').addClass("hide");
            $('#options, #complete-orders-paid').removeClass("hide");
            $('#options, #discard, #verify').removeClass("hide");
            $('#counters').removeClass("hide");
            $('#remarks_div').removeClass('hide');
        });

        $("#completed").on('click', function() {
            // hide all counters
            $.each($(".counter"), function() {
                $(this).addClass("hide");
            });
            $('#discard').addClass("hide");
            $('#verify').addClass("hide");
            $('#moveTo').addClass("hide");
            $('#complete-orders-unpaid').addClass("hide");
            $('#complete-orders-paid').addClass("hide");
            $('#counters').addClass("hide");
            $('#remarks_div').addClass('hide');
        });

        // change table config/settings and reload table according to the tab clicked
        function changeTable(settings) {
            $.each(settings, function(key, val) {
                tpOrdersTable.column(key+":name").visible(val, false);
            });
            tpOrdersTable.columns().search('');                             // clear datatables search fields
            $('.search-row .form-control').val('');                         // reset search columns
            $.each($('.form-control.select2'), function() {
                $(this).select2().val(null);                                // reset select2
            });    
            tpOrdersTable.columns( 'mode:name' ).search(selectedTabIndex).draw();    // tell backend which sql table retrieve data from
        }

        $('#complete-orders-unpaid').on('click', function() {
            if(confirm('Are you sure you want to complete verified?')){
                $.ajax({ 
                    url: '{{route("admin.tp_reports.complete_verified_order_items")}}',
                    data: {status: "0"},
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function() {
                        $('.overlay').removeClass('hide');
                    },
                    success: function(response) {
                        if (response.numRecordsAffected > 0) {
                            displayAlert("Updated " + response.numRecordsAffected + " order items to completed.", 'success');
                            tpOrdersTable.ajax.reload();
                            loadCounters();
                        }
                        else 
                            displayAlert("No records were affected.", "info");

                        if (response.numVerifiedItems!==undefined)
                            $('.num-verified-items').html(response.numVerifiedItems);
                    },
                    error: function(response) {
                        console.log("An error has occurred.");
                    },
                    complete: function() {
                        $('.overlay').addClass('hide');
                    }
                });
            }
        });

        $('#complete-orders-paid').on('click', function() {
            if(confirm('Are you sure you want to complete verified?')){
                $.ajax({ 
                    url: '{{route("admin.tp_reports.complete_verified_order_items")}}',
                    data: {status: "1"},
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function() {
                        $('.overlay').removeClass('hide');
                    },
                    success: function(response) {
                        if (response.numRecordsAffected > 0) {
                            displayAlert("Updated " + response.numRecordsAffected + " order items to completed.", 'success');
                            tpOrdersTable.ajax.reload();
                            loadCounters();
                        }
                        else 
                            displayAlert("No records were affected.", "info");

                        if (response.numVerifiedItems!==undefined)
                            $('.num-verified-items').html(response.numVerifiedItems);
                    },
                    error: function(response) {
                        console.log("An error has occurred.");
                    },
                    complete: function() {
                        $('.overlay').addClass('hide');
                    }
                });
            }
        });

        // set tab counters
        function loadCounters() {
            $.ajax({ 
                url: '{{route("admin.tp_reports.counters")}}',
                data: {"selectedTabIndex": selectedTabIndex},
                dataType: 'json',
                type: 'GET',
                success: function(response){
                    $("#count-pending-tp").html(response.pending_tp_payment !== undefined ? response.pending_tp_payment : 0);
                    $("#count-pending-merchant").html(response.pending_payment_to_merchant !== undefined ? response.pending_payment_to_merchant : 0);
                    $("#count-paid-merchant").html(response.paid_to_merchant !== undefined ? response.paid_to_merchant : 0);
                    $(".num-verified-items-unpaid").html(response.num_verified_items_unpaid !== undefined ? response.num_verified_items_unpaid : 0);
                    $(".num-verified-items-paid").html(response.num_verified_items_paid !== undefined ? response.num_verified_items_paid : 0);

                    $("#not_found").html(response.not_found !== undefined ? response.not_found : 0);
                    $("#unverified").html(response.unverified !== undefined ? response.unverified : 0);
                    $("#verified").html(response.verified !== undefined ? response.verified : 0);
                    // $("#completed").html(response.completed !== undefined ? response.completed : 0);
                },
                error: function(response) {
                    console.log("An error has occurred.");
                }
            });
        } 
        loadCounters();     // set tab counters on page load

        $(".export-option").on('click', function() {
            $("input[name='tab']").val(selectedTabIndex);
            $("input[name='option']").val($(this).data("option"));
            $("input[name='ids']").val(JSON.stringify(selectedItems[selectedTabIndex]));
            
            $("#export-form").submit();
        });

        $(".discard-option").on('click', function() {
            $("input[name='tab']").val(selectedTabIndex);
            $("input[name='option']").val($(this).data("option"));
            $("input[name='ids']").val(JSON.stringify(selectedItems[selectedTabIndex]));
            
            $("#discard-form").submit();
        });


        $(".verify-option").on('click', function() {
            $("input[name='tab']").val(selectedTabIndex);
            $("input[name='option']").val($(this).data("option"));
            $("input[name='ids']").val(JSON.stringify(selectedItems[selectedTabIndex]));
            
            $("#verify-form").submit();
        });

        $(".moveTo-option").on('click', function() {
            $("input[name='tab']").val(selectedTabIndex);
            $("input[name='option']").val($(this).data("option"));
            $("input[name='ids']").val(JSON.stringify(selectedItems[selectedTabIndex]));
            
            $("#moveTo-form").submit();
        });

        // type - warning, danger, success, info, etc
        function displayAlert(message, type) {
            $(".errors").html('<div class="alert alert-'+type+' alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
              '<span aria-hidden="true" style="font-size:inherit;">&times;</span></button>'+message+
            '</div>');
        }

        // on page load, select tab if hashtag is present in the url
        /*var hash = window.location.hash; 
        if (hash && $(hash).is("a"))
            $(hash).click();*/
        $('#month').datepicker({
            format :  'MM-yyyy',
            disableDate: true,
            startView: "months", 
            minViewMode: "months",
            autoclose: true,
        });
    
    });    
    </script>
@append
