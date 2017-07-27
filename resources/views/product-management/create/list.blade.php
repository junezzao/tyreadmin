@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
@append

@section('title')
    @lang('product-management.page_title_product_mgmt_create_list')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('product-management.content_header_product_mgmt')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content create-restock create-restock-list-page">
        <div class="row">
            <div class="col-xs-12">
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! session('success') !!}
                    </div>
                    @elseif(Session::has('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! session('error') !!}
                    </div>
                @endif
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('product-management.box_header_create')
                        </h3>
                        @if(\Auth::user()->can('create.restock'))
                        <a href="{{route('products.create.create')}}">
                            <buttton type="button" id="new_create" class="btn btn-default pull-right">
                                @lang('product-management.button_add_new_create')
                            </button>
                        </a>
                        @endif
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li id="pending" data-status='0' class="active"><a href="#">@lang('product-management.create_form_label_status_pending')</a></li>
                                <li id="received" data-status='1'><a href="#">@lang('product-management.create_form_label_status_received')</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active">
                                    <table id="create_table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:5%">
                                                    @lang('product-management.create_table_id')
                                                </th>
                                                <th style="width:10%">
                                                    @lang('product-management.create_batch_date')
                                                </th>
                                                <th style="width:15%">
                                                    @lang('product-management.create_merchandiser')
                                                </th>
                                                <th style="width:15%">
                                                    @lang('product-management.create_merchant')
                                                </th>
                                                <th style="width:10%">
                                                    @lang('product-management.create_no_of_items')
                                                </th>
                                                <th style="width:15%">
                                                    @lang('product-management.create_total_value')
                                                </th>
                                                <th style="width:15%">
                                                    @lang('product-management.create_status')
                                                </th>
                                                <th style="width:20%">
                                                    @lang('product-management.create_action')
                                                </th>
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
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>

<script type="text/javascript">
jQuery(document).ready(function(){
    var channel_id = '{{ $channel_id }}';
    waitingDialog.show('Loading...', {dialogSize: 'sm'});
    var table = jQuery('#create_table').DataTable({
        "sDom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
        "ajax": '{{ route("products.create.tableData") }}' + (channel_id.length > 0 ? '?channel_id='+channel_id : ''),
        "lengthMenu": [[30, 50, 100], [30, 50, 100]],
        "pageLength": 30,
        "order": [[1, "desc"]],
        "scrollX": true,
        "scrollY": false,
        "autoWidth": false,
        "orderCellsTop": true,
        "fnDrawCallback": function (o) {
            jQuery(window).scrollTop(0);
        },
        "initComplete": function(settings, json) {
            table.column(6).search('Pending').draw();
            waitingDialog.hide();
        },
        "columns": [
            { "data": "id", "name": "id", "targets": 0 },
            { "data": "batch_date", "name": "batch_date", "targets": 1 },
            { "data": "merchandiser", "name": "merchandiser", "targets": 2 },
            { "data": "merchant", "name": "merchant", "targets": 3 }, 
            { "data": "no_of_items", "name": "no_of_items", "targets": 4},
            { "data": "total_value", "name": "total_value", "targets": 5},
            { "data": "status", "name": "status", "targets": 6},
            { "data": "action", "name": "action", "targets": 7}
        ],
    });
    jQuery('#create_table').offset().top;

    $("ul.nav li").on('click', function(e) {
        var navTab = $(this);
        if (navTab.attr('id') == 'pending') {
            $("#received").removeClass('active');
            var searchData = 'Pending';
            navTab.addClass('active');
        }
        else if (navTab.attr('id') == 'received') {
            $("#pending").removeClass('active');
            var searchData = 'Received';
            navTab.addClass('active');
        }
        table.column(6).search(searchData).draw();
    });
});
</script>
@append