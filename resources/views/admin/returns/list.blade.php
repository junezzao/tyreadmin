@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<style>
	.popover{
		
	    width:300px;
	    /*
	    height:250px;    
		*/
	}
</style>

@append

@section('title')
	@lang('admin/fulfillment.page_title_fulfillment')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/fulfillment.content_header_fulfillment')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/fulfillment.box_header_returns')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<!-- tabs -->
	            		<div class="nav-tabs-custom">
		            		<ul class="nav nav-tabs">
								<li id="in_transit" class="active"><a href="#">@lang('admin/fulfillment.returns_in_transit')</a></li>
								<li id="done"><a href="#">@lang('admin/fulfillment.returns_done')</a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active">
				            		<table id="returns_list_table" class="table table-bordered table-striped">
					                    <thead>
					                    	<tr>
						                        <th>@lang('admin/fulfillment.returns_list_table_hubwire_sku')</th>
						                        <th>@lang('admin/fulfillment.returns_list_table_item_name')</th>
						                        <th>@lang('admin/fulfillment.returns_list_table_order_id')</th>
						                        <th>@lang('admin/fulfillment.returns_list_table_created_at')</th>
						                        <th>@lang('admin/fulfillment.returns_list_table_completed_at')</th>
						                        <th>@lang('admin/fulfillment.returns_list_table_status')</th>
						                        <th>@lang('admin/fulfillment.returns_list_table_actions')</th>
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/jquery.timeago.js', env('HTTPS', false)) }}" type="text/javascript"></script>

<script type="text/javascript">
function processReject(el) {
	//console.log($(el).data());
	var btnName = $(el).attr('name');
	var trigger = $(el).closest('td').find('[rel="popover"]').first();

	if (btnName == 'btn_cancel') {
		trigger.popover('hide');
	}
}
jQuery(document).ready(function(){
	$('body').on('change', '#remark-options', function(){
		var elem = $('{!! Form::textarea('remark',null,['class'=>'form-control reject-reason-input', 'style'=>'width:100%;','placeholder'=>'Remark'])!!}')
				if($(this).val() == 3)
					$('#remark-div').html(elem);
				else
					$('#remark-div').html('');
	});
	var channel_id = '{{ $channel_id }}';

	var popOverSettings = {
		trigger: 'click',
	    placement: 'left',
	    title: 'Reject Reason',
	    // container: 'body',
	    html: true,
	    selector: '[rel="popover"]', // Specify the selector here
	    content: function () {
	    	return getPopoverContent();
	    }
	}

	$('body').popover(popOverSettings);

	waitingDialog.show('Retrieving data....', {dialogSize: 'sm'});
	var table = jQuery('#returns_list_table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{route("admin.fulfillment.return.search")}}?status=in_transit' + (channel_id.length > 0 ? '&channel_id='+channel_id : ''),
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[3, "desc"]],
		"scrollX": false,
		"scrollY": false,
		"autoWidth": true,
		"orderCellsTop": true,
		"drawCallback": function (settings) {
			jQuery(window).scrollTop(0);
			waitingDialog.hide();
		},
		"initComplete": function(settings, json) {
			checkStatusToDisplayColumns();
		},
		"columns": [
            { "data": "hubwire_sku", "name": "hubwire_sku", "targets": 0 },
            { "data": "item_name", "name": "item_name", "targets": 1 },
            { "data": "order_id", "name": "order_id", "targets": 2 },
            { "data": "created_at", "name": "created_at", "targets": 3 }, 
            { "data": "completed_at", "name": "completed_at", "targets": 4, "visible": false }, 
            { "data": "status", "name": "status", "targets": 5 },
            { "data": "actions", "name": "actions", "targets": 6, "orderable": false }
        ]
    });

    jQuery('#returns_list_table').offset().top;

    $("ul.nav li").on('click', function(e) {
    	if ($(this).attr('id') == 'in_transit') {
    		$("#done").removeClass('active');
    		$(this).addClass('active');
    	}
    	else if ($(this).attr('id') == 'done') {
    		$("#in_transit").removeClass('active');
    		$(this).addClass('active');
    	}

    	waitingDialog.show('Retrieving data....', {dialogSize: 'sm'});
    	table.ajax.url('{{route("admin.fulfillment.return.search")}}?status=' + $(this).attr('id') + (channel_id.length > 0 ? '&channel_id='+channel_id : '')).load();

    	checkStatusToDisplayColumns();
    });

    function checkStatusToDisplayColumns() {
    	if ($("#in_transit").hasClass("active")) {
    		table.column(4).visible(false, false);
    		table.column(6).visible(true);
    	}
    	else if ($("#done").hasClass("active")) {
    		table.column(6).visible(false, false);
    		table.column(4).visible(true);
    		table.order([4, 'desc'])
    	}
    	table.columns.adjust().draw();
    }

    function getPopoverContent() {
			return ('{!! Form::select('remark',$reasons,null,['class'=>'form-control','required','id'=>'remark-options','placeholder'=>'Please select reason']) !!}'
					+ '<div id="remark-div"></div>'
					+ '<div class="popover-buttons-div"><button type="button" name="btn_confirm" class="btn btn-default btn-table form-inline btn-submit-reject" style="margin-right:5px;" onclick="processReject(this)">@lang("admin/fulfillment.button_confirm")</button>'
					+ '<button type="button" name="btn_cancel" class="btn btn-default btn-table form-inline" onclick="processReject(this)">@lang("admin/fulfillment.button_cancel")</button></div>');
		
	}

	$('.btn-reject').on('click', function (e) {
	    $('.btn-reject').not(this).popover('hide');
	});

	$('body').on('click', function (e) {
	    $('[rel=popover]').each(function () {
	        // hide any open popovers when the anywhere else in the body is clicked
	        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
	            $(this).popover('hide');
	        }
	    });
	});

	
	$('body').on('shown.bs.popover', function () {
		$('.btn-submit-reject').on('click', function(e){
			var submitForm = $(this).closest('form');
			var selectInput = $(this).closest('form').find('select');
			if(selectInput.val() == 3){
				var reasonInput = $(this).closest('form').find('.reject-reason-input');
				var reason = reasonInput.val();
				if(reason.trim() == ''){
					alert('Please enter the reject reason.');
					test = 'bar';
					return false;
				}
			}
			submitForm.submit();
		});
	})
});
</script>
@append