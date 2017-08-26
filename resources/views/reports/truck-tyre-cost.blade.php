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
		"ajax": '{{ route('reports.truckTyreCost.load') }}?sort={{ $sort }}&limit={{ $limit }}',
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"columns": [
            { "data": "customer", "name": "customer", "targets": 0 },
            { "data": "customer2", "name": "customer2", "targets": 1, "orderable": false },
            { "data": "date", "name": "date", "targets": 2, "orderable": false },
            { "data": "vehicleType", "name": "vehicleType", "targets": 3, "orderable": false },
            { "data": "rank", "name": "rank", "targets": 4, "orderable": false },
            { "data": "vehicleNo", "name": "vehicleNo", "targets": 5, "orderable": false },
            { "data": "cost", "name": "cost", "targets": 6, "orderable": false },
        ],
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