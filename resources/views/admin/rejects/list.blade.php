@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<link href="{{ asset('plugins/datepicker/datepicker3.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js', env('HTTPS', false)) }}" type="text/javascript"></script>
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css', env('HTTPS', false)) }}">
@append

@section('title')
	@lang('admin/reject.page_title_reject')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/reject.content_header_reject')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/reject.box_header_reject')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<table id="reject-history-table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th style="width: 5%;">@lang('admin/reject.reject_list_table_id')</th>
			                        <th style="width: 13%;">@lang('admin/reject.reject_list_table_item_name')</th>
			                        <th style="width: 14%;">@lang('admin/reject.reject_list_table_hubwire_sku')</th>
			                        <th style="width: 12%;">@lang('admin/reject.reject_list_table_reason')</th>
			                        <th style="width: 5%;">@lang('admin/reject.reject_list_table_quantity')</th>
			                        <th style="width: 11%;">@lang('admin/reject.reject_list_table_channel_name')</th>
			                        <th style="width: 11%;">@lang('admin/reject.reject_list_table_merchant_name')</th>
			                        <th style="width: 9%;">@lang('admin/reject.reject_list_table_user')</th>
			                        <th style="width: 12%;">@lang('admin/reject.reject_list_table_rejected_at')</th>
		                    	</tr>
		                    	<tr class="search-row">
		                            <td style="width: 5%;"></td>
		                            <td style="width: 13%;" class="search text 1"></td>
		                            <td style="width: 14%;" class="search text 2"></td>
		                            <td style="width: 12%;" class="search text 3"></td>
		                            <td style="width: 5%;" class="search text 4"></td>
		                            <td style="width: 11%;" class="search dropdown 5"></td>
		                            <td style="width: 11%;" class="search dropdown 6"></td>
		                            <td style="width: 9%;" class="search text 7"></td>
		                            <td style="width: 12%;" class="search date 8"><input id="date-range" name="report-date-range" type="text" class="form-control filter-date"></td>
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
<style>

.search-row input {
	width: 100% !important;
	
}
.search-row select {
    width: 100% !important;
}

</style>
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS', false) )}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS', false) )}}"></script>
<script type="text/javascript">
jQuery(document).ready(function(){

	//Date range
    $('.filter-date').daterangepicker(
        {
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
	            $('.filter-date').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
	        }
        }
	);

	waitingDialog.show('Retrieving data....', {dialogSize: 'sm'});

	// Custom search function for searching date range
    $.fn.dataTable.ext.search.push(
	    function( settings, data, dataIndex ) {
	        var dateRange = $('#date-range').val();
	        //console.log('foobar date filter');
	        // console.log(dateRange);
	        if(dateRange != ''){
	        	dateRange = dateRange.split(" - ");
	        	if(dateRange[0] == 'Invalid date' || dateRange[1] == 'Invalid date'){
	        		return true;
	        	}
		        var startDate = moment(dateRange[0]);
		        startDate = startDate.hour(00).minute(00).second(00)
		        var endDate = moment(dateRange[1]);
		        endDate = endDate.hour(23).minute(59).second(59);

		        var rejectDate = moment(data[8]) || 0; // use data for the product count column
		 
		        if ( !( moment.isMoment(startDate) && moment.isMoment(endDate) ) || rejectDate.isBetween(startDate, endDate) )
		        {
		        	// console.log('foobar true 1');
		            return true;
		        }
		        // console.log('foobar false 1');
		        return false;
	        }else{
	        	// console.log('foobar true 2');
	        	return true;
	        }
	    }
	);

	$('#reject-history-table thead td.search.text').each( function () {
        $(this).append( '<input type="text" class="form-control"/>' );
    } );

    $('#reject-history-table thead td.search.dropdown').each( function () {
        $(this).append( $("."+$(this).data('field')).html() );
    } );

    var channel_id = '{{ $channel_id }}';
	var table = jQuery('#reject-history-table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '/admin/reject/getTableData' + (channel_id.length > 0 ? '?channel_id='+channel_id : ''),
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[8, "desc"]],
        "scrollX": true,
        "scrollY": false,
        "autoWidth": false,
		"orderCellsTop": true,
        "initComplete": function(settings, json) {
            waitingDialog.hide();
            this.api().columns().every(function(){
                var column = this;
                if(column.index() == 5 || column.index() == 6){
                    var select = $('<select class="form-control"><option value="">All</option></select>')
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
		"drawCallback": function (settings) {
			jQuery(window).scrollTop(0);
		},
		//"initComplete": function(settings, json) {
		//	checkStatusToDisplayColumns();
		//},
		"columnDefs": [
            { "data": "id", "name": "id", "width": "5%", "targets": 0 },
            { "data": "item_name", "name": "item_name", "width": "13%", "targets": 1 },
            { "data": "hubwire_sku", "name": "hubwire_sku", "width": "14%", "targets": 2},
            { "data": "reason", "name": "reason", "width": "12%", "targets": 3},
            { "data": "quantity", "name": "quantity", "width": "5%", "targets": 4 }, 
            { "data": "channel_name", "name": "channel_name", "width": "11%", "targets": 5 }, 
            { "data": "merchant_name", "name": "merchant_name", "width": "11%", "targets": 6 }, 
            { "data": "user_id", "name": "user_id", "width": "9%", "targets": 7 },
            { "data": "rejected_at", "name": "rejected_at", "width": "12%", "targets": 8}
        ]
    });

    // Apply the search
    table.columns().every( function () {
    	var that = this;
        $( 'input', 'thead tr.search-row td.text.search.'+that.index() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );

        $( 'select', 'thead tr.search-row td.dropdown.search.'+that.index() ).on( 'change', function () {       
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );

        $( 'input', 'thead tr.search-row td.date.search.'+that.index() ).on( 'keyup change', function () {
            table.draw();
        } );

    } );
    
	// To clear daterangepicker
    $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        table.draw();
    });
});
</script>
@append