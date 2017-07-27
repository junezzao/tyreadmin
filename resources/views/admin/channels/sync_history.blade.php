@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
<link href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
@append

@section('title')
	@if($archived)
		@lang('admin/channels.page_title_sync_archive')
	@else
		@lang('admin/channels.page_title_sync_history')
	@endif
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/channels.content_header_channels')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">
	              			@if($archived)
	              				@lang('admin/channels.box_header_sync_archive')
	              			@else
	              				@lang('admin/channels.box_header_sync_history')
	              			@endif
	              		</h3>
	              		<div id="options" class="pull-right">
		              		@if(!$archived)
		              			<a href="{{route('admin.channels.sync_history.archive')}}" class="btn btn-default">@lang('admin/channels.button_sync_archive')</a>
								@if($admin->can('edit.channel'))
									<div class="dropdown">
										<button type="button" id="btn_unselect_all" class="btn btn-default">@lang("product-management.button_inventory_filter_unselect_all")</button>
										<button type="button" id="btn_selected" class="btn btn-default">@lang("product-management.button_inventory_filter_selected") <span class="badge info selectedNum">0</span></button>
										<button type="button" id="btn_misc_options" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
										<ul class="dropdown-menu dropdown-menu-right">
											<li><a href="#" class="bulk-action" data-url="{{ route('admin.channels.sync_history.bulkUpdate') }}" data-action="retry">Bulk Retry</a></li>
											<li><a href="#" class="bulk-action" data-url="{{ route('admin.channels.sync_history.bulkUpdate') }}" data-action="cancel">Bulk Cancel</a></li>
										</ul>
									</div>
								@endif
		              		@else
		              			<a href="{{route('admin.channels.sync_history.index')}}" class="btn btn-default">@lang('admin/channels.button_sync_archive_back')</a>
		            		@endif
						</div>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<div id="div_items_list" class="sync-list-div">
		            		<table id="sync_history_table" class="table table-bordered table-striped">
			                    <thead>
									<tr>
										@if(!$archived)
											<th style="width:7%" class="check">{!!Form::checkbox('select-all', '')!!}</th>
										@endif
										<th style="width:10%">@lang('admin/channels.sync_history_table_sync_id')</th>
										<th style="width:10%">@lang('admin/channels.sync_history_table_product_id')</th>
										<th style="width:13%">@lang('admin/channels.sync_history_table_event')</th>
										<th style="width:13%">@lang('admin/channels.sync_history_table_trigger_event')</th>
										<th style="width:10%">@lang('admin/channels.sync_history_table_status')</th>
										<th style="width:13%">@lang('admin/channels.sync_history_table_sent_time')</th>
										<th style="width:13%">@lang('admin/channels.sync_history_table_created_at')</th>
										@if(!$archived)
											<th style="width:10%">@lang('admin/channels.sync_history_table_actions')</th>
										@endif
									</tr>
			                      	<tr class="search-row">
			                      		@if(!$archived)
			                      			<td style="width:7%"></td>
			                      		@endif
			                            <td style="width:10%" class="search-data text">{!! Form::number( 'search_data[id]', null, ['class' => 'form-control'] ) !!}</td>
			                            <td style="width:10%" class="search-data text">{!! Form::number( 'search_data[product_id]', null, ['class' => 'form-control'] ) !!}</td>
			                            <td style="width:13%" class="search-data text">{!! Form::text( 'search_data[event]', null, ['class' => 'form-control'] ) !!}</td>
			                            <td style="width:13%" class="search-data text">{!! Form::text( 'search_data[trigger_event]', null, ['class' => 'form-control'] ) !!}</td>
			                            <td style="width:10%" class="search-data text">{!! Form::select('search_data[status]', $statuses, null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('product-management.inventory_filter_placeholder_status'))) !!}</td>
			                            <td style="width:13%" class="search-data date">{!! Form::text( 'search_data[sent_time]', null, ['class' => 'form-control filter-date', 'placeholder' => trans('admin/channels.sync_history_table_placeholder_date_range'), 'readonly' => true] ) !!}</td>
			                            <td style="width:13%" class="search-data date">{!! Form::text( 'search_data[created_at]', null, ['class' => 'form-control filter-date', 'placeholder' => trans('admin/channels.sync_history_table_placeholder_date_range'), 'readonly' => true] ) !!}</td>
			                            @if(!$archived)
			                            	<td style="width:10%"></td>
			                            @endif
		                        	</tr>
			                    </thead>
			                    <tbody>
			                    </tbody>
		                    </table>
		                </div>
		                @if(!$archived)
		                    <div id="div_selected_list" class="sync-list-div" style="width: 100%;">
			                    <table id="selected_list" class="table table-bordered table-striped">
				                    <thead>
										<tr>
											<th style="width:7%"></th>
											<th style="width:10%">@lang('admin/channels.sync_history_table_sync_id')</th>
											<th style="width:10%">@lang('admin/channels.sync_history_table_product_id')</th>
											<th style="width:13%">@lang('admin/channels.sync_history_table_event')</th>
											<th style="width:13%">@lang('admin/channels.sync_history_table_trigger_event')</th>
											<th style="width:10%">@lang('admin/channels.sync_history_table_status')</th>
											<th style="width:13%">@lang('admin/channels.sync_history_table_sent_time')</th>
											<th style="width:13%">@lang('admin/channels.sync_history_table_created_at')</th>
											<th style="width:10%">@lang('admin/channels.sync_history_table_actions')</th>
										</tr>
				                    </thead>
				                    <tbody>
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/prettify.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/run_prettify.js', env('HTTPS', false)) }}"></script>

