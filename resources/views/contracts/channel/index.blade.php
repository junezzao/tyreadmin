@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS',false)) }}"></script>
<link href="{{ asset('plugins/datepicker/datepicker3.css', env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js', env('HTTPS',false)) }}" type="text/javascript"></script>
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css', env('HTTPS',false)) }}">
@append

@section('title')
	@lang('contracts.page_title_channel_contracts_index')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('contracts.content_header_channel_contracts')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('contracts.box_header_channel_contracts_index')</h3>
	              		@if($user->can('create.channelcontract'))
                        <a href="{!! route('contracts.channels.create', []) !!}" id="new_contract" class="btn btn-default pull-right">
                            @lang('contracts.btn_create_new_channel')
                        </a>
                        @endif
	            	</div><!-- /.box-header -->
	            	<div class="box-body contracts-module contract-index-page">
	            		<table id="contracts-table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>@lang('contracts.table_header_id')</th>
                                    <th>@lang('contracts.table_header_channel')</th>
			                        <th>@lang('contracts.table_header_merchant')</th>
			                        <th>@lang('contracts.table_header_brand')</th>
			                        <th>@lang('contracts.table_header_contract_name')</th>
			                        <th>@lang('contracts.table_header_created')</th>
			                        <th>@lang('contracts.table_header_updated')</th>
			                        <th>@lang('contracts.table_header_start_date')</th>
                                    <th>@lang('contracts.table_header_end_date')</th>
			                        <th>@lang('contracts.table_header_actions')</th>
		                    	</tr>
		                    	<tr class="search-row">
		                            <td style="width: 1%" class="search text 0"></td>
                                    <td style="width: 12%" class="search text 1"></td>
		                            <td style="width: 12%" class="search text 2"></td>
		                            <td style="width: 11%" class="search text 3"></td>
		                            <td style="width: 10%" class="search text 4"></td>
		                            <td style="width: 10%" class="search text 5"></td>
		                            <td style="width: 5%" class="search text 6"></td>
		                            <td style="width: 5%" class="search text 7"></td>
                                    <td style="width: 5%" class="search text 8"></td>
		                            <td style="width: 9%"></td>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    </tbody>
	                    </table>
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS',false)) }}"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	$('#contracts-table thead td.search.text').each( function () {
        $(this).append( '<input type="text" class="form-control"/>' );
    } );

    // no dropdown in table filters
    // $('#contracts-table thead td.search.dropdown').each( function () {
    //     $(this).append( $("."+$(this).data('field')).html() );
    // } );

    $('#contracts-table').on('click', '.btn-delete-contract',function(){
    	if(confirm('Are you sure you want to delete this contract?')){
    		$(this).closest('form').submit();
    	}
    });

    var channel_id = '{{ $channel_id }}';
	var table = jQuery('#contracts-table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '/contracts/channels/table_data' + (channel_id.length > 0 ? '?channel_id='+channel_id : ''),
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[6, "desc"]],
        "scrollX": true,
        "scrollY": false,
        "autoWidth": false,
		"orderCellsTop": true,
        "initComplete": function(settings, json) {
            waitingDialog.hide();
            // no dropdown in table filters
            // this.api().columns().every(function(){
            //     var column = this;
            //     if(column.index() == 6){
            //         var select = $('<select class="form-control"><option value="">All</option></select>')
            //             .appendTo($('.dataTable thead tr td:nth-child(' + (column.index() + 1) + ')').first().empty())
            //             .on('change', function(){
            //                 var val = $.fn.dataTable.util.escapeRegex(
            //                     $(this).val()
            //                 );
     
            //                 column.search(val ? '^'+val+'$' : '', true, false).draw();
            //             });
     
            //         column.data().unique().sort().each(function(d, j){
            //             select.append('<option value="'+d+'">'+d+'</option>')
            //         });
            //     }
            // });
        },
		"drawCallback": function (settings) {
			jQuery(window).scrollTop(0);
		},
		"columnDefs": [
            { "data": "contract_id", "name": "contract_id", "targets": 0 },
            { "data": "channel", "name": "channel", "targets": 1 },
            { "data": "merchant", "name": "merchant", "targets": 2 },
            { "data": "brand", "name": "brand", "targets": 3},
            { "data": "name", "name": "name", "targets": 4},
            { "data": "created_at", "name": "created_at", "targets": 5 }, 
            { "data": "updated_at", "name": "updated_at", "targets": 6 }, 
            { "data": "start_date", "name": "start_date", "targets": 7 },
            { "data": "end_date", "name": "end_date", "targets": 8 },
            { "data": "actions", "name": "actions", "targets": 9 },
        ]
    });

    table.columns().every( function () {
    	var that = this;
        $( 'input', 'thead tr.search-row td.text.search.'+that.index() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );

        // no dropdown in table filters
        // $( 'select', 'thead tr.search-row td.dropdown.search.'+that.index() ).on( 'change', function () {       
        //     if ( that.search() !== this.value ) {
        //         that
        //             .search( this.value )
        //             .draw();
        //     }
        // } );

    } );
});
</script>
@append