@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/page-titles.page_title_merchants')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.content_header_merchants')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_merchants')</h3>
	              		@if($user->can('create.merchant'))
	              		<div class="pull-right"><a href="{{ URL::to('admin/merchants/create') }}" class="btn btn-default right">Add New Merchant</a></div>
	            		@endif
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<!-- <pre><?php //var_dump($errors);?></pre> -->
	            		<table id="data_table" class="table table-bordered table-striped">
	                    <thead>
	                      <tr>
	                        <th>Id</th>
	                        <th>Name</th>
	                        <th>Account Manager</th>
	                        <th>Status</th>
	                        <th>Created At</th>
	                        <th>Updated At</th>
	                        <th>Actions</th>
	                      </tr>
	                    </thead>
	                    <tbody>
	                      <tr>
	                        <td>Id</td>
	                        <td>Name</td>
	                        <td>Account Manager</td>
	                        <td>Status</td>
	                        <td>Created At</td>
	                        <th>Updated At</th>
	                        <td>Actions</td>
	                      </tr>
	                    </tbody>
	                    </table>
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<style>
.dataTables_customFilter{
	
}
.dataTables_customFilter label{
	font-weight: normal;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function(){
	@if($user->can('edit.merchant'))
	$('#data_table').on('click','.confirm',function(){
		var c = confirm('Are you sure? '+$(this).data('message'));
		var url = $(this).data('href');
		var status = $(this).data('status');
		if(c)
		{
			$('<form action="'+url+'" method="POST"><?php echo Form::token();?><input type="hidden" name="status" value="'+status+'"><input type="hidden" name="_method" value="PUT"></form>').appendTo('body').submit();
		}
	});
	@endif
	var channel_id = '{{ $channel_id }}';
	var filterBy = '{{ $filterBy }}';

	var dataTable = jQuery('#data_table').DataTable({
		"dom": '<"filterbar"><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ URL::to("admin/merchants/table_data") }}' + (filterBy.length > 0 ? '?filterBy='+filterBy : '') + (channel_id.length > 0 ? '&channel_id='+channel_id : ''),
		"orderCellsTop": true,
		"columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "ae" },
            { "data": "status" },
            { "data": "created_at" },
            { "data": "updated_at"},
            { "data": "actions", "orderable": false }
        ],
        "order": [[ 5, 'desc' ]],
	    "paging": true,
	    "pageLength": 30,
	    "lengthChange": true,
	    "lengthMenu": [[10, 30, 50, 100, 250, 500], [10, 30, 50, 100, 250 , 500]],
	    "searching": true,
	    "ordering": true,
	    "info": true,
	    "autoWidth": false
    });
    var filterDiv = '\
		<div class="dataTables_customFilter">\
			<label>Filter By: \
				<select id="merchant-filter" class="form-control" name="filterType">\
					<option value="all">All</option>\
					<option value="newSignUps">New merchants this month</option>\
					<option value="liveByWeek">Merchants live in the last 7 days</option>\
					<option value="liveByMonth">Merchants live in the last 30 days</option>\
				</select>\
			</label>\
		</div>\
    ';
    $("div.filterbar").html(filterDiv);
    $('#merchant-filter').select2({
		minimumResultsForSearch: Infinity
	});
    $('#merchant-filter').val(filterBy).trigger('change');

    $("#merchant-filter").change(function () {
        var val = this.value;
        waitingDialog.show('Retrieving merchants...', {dialogSize: 'sm'});
        dataTable.ajax.url('{{ URL::to("admin/merchants/table_data") }}'+'?filterBy='+val).load(function(){
        	waitingDialog.hide();
        });
    });
    // on dd change do table.ajax.url( 'newData.json' ).load();
});
</script>
@append