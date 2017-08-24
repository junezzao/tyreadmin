@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
@lang('titles.users')
@stop

@section('content')
	<section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/user.box_header_users')</h3>
	            	</div>
	            	<div class="box-body">
	            		<table id="user_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>ID</th>
			                        <th>Name</th>
			                        <th>Email</th>
			                        <th>Contact No</th>
			                        <th>Company Name</th>
			                        <th>Status</th>
			                        <th>User Type</th>
			                        <th>Actions</th>
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

@include('includes.datatables')
@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function(){
	var table = jQuery('#user_table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ URL::to("admin/users/table_data") }}',
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[0, "desc"]],
		"scrollX": false,
		"scrollY": false,
		"autoWidth": true,
		"orderCellsTop": true,
		"columns": [
            { "data": "id", "name": "id", "targets": 0 },
            { "data": "name", "name": "name", "targets": 1 },
            { "data": "email", "name": "email", "targets": 2 },
            { "data": "contact_no", "name": "contact_no", "targets": 3 },
            { "data": "company_name", "name": "company_name", "targets": 4 },
            { "data": "status", "name": "status", "targets": 5 },
            { "data": "user_type", "name": "user_type", "targets": 6 },
            { "data": "actions", "name": "actions", "targets": 7, "orderable": false }
        ]
    });
});
</script>
@append