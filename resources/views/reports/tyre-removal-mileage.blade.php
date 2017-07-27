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
		                    	@foreach ($data as $serialNo => $fittings)
		                    		<?php $totalMileage = 0; ?>
	                    			@foreach ($fittings as $index => $fitting)
	                    				<?php if(is_numeric($fitting['mileage'])) $totalMileage += $fitting['mileage']; ?>
						                <tr>
						                    <td>{{ $serialNo }}</td>
						                    <td>@if($index == 0) {{ $serialNo }} @endif</td>
						                    <td>{{ $fitting['tyre'] }}</td>
						                    <td>{{ $fitting['tyre_retread'] }}</td>
						                    <td>{{ $fitting['in_out'] }}</td>
						                    <td>{{ $fitting['remark'] }}</td>
						                    <td>{{ $fitting['date'] }}</td>
						                    <td>{{ $fitting['jobsheet'] }}</td>
						                    <td>{{ $fitting['vehicle'] }}</td>
						                    <td>{{ $fitting['position'] }}</td>
						                    <td>{{ $fitting['odometer'] }}</td>
						                    <td>{{ $fitting['mileage'] }}</td>
						                </tr>
						            @endforeach
						            <tr>
					                    <td>{{ $serialNo }}</td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td></td>
					                    <td><b>Total Mileage</b></td>
					                    <td>{{ $totalMileage }}</td>
					                </tr>
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
	var report_table = jQuery('#report_table').DataTable({
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