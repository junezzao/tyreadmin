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
		                    	@foreach ($missing as $data)
					                <tr>
					                    <td>{{ $data['line'] }}</td>
					                    <td>{{ $data['jobsheet'] }}</td>
					                    <td>{{ $data['type'] }}</td>
					                    <td>{{ $data['customer'] }}</td>
					                    <td>{{ $data['vehicle'] }}</td>
					                    <td>{{ $data['position'] }}</td>
					                    <td>{{ $data['in_out'] }}</td>
					                    <td>{{ $data['tyre'] }}</td>
					                    <td>{{ $data['remark'] }}</td>
					                </tr>
					            @endforeach
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
		                    	@foreach ($repeated as $serialNo => $fittings)
		                    		@foreach ($fittings as $index => $fitting)
						                <tr>
						                    <td>{{ $serialNo }}</td>
						                    <td>@if($index == 0) {{ $serialNo }} @endif</td>
						                    <td>{{ $fitting }}</td>
						                </tr>
						            @endforeach
					            @endforeach
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
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [[0, "asc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
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
		"lengthMenu": [[10, 30, 50], [10, 30, 50]],
		"pageLength": 10,
		"order": [],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
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
        ]
    } );
});
</script>
@append