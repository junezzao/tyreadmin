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
		                    	<?php 
		                    		$lastCustomer 		= '';
		                    		$lastVehicleType 	= '';
		                    		$lastVehicleNo 		= '';
		                    	?>
		                    	@foreach ($data as $customer => $customerData)
		                    		@foreach ($customerData as $vehicleType => $vehicleData)
		                    			@foreach ($vehicleData as $vehicleNo => $positionData)
		                    				@foreach ($positionData as $position => $jobs)
		                    					@foreach ($jobs as $index => $job)
		                    						<tr>
								                        <td>{{ $customer }}</td>
								                        <td>{{ $vehicleNo }}</td>
								                        <td><b>@if($customer != $lastCustomer) {{ $customer }} @endif</b></td>
								                        <td><b>@if($vehicleType != $lastVehicleType) {{ strtoupper($vehicleType) }} @endif</b></td>
								                        <td><b>@if($vehicleNo != $lastVehicleNo) {{ $vehicleNo }} @endif</b></td>
								                        <td>{{ $position }}</td>
								                        <td>{{ $job['info'] }}</td>
								                        <td>{{ $job['in'] }}</td>
								                        <td>{{ $job['out'] }}</td>
							                      	</tr>
							                      	<?php
							                      		$lastCustomer 		= $customer;
							                      		$lastVehicleType 	= $vehicleType;
		                    							$lastVehicleNo 		= $vehicleNo;
							                      	?>
		                    					@endforeach
		                    				@endforeach
		                    			@endforeach
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
	var table = jQuery('#table').DataTable({
		"dom": '<"clearfix"B><"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
		"lengthMenu": [[50, 80, 100], [50, 80, 100]],
		"pageLength": 50,
		"order": [],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columnDefs": [
			{ "targets": [0, 1], "visible": false }
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