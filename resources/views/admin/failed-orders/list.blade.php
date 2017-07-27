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
	@lang('admin/fulfillment.page_title_failed_orders')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/fulfillment.content_header_failed_orders')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/fulfillment.box_header_failed_orders')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<table id="failed-orders-table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>@lang('admin/fulfillment.failed_orders_table_tp_id')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_channel')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_error')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_pulled_date')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_tp_date')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_order_id')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_status')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_resolved_by')</th>
			                        <th>@lang('admin/fulfillment.failed_orders_table_actions')</th>
		                    	</tr>
		                    	<tr class="search-row">
		                            <td style="width: 7%" class="search text 0"></td>
		                            <td style="width: 11%" class="search dropdown 1"></td>
		                            <td style="width: 17%" class="search text 2"></td>
		                            <td style="width: 10%" class="search date 3"><input id="created-date-range" name="report-date-range" type="text" class="form-control filter-date"></td>
		                            <td style="width: 10%" class="search date 4"><input id="tp-date-range" name="report-date-range" type="text" class="form-control filter-date"></td>
		                            <td style="width: 5%" class="search text 5"></td>
		                            <td style="width: 6%" class="search dropdown 6"></td>
		                            <td style="width: 8%" class="search dropdown 7"></td>
		                            <td style="width: 8%"></td>
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
	max-width: initial !important;
}
.search-row select {
    width: 100% !important;
    max-width: initial !important;
}
.centered{
	text-align: center;
}

.centered a{
	display: block;
	margin-top: 5px;
}