<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS', false) )}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS', false) )}}"></script>

<script type="text/javascript">
function process ( button ) {
	waitingDialog.show('Updating sync status....', {dialogSize: 'sm'});
	var url = $(button).attr('data-url');

	$.ajax({
		url: url,
		method: 'POST',
		success: function( result ) {
        	if (result.success) {
				var statusCell = $(button).closest('tr').find('td.sync_status').first();
				statusCell.text(result.new_status);

				var buttonCell = $(button).closest('td');
				buttonCell.html(result.button);
			}
        	else {
        		alert(result.message);
        	}
        	waitingDialog.hide();
    	},
    	error: function( jqXHR, textStatus, errorThrown ) {
    		alert(errorThrown);
    		waitingDialog.hide();
    	}
	});
}

jQuery(document).ready(function(){

	var selectedSyncs = [];
	var unchecked = new Array;
	var selected_list_data = new Array;

	showSelectedNumber();

	function showSelectedNumber(){
		$('.selectedNum').html(selectedSyncs.length);
	}

	// determine whether to check the select all checkbox
    function checkSelectAll() {
    	// count number of checked boxes on the page
    	var checkedCheckboxes = $("input[name='sync']:checked").length;
    	if (checkedCheckboxes==table.page.len()) {
    		$("input[name='select-all']").prop("checked", true);
    	}
    	else {
    		$("input[name='select-all']").prop("checked", false);
    	}
    }

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
		// var showRemarkStatuses = ['FAILED', 'ERROR', 'CANCELLED'];
		var logs = data.logs;
		var html = '<div class="expandable-details">'
						+ '<table border="0" >';

		if (data.remarks != "") {
			html += '<tr class="sync-remarks">' +
						'<td colspan="3"><pre class="prettyprint">' + truncateRemark(data.remarks) + '</pre></td>'
					'</tr>';
		}
		else if (data.remarks == "") { // && showRemarkStatuses.indexOf(data.status) >= 0
			html += '<tr class="sync-remarks">' +
		                '<td colspan="3">No remarks.</td>' +
		            '</tr>';
		}

		if (logs.length > 0) {
			$.each(logs, function ( i, el ) {
				html += '<tr>' +
			                '<td class="timestamp-cell">' + el.sent_time + '</td>' +
			                '<td>' + el.status + '</td>' +
			                '<td><pre class="prettyprint">' + truncateRemark(el.remarks) + '</pre></td>' +
			            '</tr>';
			});
		}
		else {
			html += '<tr><td colspan="3">No retry history.</td></tr>';
		}

		html += '</table></div>';
		return html;
	}

	function truncateRemark( string ) {
		var str = parseJson(string);
		var more = '';

		// similar to php's htmlspecialchars()
		var map = {
		    '&': '&amp;',
		    '<': '&lt;',
		    '>': '&gt;',
		    '"': '&quot;',
		    "'": '&#039;'
		};

		str = str.replace(/[&<>"']/g, function(m) {
			return map[m];
		});

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

	var enter = jQuery.Event( 'keyup', { which: 13, keyCode: 13 } );

	var default_channel = '{{ $default_channel }}';
	var cols = [
			@if(!$archived)
			{ "data": "checkbox", "name":"checkbox", className:"check", "targets":0 },
			@endif
            { "data": "id", "name": "id", "targets": @if(!$archived) 1 @else 0 @endif},
            { "data": "product_id", "name": "product_id", "targets": @if(!$archived) 2 @else 1 @endif },
            { "data": "event", "name": "action", "targets": @if(!$archived) 3 @else 2 @endif },
            { "data": "trigger_event", "name": "trigger_event", "targets": @if(!$archived) 4 @else 3 @endif },
            { "data": "status", "name": "status", "targets": @if(!$archived) 5 @else 4 @endif, "class": "sync_status" },
            { "data": "sent_time", "name": "sent_time", "targets": @if(!$archived) 6 @else 5 @endif },
            { "data": "created_at", "name": "created_at", "targets": @if(!$archived) 7 @else 6 @endif },
            // { "data": "logs", "name": "logs", "targets": 8, "class": "sync_logs", "visible": false, "searchable": false, "orderable": false },
            // { "data": "remarks", "name": "remarks", "targets": 8, "class": "sync_remarks", "visible": false, "searchable": false, "orderable": false }
        ];

    if (Boolean('{{ $archived }}') !== true) {
    	cols.push({ "data": "actions", "name": "actions", "targets": 8, "orderable": false, "class": "actions" });
    }

	var table = jQuery('#sync_history_table').DataTable({
		"dom": '<"clearfix"<"#channel_filter">><"clearfix"lp>t<"clearfix"ip>',
		"ajax": '{{route("admin.channels.sync_history.data")}}?channel=' + default_channel + '&archived={{ $archived }}',
		"lengthMenu": [[20, 40, 60, 120], [20, 40, 60, 120]],
		"pageLength": 20,
		"order": [[@if(!$archived) 7 @else 6 @endif, "desc"]],
		"autoWidth": false,
		"orderCellsTop": true,
		"serverSide": true,
		"processing": false,
		"initComplete" : function ( settings, json ) {
			$('#channel_filter').html('{!! Form::select("channel", $channels, !empty($default_channel) ? $default_channel : null, array("class" => "form-control inventory-filter select2")) !!}');
			default_channel = $("select[name=channel] option:selected").val();
			$("select[name=channel]").select2();

			if (default_channel == '') {
				$("select[name=channel]").prop('disabled', true);
				$("#sync_history_table_filter input").prop('disabled', true);
			}
			else {
				window.history.pushState('', document.title, window.location.pathname + '?channel=' + default_channel);
			}

			$('select[name=channel]').on('change', function () {
				var params = $(".search-row td.search-data input, .search-row td.search-data select").filter(function () {
					if ($(this).val() !== "") {
						return this;
					}
				}).serialize();

				if ($(this).val() !== "") {
					window.history.pushState('', document.title, window.location.pathname + '?channel=' + $(this).val());
				}

    			table.ajax.url("{{route('admin.channels.sync_history.data')}}?channel=" + $(this).val() + '&' + params + '&archived={{ $archived }}').load();
			});
		},
		"preDrawCallback" : function ( settings ) {
			waitingDialog.show('Retrieving sync history....', {dialogSize: 'sm'});
		},
		"drawCallback": function ( settings ) {
			jQuery(window).scrollTop(0);
			waitingDialog.hide();

			$('#sync_history_table tbody tr td').not('.actions').not('.check').on('click', function () {
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
    					var pre = $(this).closest('pre.prettyprint');

    					pre.find('.btn-show-more').toggle();
    					pre.find('.btn-show-less').toggle();
    					pre.find('.excess-text').toggle();
    					pre.find('.show-more-text').toggle();
    				});
				}
			});

			var checkboxes = $("input[name='sync']");

	    	if (selectedSyncs.length > 0) {
	    		for (var i = 0; i < checkboxes.length; i++) {
	    			if (selectedSyncs.indexOf(checkboxes[i].value)!=-1) {
	    				$(checkboxes[i]).prop("checked", true);
	    			}
	    		}
	    	}

	    	checkSelectAll();

	    	// attach checkbox event
	    	$('input[name="sync"]').change(function(){
	    		if(this.checked){
	    			// console.log($(this).val());
	    			// add sync to array of selected syncs
	    			if (selectedSyncs.indexOf($(this).val())==-1){
	    				selectedSyncs.push($(this).val());
	    				selected_list_data[$(this).val()] = table.row($(this).closest('tr')).data();
	    			}
	    		}else{
	    			var index = selectedSyncs.indexOf($(this).val());
	    			if (selectedSyncs.indexOf($(this).val())!=-1){
    					selectedSyncs.splice(index, 1);
    					delete selected_list_data[$(this).val()];
	    			}
	    		}
	    		showSelectedNumber();
	    		checkSelectAll();
	    	});
		},
		"columns": cols,
		"columnDefs": [
	        {
	        	// unsortable columns
	        	"targets": [0],
	        	"sortable": false,
	        	"searchable": false
	        },
        ],
    });

	var selected_list = $('#selected_list').DataTable({
	 	"dom": '<"clearfix"<"#channel_filter">><"clearfix"lp>t<"clearfix"ip>',
		"lengthMenu": [[20, 40, 60, 120], [20, 40, 60, 120]],
		"pageLength": 20,
		"order": [[@if(!$archived) 7 @else 6 @endif, "desc"]],
		"autoWidth": false,
		"orderCellsTop": true,
		"columnDefs": [
	        {
	        	// unsortable columns
	        	"targets": [0],
	        	"sortable": false,
	        	"searchable": false
	        },
        ],
        "columns": cols,
		"drawCallback": function( settings ) {
			if (selectedSyncs.length > 0) {
				$.each(selectedSyncs, function () {
					$('input[value="'+this+'"]').prop('checked', true);
				});
			}
			// attach checkbox event
	    	$('input[name="sync"]').change(function(){
	    		if(this.checked){
	    			// do nothing
	    		}else{
	    			var index = selectedSyncs.indexOf($(this).val());
	    			if (selectedSyncs.indexOf($(this).val())!=-1){
    					selectedSyncs.splice(index, 1);
    					delete selected_list_data[$(this).val()];
	    			}
	    			selectedSyncs.splice($(this).val(), 1);

					selected_list.row($(this).closest('tr')).remove().draw();
					delete selected_list_data[$(this).val()];

					unchecked.push($(this).val());
	    		}
	    		showSelectedNumber();
	    	});

			// $('#selected_list .inventory-item .img').on('click', function() {
			// 	var item = $(this).closest('.inventory-item');
			// 	item.removeClass('selected');

			// 	var product_id = item.children('.chk_select').val();
			// 	var index = checked.indexOf(product_id + "");

			// 	if (index >= 0) {
			// 		checked.splice(index, 1);

			// 		selected_list.row($(this).closest('tr')).remove().draw();
			// 		delete selected_list_data[product_id];

			// 		unchecked.push(product_id);
			// 	}

			// 	$('#count-selected').text(checked.length);
			// });
		},
	});

    $('.search-row td input').unbind();
	$('.search-row td input, .search-row td select').bind('keyup', function(e) {
		if(e.keyCode == 13) {
			var params = $(".search-row td.search-data input, .search-row td.search-data select").filter(function () {
				if ($(this).val() !== "") {
					return this;
				}
			}).serialize();

	    	table.ajax.url('{{route("admin.channels.sync_history.data")}}?channel=' + $('select[name=channel]').val() + '&' + params + '&archived={{ $archived }}').load();
		}
	});

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

    if (Boolean('{{ $archived }}') == true) {
        dateRanges = {
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
        };

        var startDate = moment().startOf('year');
        var endDate = moment().endOf('year');
    }

	//Date range
    $('.filter-date').daterangepicker(
        {
            format: 'YYYY-MM-DD',
            ranges: dateRanges,
            startDate: startDate,
            endDate: endDate,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
        },
        function (start, end) {
        	if(start._isValid && end._isValid) {
	            $(this).val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
	        }
        }
	);

	$('.filter-date').on('change', function () {
		$(this).trigger(enter);
	});

	// To clear daterangepicker
    $('.filter-date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
		$(this).trigger(enter);
    });

    $("select[name='search_data[status]']").on('change', function () {
		$(this).trigger(enter);
	});

	$("input[name='select-all']").on('click', function() {
    	var checkboxes = $("input[name='sync']").not(":disabled");
    	if (this.checked) {
    		for (var i = 0; i < checkboxes.length; i++) {
    			// tick checkbox
    			$(checkboxes[i]).prop("checked", true);

    			// add sync to array of selected syncs
    			if (selectedSyncs.indexOf(checkboxes[i].value)==-1)
    				selectedSyncs.push(checkboxes[i].value);
    		}
    	}
    	else {
    		for (var i = 0; i < checkboxes.length; i++) {
    			// untick checkbox
    			$(checkboxes[i]).prop("checked", false);
    			var index = selectedSyncs.indexOf(checkboxes[i].value);
    			// remove sync from array of selected syncs
    			if (selectedSyncs.indexOf(checkboxes[i].value)!=-1)
    				selectedSyncs.splice(index, 1);
    		}
    	}
    	showSelectedNumber();
    	// console.log(selectedSyncs);
    });

    $(".bulk-action").on('click', function(){
    	// change this
    	if (selectedSyncs.length > 0) {

			var url = $(this).data('url');
			var action = $(this).data('action');

			$form = $('<form action="' + url + '" method="POST"></form>');
			$form.append('<input type="hidden" name="action" value="' + action + '"></input>');
			selectedSyncs.join(',');

			$form.append('<input type="hidden" name="sync-ids" value="' + selectedSyncs + '"></input>');

			$('body').append($form);
			$form.submit();
		}
		else {
			alert("Please select at least one sync.");
		}
    });

    $("#btn_selected").on('click', function() {
		$('#div_items_list').toggle();
		$('#div_selected_list').toggle();

		if ($("#div_selected_list").is(":visible")) {
			$("#btn_select_all").hide();
			$("#btn_unselect_all").hide();
			selected_list.clear();

			if (selected_list_data.length > 0) {
				for (var key in selected_list_data) {
					selected_list.row.add(selected_list_data[key]);
				}
			}

			selected_list.draw();
		}else{
			// table.draw();
		}

		if ($("#div_items_list").is(":visible")) {

			if (unchecked.length > 0) {
				$.each(unchecked, function () {
					$('input[value="'+this+'"]').prop('checked', false);
				});

				unchecked = new Array;
			}
		}
	});
});
</script>
@append