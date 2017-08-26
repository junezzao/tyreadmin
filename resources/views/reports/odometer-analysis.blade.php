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
	            		<div class="data-table-header">Missing Odometer Entry</div>
	            		<table id="missing_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Line No.</th>
			                        <th>Date</th>
			                        <th>Jobsheet No.</th>
			                        <th>Vehicle Info</th>
			                        <th>Remark</th>
		                      	</tr>
		                    </thead>
		                    <tbody>
		                    </tbody>
	                    </table>

	                    <div class="data-table-header">Odometer Less Than Previous Record</div>
	            		<table id="less_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Vehicle Info</th>
			                        <th>Vehicle Info</th>
			                        <th>Remarks</th>
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
	var missing_table = jQuery('#missing_table').DataTable({
		"dom": '<"clearfix"B><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ route('reports.odometerAnalysis.load.missing') }}',
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [[0, "asc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columns": [
            { "data": "line", "name": "line", "targets": 0 },
            { "data": "date", "name": "date", "targets": 1 },
            { "data": "jobsheet", "name": "jobsheet", "targets": 2 },
            { "data": "vehicle", "name": "vehicle", "targets": 3 },
            { "data": "remark", "name": "remark", "targets": 4 },
        ],
		buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_odometer_entry',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_odometer_entry',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });

	var less_table = jQuery('#less_table').DataTable({
		"dom": '<"clearfix"B><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"ajax": '{{ route('reports.odometerAnalysis.load.less') }}',
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columns": [
            { "data": "vehicle", "name": "vehicle", "targets": 0 },
            { "data": "vehicle", "name": "vehicle", "targets": 1 },
            { "data": "reading", "name": "reading", "targets": 2, "orderable": false  },
        ],
		"columnDefs": [
			{ "targets": 0, "visible": false }
        ],
		buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_odometer_less_than_previous_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_odometer_less_than_previous_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ],
        "drawCallback": function( settings ) {
        	var lastVehicleNo = '';
        	var vehicleNo = '';
	        $('#less_table > tbody  > tr').each(function() {
	        	vehicleNo = $(this).children('td:eq(0)').text();
	        	if(vehicleNo == lastVehicleNo) {
					$(this).children('td:eq(0)').text('');
	        	}
	        	lastVehicleNo = vehicleNo;
	        });
	    }
    });
});
</script>
@append