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
	            		<div class="data-table-header">Missing Serial No. Entry</div>
	            		<table id="missing_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Line No.</th>
			                        <th>Jobsheet No.</th>
			                        <th>Yard/Breakdown</th>
			                        <th>Customer</th>
			                        <th>Vehicle Info</th>
			                        <th>Position</th>
			                        <th>Tyre In/Out</th>
			                        <th>Tyre Info</th>
			                        <th>Remark</th>
		                      	</tr>
		                    </thead>
		                    <tbody>
		                    </tbody>
	                    </table>

	                    <div class="data-table-header">Repeated Serial No. Without Removing</div>
	            		<table id="repeated_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Serial No.</th>
			                        <th>Serial No.</th>
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
		"ajax": '{{ route('reports.serialNoAnalysis.load.missing') }}',
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [[0, "asc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columns": [
            { "data": "line", "name": "line", "targets": 0 },
            { "data": "jobsheet", "name": "jobsheet", "targets": 1 },
            { "data": "type", "name": "type", "targets": 2 },
            { "data": "customer", "name": "customer", "targets": 3 },
            { "data": "vehicle", "name": "vehicle", "targets": 4 },
            { "data": "position", "name": "position", "targets": 5 },
            { "data": "in_out", "name": "in_out", "targets": 7 },
            { "data": "tyre", "name": "tyre", "targets": 8 },
            { "data": "remark", "name": "remark", "targets": 9 },
        ],
		buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_serial_no_entry',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_serial_no_entry',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });

    var repeated_table = $('#repeated_table').DataTable({
        "dom": '<"clearfix"B><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
        "ajax": '{{ route('reports.serialNoAnalysis.load.repeated') }}',
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columns": [
            { "data": "serialNo", "name": "serialNo", "targets": 0 },
            { "data": "serialNo", "name": "serialNo", "targets": 1 },
            { "data": "fitting", "name": "fitting", "targets": 2, "orderable": false },
        ],
		"columnDefs": [
			{ "targets": 0, "visible": false }
        ],
        buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_repeated_serial_no_without_removing',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_repeated_serial_no_without_removing',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ],
        "drawCallback": function( settings ) {
        	console.log('drawn');
        	var lastSerialNo = '';
        	var serialNo = '';
	        $('#repeated_table > tbody  > tr').each(function() {
	        	serialNo = $(this).children('td:eq(0)').text();
	        	if(serialNo == lastSerialNo) {
					$(this).children('td:eq(0)').text('');
	        	}
	        	lastSerialNo = serialNo;
	        });
	    }
    } );
});
</script>
@append