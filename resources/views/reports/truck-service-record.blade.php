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
	            		<div class="data-table-header">Truck Service Record</div>
	            		<table id="table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Customer</th>
			                        <th>Vehicle No.</th>
			                        <th>Customer</th>
			                        <th>Vehicle Type</th>
			                        <th>Vehicle No.</th>
			                        <th>Position</th>
			                        <th>Jobsheet Info</th>
			                        <th>Tyre In</th>
			                        <th>Tyre Out</th>
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
	var table = jQuery('#table').DataTable({
		"dom": '<"clearfix"B><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ route('reports.truckServiceRecord.load') }}',
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columnDefs": [
			{ "targets": [0, 1], "visible": false }
        ],
        "columns": [
            { "data": "customer", "name": "customer", "targets": 0 },
            { "data": "vehicleNo", "name": "vehicleNo", "targets": 1 },
            { "data": "customer2", "name": "customer2", "targets": 2, "orderable": false },
            { "data": "vehicleType", "name": "vehicleType", "targets": 3, "orderable": false },
            { "data": "vehicleNo2", "name": "vehicleNo2", "targets": 4, "orderable": false },
            { "data": "position", "name": "position", "targets": 5, "orderable": false },
            { "data": "info", "name": "info", "targets": 6, "orderable": false },
            { "data": "in", "name": "in", "targets": 7, "orderable": false },
            { "data": "out", "name": "out", "targets": 8, "orderable": false },
        ],
		
		buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_truck_service_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_truck_service_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });
});
</script>
@append