</style>
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS',false)) }}"></script>
<script type="text/javascript">
jQuery(document).ready(function(){

	function parseJson( string ) {
		try {
			jQuery.parseJSON(string);
		}
		catch (e) {
			return string;
		}

		return JSON.stringify(jQuery.parseJSON(string), null, 4);
	}

	function format ( data ) {
		var error = data.error;
		var html = '<div class="expandable-details">'
						+ '<table border="0" >';

		html += '<tr>' +
					'<td><pre>' + truncateRemark(data.error) + '</pre></td>'
				'</tr>';

		html += '</table></div>';
		return html;
	}

	function truncateRemark( string ) {
		var str = parseJson(string);
		var more = '';

		if (str.length > 150) {
			more = str.substring(150);
			str = str.substring(0, 150);
		}

		var remarkHtml = str;

		if (more != '') {
	    	remarkHtml += '<span class="excess-text">...</span>' +
	    					'<span class="show-more-text">' + more + '</span>' +
	    					'<button type="button" class="btn btn-link btn-show-more no-padding">@lang("admin/channels.button_show_more")</button>' +
	    					'<button type="button" class="btn btn-link btn-show-less no-padding">@lang("admin/channels.button_show_less")</button>';
	    }

	    return remarkHtml;
	}

	waitingDialog.show('Retrieving data....', {dialogSize: 'sm'});

	//Date range
    $('#created-date-range').daterangepicker(
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
	            $('#created-date-range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
	        }
        }
	);

	$('#tp-date-range').daterangepicker(
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
	            $('#tp-date-range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
	        }
        }
	);

	// Custom search function for searching date range
    $.fn.dataTable.ext.search.push(
	    function( settings, data, dataIndex ) {
	        var createdDateRange = $('#created-date-range').val();
	        var tpDateRange = $('#tp-date-range').val();

	        var createdDateFlag = false;
	        var tpDateFlag = false;

	        //console.log('foobar date filter');
	        // console.log(dateRange);

	        // compare created date
	        if(createdDateRange == '' || data[3] == 'N/A'){
	        	// console.log('foobar true 2');
	        	createdDateFlag = true;
	        }else{
	        	createdDateRange = createdDateRange.split(" - ");
	        	if(createdDateRange[0] == 'Invalid date' || createdDateRange[1] == 'Invalid date'){
	        		createdDateFlag = true;
	        	}
		        var createdStartDate = moment(createdDateRange[0]);
		        createdStartDate = createdStartDate.hour(00).minute(00).second(00)
		        var createdEndDate = moment(createdDateRange[1]);
		        createdEndDate = createdEndDate.hour(23).minute(59).second(59);

		        var createDate = moment(data[3]) || 0; // use data for the product count column
		 
		        if ( !( moment.isMoment(createdStartDate) && moment.isMoment(createdEndDate) ) || createDate.isBetween(createdStartDate, createdEndDate) )
		        {
		        	// console.log('foobar true 1');
		            createdDateFlag = true;
		        }else{
			        // console.log('foobar false 1');
			        createdDateFlag = false;
			    }
	        }

	        // compare tp date
	        if(tpDateRange == '' || data[4] == 'N/A'){
	        	// console.log('foobar true 2');
	        	tpDateFlag = true;
	        }else{
	        	tpDateRange = tpDateRange.split(" - ");
	        	if(tpDateRange[0] == 'Invalid date' || tpDateRange[1] == 'Invalid date'){
	        		tpDateFlag = true;
	        	}
		        var tpStartDate = moment(tpDateRange[0]);
		        tpStartDate = tpStartDate.hour(00).minute(00).second(00)
		        var tpEndDate = moment(tpDateRange[1]);
		        tpEndDate = tpEndDate.hour(23).minute(59).second(59);

		        var tpDate = moment(data[4]) || 0; // use data for the product count column
		 
		        if ( !( moment.isMoment(tpStartDate) && moment.isMoment(tpEndDate) ) || tpDate.isBetween(tpStartDate, tpEndDate) )
		        {
		        	// console.log('foobar true 1');
		            tpDateFlag = true;
		        }else{
			        // console.log('foobar false 1');
			        tpDateFlag = false;
		        }
	        }
	        // console.log('tp flag [' + tpDateFlag + ']');
	        // console.log('created flag [' + createdDateFlag + ']');
	        return (tpDateFlag && createdDateFlag);
	    }
	);

	$('#failed-orders-table thead td.search.text').each( function () {
        $(this).append( '<input type="text" class="form-control"/>' );
    } );

    $('#failed-orders-table thead td.search.dropdown').each( function () {
        $(this).append( $("."+$(this).data('field')).html() );
    } );

    var channel_id = '{{ $channel_id }}';
	var table = jQuery('#failed-orders-table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '/admin/fulfillment/failed_orders/getTableData' + (channel_id.length > 0 ? '?channel_id='+channel_id : ''),
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[3, "desc"]],
        "scrollX": true,
        "scrollY": false,
        "autoWidth": false,
		"orderCellsTop": true,
        "initComplete": function(settings, json) {
            waitingDialog.hide();
            this.api().columns().every(function(){
                var column = this;
                if(column.index() == 1 || column.index() == 6 || column.index() == 7){
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
			// discard btn event
		    $('.discard-btn').on('click', function(e){
		    	e.preventDefault();
		    	var discardAction = confirm('Are you sure you want to discard this record?');
		    	if(discardAction){
		    		var url = $(this).data('link');
			    	window.location.href = url;
		    	}
		    });

		    // row dropdown
		    $('#failed-orders-table tr td').not('.actions').on('click', function () {
				var tr = $(this).closest('tr');
				var row = table.row(tr);

				if ( row.child.isShown() ) {
					$('div.expandable-details', row.child()).slideUp( function () {
					    row.child.hide();
					    tr.removeClass('shown');
					});
				}
				else {
					$('div.expandable-details').each(function () {
						var parentRow = $(this).closest('tr').prev();
						var tableRow = table.row(parentRow);

						$(this, tableRow.child()).slideUp( function () {
						    tableRow.child.hide();
						    parentRow.removeClass('shown');
						});
					});

					row.child( format(row.data()), 'sync-retry-history' ).show();
    				tr.addClass('shown');

    				$('div.expandable-details', row.child()).slideDown();

    				$('.btn-show-more, .btn-show-less').on('click', function () {
    					var pre = $(this).closest('pre');

    					pre.find('.btn-show-more').toggle();
    					pre.find('.btn-show-less').toggle();
    					pre.find('.excess-text').toggle();
    					pre.find('.show-more-text').toggle();
    				});
				}
			});
		},
		//"initComplete": function(settings, json) {
		//	checkStatusToDisplayColumns();
		//},
		"columnDefs": [
            { "data": "tp_order_id", "name": "tp_order_id", "targets": 0 },
            { "data": "channel", "name": "channel", "targets": 1 },
            { "data": "error", "name": "error", "targets": 2},
            { "data": "created_at", "name": "created_at", "targets": 3},
            { "data": "tp_order_date", "name": "tp_order_date", "targets": 4 }, 
            { "data": "order_id", "name": "order_id", "targets": 5 },
            { "data": "status", "name": "status", "targets": 6 },
            { "data": "resolved_by", "name": "resolved_by", "targets": 7 }, 
            { "data": "actions", "name": "actions", "targets": 8 },
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
    $('#tp-date-range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        table.draw();
    });

    // To clear daterangepicker
    $('#created-date-range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        table.draw();
    });

});
</script>
@append