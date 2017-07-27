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
	            		<div class="data-table-header">Customer Truck Tyre Cost</div>
	            		<table id="table" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
			                        <th>Customer</th>
			                        <th>Customer</th>
			                        <th>Date Range</th>
			                        <th>Vehicle Type</th>
			                        <th>Rank</th>
			                        <th>Vehicle No.</th>
			                        <th>Total Tyre Cost</th>
		                      	</tr>
		                    </thead>
		                    <tbody>
		                    	<?php 
		                    		$lastCustomer 		= '';
		                    		$lastVehicleType 	= '';
		                    	?>
		                    	@foreach ($data as $customer => $customerData)
		                    		@foreach ($customerData['costs'] as $vehicleType => $costs)
		                    			@foreach ($costs as $index => $cost)
		                    				<tr>
		                    					<td>{{ $customer }}</td>
		                    					<td><b>@if($customer != $lastCustomer) {{ $customer }} @endif</b></td>
		                    					<td>@if($customer != $lastCustomer) {{ $customerData['from'] }} till {{ $customerData['to'] }} @endif</td>
		                    					<td><b>@if($vehicleType != $lastVehicleType) {{ strtoupper($vehicleType) }} @endif</b></td>
		                    					<td>{{ $index + 1 }}</td>
		                    					<td>{{ $cost['vehicleNo'] }}</td>
		                    					<td>{{ $cost['cost'] }}</td>
		                    				</tr>
		                    				<?php
						                      		$lastCustomer 		= $customer;
						                      		$lastVehicleType 	= $vehicleType;
						                      	?>
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
			{ "targets": 0, "visible": false }
        ],
		buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_customer_truck_tyre_cost',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_customer_truck_tyre_cost',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ]
    });
});
</script>
@append