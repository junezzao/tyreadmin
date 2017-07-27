@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/page-titles.page_title_suppliers')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.content_header_suppliers')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_suppliers')</h3>
	              		@if($user->can('create.supplier'))
	              		<div class="pull-right"><a href="{{ URL::route('admin.suppliers.create') }}" class="btn btn-default right">Add New Supplier</a></div>
	            		@endif
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<!-- <pre><?php var_dump($errors);?></pre> -->
	            		<table id="data_table" class="table table-bordered table-striped">
	                    <thead>
	                      <tr>
	                        <th>Id</th>
	                        @if(!$user->is('clientadmin|clientuser'))
        						<th>Merchant</th>
	                        @endif
	                        <th>Name</th>
	                        <th>Phone</th>
	                        <th>Address</th>
	                        <th>Contact Person</th>
	                        <th>Status</th>
	                        <th>Mobile</th>
	                        <th>Updated At</th>
	                        <th>Actions</th>
	                      </tr>
	                    </thead>
	                    <tbody>
	                      <tr>
	                        <td>Id</td>
	                        @if(!$user->is('clientadmin|clientuser'))
        						<td>Merchant</td>
	                        @endif
	                        <td>Name</td>
	                        <td>Phone</td>
	                        <td>Address</td>
	                        <td>Contact Person</td>
	                        <td>Status</td>
	                        <td>Mobile</td>
	                        <td>Updated At</td>
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
<script type="text/javascript">
jQuery(document).ready(function(){
	@if($user->can('edit.supplier'))
	$('#data_table').on('click','.confirm',function(){
		var c = confirm('Are you sure? '+$(this).data('message'));
		var url = $(this).data('href');
		var status = $(this).data('status');
		if(c)
		{
			$('<form action="'+url+'" method="POST"><?php echo Form::token();?><input type="hidden" name="active" value="'+status+'"><input type="hidden" name="_method" value="PUT"></form>').appendTo('body').submit();
		}
	});
	@endif

	var channel_id = '{{ $channel_id }}';
	jQuery('#data_table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ URL::to("admin/suppliers/table_data") }}' + (channel_id.length > 0 ? '?channel_id='+channel_id : ''),
		"orderCellsTop": true,
		"columns": [
            { "data": "id" },
            @if(!$user->is('clientadmin|clientuser'))
            { "data": "merchant" },
            @endif
            { "data": "name" },
            { "data": "phone" },
            { "data": "address" },
            { "data": "contact_person" },
            { "data": "status" },
            { "data": "mobile" },
            { "data": "updated_at" },
            { "data": "actions", "orderable": false }
        ],
	    "paging": true,
	    "pageLength": 30,
	    "lengthChange": true,
	    "lengthMenu": [[10, 30, 50, 100, 250, 500], [10, 30, 50, 100, 250 , 500]],
	    "searching": true,
		"order": [[8, "desc"]],
	    "ordering": true,
	    "info": true,
	    "autoWidth": false
    });
});
</script>
@append