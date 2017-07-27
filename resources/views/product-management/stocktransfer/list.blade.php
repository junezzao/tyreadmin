@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_mgmt_transfer_list')
@stop

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('product-management.content_header_product_mgmt')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('product-management.box_header_transfer')
                        </h3>
                        @if($admin->can('create.stocktransfer'))
                        <a href="{{route('products.stock_transfer.create')}}" class="btn btn-default pull-right">
                            @lang('product-management.button_add_new_transfer')
                        </a>
                        @endif
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table id="transfer-table" width="100%" style="width:100%;" class="table table-bordered table-striped">
                            <thead>
                                <tr style="white-space: nowrap">
                                    <th>
                                        @lang('product-management.transfer_id')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_original_channel')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_target_channel')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_created_at')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_updated_at')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_received_at')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_pic')
                                    </th>                                   
                                    <th>
                                        @lang('product-management.transfer_merchant')
                                    </th>
                                    <th>
                                        @lang('product-management.transfer_status')
                                    </th>
                                    <th></th>
                                </tr>
                                <tr class="search-row">
                                    <td></td>
                                    <td class="search text 1"></td>
                                    <td class="search text 2"></td>
                                    <td class="search text 3"></td>
                                    <td class="search text 4"></td>
                                    <td class="search text 5"></td>
                                    <td class="search text 6"></td>
                                    <td class="search text 7"></td>
                                    <td class="search dropdown 8" data-field="status"></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="hide status">
        {!!Form::select('status', $statuses, null, array('class' => 'form-control status-dropdown', 'placeholder' => 'Status')) !!}
        </div>
    </section>
@stop

@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script type=""></script>
<script type="text/javascript">
jQuery(document).ready(function(){
    
    $('#transfer-table thead td.search.text').each( function () {
        $(this).append( '<input type="text" class="form-control"/>' );
    } );

    $('#transfer-table thead td.search.dropdown').each( function () {
        $(this).append( $('.'+$(this).data('field')).html() );  
    } );/*.promise().done(function() {
        $(".select2-nosearch-promise").select2({
            minimumResultsForSearch: Infinity
        });
    });*/

    var channel_id = '{{ $channel_id }}';
    var table = jQuery('#transfer-table').DataTable({
        "sDom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
        "ajax": "{{ route('products.stock_transfer.tableData') }}" + (channel_id.length > 0 ? '?channel_id='+channel_id : ''),
        "lengthMenu": [[30, 50, 100], [30, 50, 100]],
        "pageLength": 30,
        "order": [[4, "desc"]],    
        "orderCellsTop": true,
        "scrollX": true,
        "autoWidth": true,
        "fnDrawCallback": function (o) {
            jQuery(window).scrollTop(0);
        },
        "columnDefs": [
            {
                "targets": [ 9 ],
                "sortable": false,
            }
        ],
        "columns": [
            { "data": "id", "name": "id", "targets": 0 },
            { "data": "original_channel", "name": "original_channel", "targets": 1 },
            { "data": "target_channel", "name": "target_channel", "targets": 2 },
            { "data": "created_at", "name": "created_at", "targets": 3 }, 
            { "data": "updated_at", "name": "updated_at", "targets": 4},
            { "data": "received_at", "name": "received_at", "targets": 5},
            { "data": "pic", "name": "pic", "targets": 6},
            { "data": "merchant", "name": "merchant", "targets": 7},
            { "data": "status", "name": "status", "targets": 8},
            { "data": "action", "name": "action", "targets": 9},
            
        ],
    });
    jQuery('#transfer-table').offset().top;

    // Apply the search
    table.columns().every( function () {
        var that = this;
        $( 'input', 'thead tr.search-row td.search.'+that.index() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );

        $( 'select', 'thead tr.search-row td.search.'+that.index() ).on( 'change', function () { 

            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );

    } );
    
});
</script>
@append