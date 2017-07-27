@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/page-titles.page_title_issuing_companies')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.content_header_issuing_companies')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_issuing_companies')</h3>
	              		@if($user->can('create.issuingcompany'))
	              		<div class="pull-right"><a href="{{ URL::to('admin/issuing_companies/create') }}" class="btn btn-default right">Add New Issuing Company</a></div>
	            		@endif
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<!-- <pre><?php //var_dump($errors);?></pre> -->
	            		<table id="data_table" class="table table-bordered table-striped">
	                    <thead>
	                      <tr>
	                        <th>Id</th>
	                        <th>Name</th>
	                        <th>GST</th>
	                        <th>GST Registration No.</th>
                            <th>Prefix</th>
                            <th>Channel Count</th>
	                        <th>Created At</th>
	                        <th>Updated At</th>
	                        <th>Actions</th>
	                      </tr>
	                    </thead>
	                    <tbody>
	                      <tr>
	                        <td>Itdd</td>
                            <td>Name</td>
                            <td>GST</td>
                            <td>GST Registration No.</td>
                            <td>Prefix</td>
                            <td>Channel Count</td>
	                        <td>Created At</td>
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
	@if($user->can('edit.issuingcompany'))
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
	jQuery('#data_table').DataTable({
		
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
        "ajax": "{!! route('admin.issuing-companies.table_data') !!}",
		"orderCellsTop": true,
        "order": [[5, "desc"]],
		"columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "gst_reg" },
            { "data": "gst_reg_no" },
            { "data": "prefix" },
            { "data": "channel_count" },
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
});
</script>
@append