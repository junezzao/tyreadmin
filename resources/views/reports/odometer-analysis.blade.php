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
		                    	@foreach ($missing as $data)
					                <tr>
					                    <td>{{ $data['line'] }}</td>
					                    <td>{{ $data['date'] }}</td>
					                    <td>{{ $data['jobsheet'] }}</td>
					                    <td>{{ $data['vehicle'] }}</td>
					                    <td>{{ $data['remark'] }}</td>
					                </tr>
					            @endforeach
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
		                    	@foreach ($less as $vehicle => $readings)
		                    		@foreach ($readings as $index => $reading)
						                <tr>
						                    <td>{{ $vehicle }}</td>
						                    <td>@if($index == 0) {{ $vehicle }} @endif</td>
						                    <td>{{ $reading }}</td>
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
        ]
    });
});
</script>
@append