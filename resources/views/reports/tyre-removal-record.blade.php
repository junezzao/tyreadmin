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
	            		<div class="data-table-header">Missing Tyre Out Record</div>
	            		<table id="only_in_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Line No.</th>
			                        <th>Jobsheet No.</th>
			                        <th>Yard/Breakdown</th>
			                        <th>Customer</th>
			                        <th>Vehicle Info</th>
			                        <th>Position</th>
			                        <th>Remark</th>
		                      	</tr>
		                    </thead>
		                    <tbody>
		                    	@foreach ($only_in as $data)
					                <tr>
					                    <td>{{ $data['line'] }}</td>
					                    <td>{{ $data['jobsheet'] }}</td>
					                    <td>{{ $data['type'] }}</td>
					                    <td>{{ $data['customer'] }}</td>
					                    <td>{{ $data['vehicle'] }}</td>
					                    <td>{{ $data['position'] }}</td>
					                    <td>{{ $data['remark'] }}</td>
					                </tr>
					            @endforeach
		                    </tbody>
	                    </table>

	                    <div class="data-table-header">Missing Tyre In Record</div>
	            		<table id="only_out_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Line No.</th>
			                        <th>Jobsheet No.</th>
			                        <th>Yard/Breakdown</th>
			                        <th>Customer</th>
			                        <th>Vehicle Info</th>
			                        <th>Position</th>
			                        <th>Remark</th>
		                      	</tr>
		                    </thead>
		                    <tbody>
		                    	@foreach ($only_out as $data)
					                <tr>
					                    <td>{{ $data['line'] }}</td>
					                    <td>{{ $data['jobsheet'] }}</td>
					                    <td>{{ $data['type'] }}</td>
					                    <td>{{ $data['customer'] }}</td>
					                    <td>{{ $data['vehicle'] }}</td>
					                    <td>{{ $data['position'] }}</td>
					                    <td>{{ $data['remark'] }}</td>
					                </tr>
					            @endforeach
		                    </tbody>
	                    </table>

	                    <div class="data-table-header">Tyre In/Out Conflict</div>
	            		<table id="conflict_table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Vehicle Info</th>
			                        <th>Vehicle Info</th>
			                        <th>Info</th>
			                        <th>Remark</th>
		                      	</tr>
		                    </thead>
		                    <tbody>
		                    	@foreach ($conflict as $vehicle => $fittings)
		                    		@foreach ($fittings as $index => $fitting)
						                <tr>
						                    <td>{{ $vehicle }}</td>
						                    <td>@if($index == 0) {{ $vehicle }} @endif</td>
						                    <td>{{ $fitting['info'] }}</td>
						                    <td>{{ $fitting['remark'] }}</td>
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
	var only_in_table = jQuery('#only_in_table').DataTable({
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
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_tyre_out_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_tyre_out_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });

	var only_out_table = jQuery('#only_out_table').DataTable({
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
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_tyre_in_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_missing_tyre_in_record',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });

	var conflict_table = jQuery('#conflict_table').DataTable({
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
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_tyre_in_out_conflict',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_tyre_in_out_conflict',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });
});
</script>
@append