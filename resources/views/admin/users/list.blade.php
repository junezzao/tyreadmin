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
			                        <th style="width:7%">ID</th>
			                        <th style="width:15%">Name</th>
			                        <th style="width:15%">Email</th>
			                        <th style="width:15%">Contact No</th>
			                        <th style="width:15%">Company Name</th>
			                        <th style="width:15%">Status</th>
			                        <th style="width:15%">User Type</th>
			                        <th style="width:15%">Actions</th>
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script type="text/javascript">
jQuery(document).ready(function(){
	var table = jQuery('#user_table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ URL::to("admin/users/table_data") }}',
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[0, "desc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"fnDrawCallback": function (o) {
			jQuery(window).scrollTop(0);
		},
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

    if (!"{{Auth::user()->is('clientuser')}}" == 1) {
    	// Get the column API object
      	var actions_col = table.column( 7 );

        // Toggle the visibility
        actions_col.visible(true);

        if (!"{{Auth::user()->is('clientuser|clientadmin')}}" == 1) {
        	var client_name_col = table.column( 5 );
        	client_name_col.visible(true);
        }
    }

    jQuery('#user_table').offset().top;

    $("#new_user").click(function() {
    	window.location.href = "users/create";
    });

	$(document).on('click', '.confirmation', function (e) {
		//e.preventDefault();
        return confirm('Are you sure you want to delete this user?');
    });
});
</script>
@append