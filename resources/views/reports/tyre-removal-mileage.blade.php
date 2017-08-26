@extends('layouts.master')

@section('title')
@lang('titles.reports')
@stop

@section('content')
	<section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	
	            	<div class="box-body">
	            		<div class="data-table-header">Tyre Removal Mileage</div>
	            		<table id="report_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Serial No.</th>
			                        <th>Serial No.</th>
			                        <th>Tyre Info</th>
			                        <th>Retread Info</th>
			                        <th>Tyre In/Out</th>
			                        <th>Remark</th>
			                        <th>Date</th>
			                        <th>Jobsheet</th>
			                        <th>Vehicle Info</th>
			                        <th>Position</th>
			                        <th>Odometer</th>
			                        <th>Mileage</th>
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
	var report_table = jQuery('#report_table').DataTable({
		"dom": '<"clearfix"B><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ route('reports.tyreRemovalMileage.load') }}',
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [[0, "asc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columns": [
            { "data": "serialNo", "name": "serialNo", "targets": 0 },
            { "data": "serialNo2", "name": "serialNo2", "targets": 1, "orderable": false },
            { "data": "tyre", "name": "tyre", "targets": 2, "orderable": false },
            { "data": "tyre_retread", "name": "tyre_retread", "targets": 3, "orderable": false },
            { "data": "in_out", "name": "in_out", "targets": 4, "orderable": false },
            { "data": "remark", "name": "remark", "targets": 5, "orderable": false },
            { "data": "date", "name": "date", "targets": 6, "orderable": false },
            { "data": "jobsheet", "name": "jobsheet", "targets": 7, "orderable": false },
            { "data": "vehicle", "name": "vehicle", "targets": 8, "orderable": false },
            { "data": "position", "name": "position", "targets": 9, "orderable": false },
            { "data": "odometer", "name": "odometer", "targets": 10, "orderable": false },
            { "data": "mileage", "name": "mileage", "targets": 11, "orderable": false },
        ],
		"columnDefs": [
			{ "targets": 0, "visible": false }
        ],
		buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_tyre_removal_mileage',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_tyre_removal_mileage',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });
});
</script>
@append