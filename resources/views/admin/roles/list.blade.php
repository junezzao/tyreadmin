@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/user.page_title_access_management')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/user.content_header_access_management')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/user.box_header_roles')</h3>
	              		@if (Auth::user()->can('create.roles'))
	              			<a href="{{route('admin.roles.create')}}" class="btn btn-default pull-right">@lang('admin/user.button_add_new_role')</a>
	              		@endif
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<table id="roles_table" class="table table-bordered table-striped">
		                    <thead>
		                      <tr>
		                        <th>@lang('admin/user.role_table_name')</th>
		                        <th>@lang('admin/user.role_table_description')</th>
		                        <th>@lang('admin/user.role_table_user_count')</th>
		                        <th>@lang('admin/user.role_table_level')</th>
		                        <th>@lang('admin/user.role_table_status')</th>
		                        <th>@lang('admin/user.role_table_actions')</th>
		                      </tr>
		                    </thead>
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
	$(document).ready(function(){
		$('#roles_table').DataTable({
			"data": <?php echo $data ?>,
			"orderCellsTop": true,
			"columns": [
	            { "data": "name" },
	            { "data": "description" },
	            { "data": "users_count" },
	            { "data": "level" },
	            { "data": "status" },
				{ "data": "actions" }
	        ],
		    "paging": true,
		    "lengthChange": false,
		    "pageLength": 30,
		    "searching": true,
		    "ordering": true,
		    "info": true,
		    "autoWidth": false
	    });

	    $('.confirm-deactivate').on('click', function (e) {
	        return confirm('Deactivating a role will deactivate all users of that role. Reactivating the role will NOT reactivate the users. Are you sure you want to deactivate this role?');
	    });

	    $('.confirm-activate').on('click', function (e) {
	        return confirm('Are you sure you want to activate this role?');
	    });
	});
</script>
@append