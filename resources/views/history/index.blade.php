@extends('layouts.master')

@section('title')
	@lang('titles.tyre_history')
@stop

@section('content')
    <section class="content">
	    <div class="row data-upload">
	        <div class="col-xs-12">
	          	<div class="box">
	            	
	            	<div class="box-body tyre-history" style="overflow-y:auto">
	            		
	            		<div class="view">
		            		<div class="title">View by Truck Position</div>
		            		<div class="level level-0"><div class="item header"><a>Customer <div class="symbol">&#9654;</div></a></div>
		            		@foreach ($truckPositionData as $customer => $customerData) 
		            			<div class="level level-1"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>

		            			@foreach ($customerData as $vehicle => $vehicleData)
		            				<div class="level level-2"><div class="item"><a>{{ $vehicle }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($vehicleData as $vehicleNo => $vehicleNoData)
			            				<div class="level level-3"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>

			            				@foreach ($vehicleNoData as $position => $positionData)
				            				<div class="level level-4"><div class="item"><a>Pos {{ $position }} <div class="symbol">&#9654;</div></a></div>

				            				@foreach ($positionData as $index => $job)
				            					<div class="level level-5"><div class="item"><a>{{ $job['date'] }} <div class="symbol">&#9654;</div></a></div>
						            				
						            				<div class="level level-6"><div class="item"><a>IN: {{ $job['in'] }} <div class="symbol">&#9654;</div></a></div>
							            				<div class="level level-7"><div class="item">{{ $job['invoice'] }}</div></div>
							            			</div>

							            			<div class="level level-6"><div class="item">OUT: {{ $job['out'] }}</div></div>
						            			</div>
					            			@endforeach
					            			</div>
				            			@endforeach
				            			</div>
			            			@endforeach
			            			</div>
		            			@endforeach
		            			</div>
		            		@endforeach
		            		</div>
		            	</div>

		            	<div class="view">
		            		<div class="title">View by Truck Serviced Date and Job Sheet</div>
		            		<div class="level level-0"><div class="item header"><a>Customer <div class="symbol">&#9654;</div></a></div>
		            		@foreach ($truckServiceData as $customer => $customerData) 
		            			<div class="level level-1"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>

		            			@foreach ($customerData as $vehicle => $vehicleData)
		            				<div class="level level-2"><div class="item"><a>{{ $vehicle }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($vehicleData as $vehicleNo => $vehicleNoData)
			            				<div class="level level-3"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>

			            				@foreach ($vehicleNoData as $jobsheetDate => $jobsheetDateData)
				            				<div class="level level-4"><div class="item"><a>{{ $jobsheetDate }} <div class="symbol">&#9654;</div></a></div>

				            					@foreach ($jobsheetDateData as $jobsheet => $jobsheetData)
					            					<div class="level level-5"><div class="item"><a>{{ str_replace('TOTAL_PRICE', 'RM'.number_format($jobsheetData['totalPrice'], 2), $jobsheet) }} <div class="symbol">&#9654;</div></a></div>
							            				
							            				@foreach ($jobsheetData['positions'] as $index => $job)
							            					<div class="level level-6"><div class="item"><a>Pos {{ $job['position'] }} <div class="symbol">&#9654;</div></a></div>
									            				
									            				<div class="level level-7"><div class="item"><a>IN: {{ $job['in'] }} <div class="symbol">&#9654;</div></a></div>
										            				<div class="level level-8"><div class="item">{{ $job['invoice'] }}</div></div>
										            			</div>

										            			<div class="level level-7"><div class="item">OUT: {{ $job['out'] }}</div></div>
									            			</div>
								            			@endforeach
							            			</div>
							            		@endforeach
					            			</div>
				            			@endforeach
				            			</div>
			            			@endforeach
			            			</div>
		            			@endforeach
		            			</div>
		            		@endforeach
		            		</div>
		            	</div>

		            	<div class="view">
		            		<div class="title">View by Tyre Brand</div>
		            		<div class="level level-0"><div class="item header"><a>New Tyre (NT) <div class="symbol">&#9654;</div></a></div>
		            		@foreach ($tyreBrand['NT'] as $brand => $brandData) 
		            			<div class="level level-1"><div class="item"><a>{{ $brand }} <div class="symbol">&#9654;</div></a></div>

		            			@foreach ($brandData as $pattern => $patternData)
		            				<div class="level level-2"><div class="item"><a>{{ $pattern }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($patternData as $size => $sizeData)
			            				<div class="level level-3"><div class="item"><a>{{ $size }} <div class="symbol">&#9654;</div></a></div>

			            				@foreach ($sizeData as $serialNo => $serialNoData)
				            				<div class="level level-4"><div class="item"><a>{{ $serialNo }} <div class="symbol">&#9654;</div></a></div>

				            					@foreach ($serialNoData as $customer => $customerData)
					            					<div class="level level-5"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>
							            				
							            				@foreach ($customerData as $vehicleType => $vehicleData)
							            					<div class="level level-6"><div class="item"><a>{{ $vehicleType }} <div class="symbol">&#9654;</div></a></div>
									            				
									            				@foreach ($vehicleData as $vehicleNo => $positionData)
									            					<div class="level level-7"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>
											            				
											            				@foreach ($positionData as $position => $jobs)
											            					<div class="level level-8"><div class="item"><a>Pos {{ $position }} <div class="symbol">&#9654;</div></a></div>
													            				
													            				@foreach ($jobs as $index => $job)
													            					<div class="level level-9"><div class="item">{{ $job }}</div></div>
														            			@endforeach
													            			</div>
												            			@endforeach
											            			</div>
										            			@endforeach
									            			</div>
								            			@endforeach
							            			</div>
							            		@endforeach
					            			</div>
				            			@endforeach
				            			</div>
			            			@endforeach
			            			</div>
		            			@endforeach
		            			</div>
		            		@endforeach
		            		</div>


		            		<div class="level level-0"><div class="item header"><a>Stock Retread (STK) <div class="symbol">&#9654;</div></a></div>
		            		@foreach ($tyreBrand['STK'] as $brand => $brandData) 
		            			<div class="level level-1"><div class="item"><a>{{ $brand }} <div class="symbol">&#9654;</div></a></div>

		            			@foreach ($brandData as $pattern => $patternData)
		            				<div class="level level-2"><div class="item"><a>{{ $pattern }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($patternData as $size => $sizeData)
			            				<div class="level level-3"><div class="item"><a>{{ $size }} <div class="symbol">&#9654;</div></a></div>

			            				@foreach ($sizeData as $serialNo => $serialNoData)
				            				<div class="level level-4"><div class="item"><a>{{ $serialNo }} <div class="symbol">&#9654;</div></a></div>

				            					@foreach ($serialNoData as $customer => $customerData)
					            					<div class="level level-5"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>
							            				
							            				@foreach ($customerData as $vehicleType => $vehicleData)
							            					<div class="level level-6"><div class="item"><a>{{ $vehicleType }} <div class="symbol">&#9654;</div></a></div>
									            				
									            				@foreach ($vehicleData as $vehicleNo => $positionData)
									            					<div class="level level-7"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>
											            				
											            				@foreach ($positionData as $position => $jobs)
											            					<div class="level level-8"><div class="item"><a>Pos {{ $position }} <div class="symbol">&#9654;</div></a></div>
													            				
													            				@foreach ($jobs as $index => $job)
													            					<div class="level level-9"><div class="item">{{ $job }}</div></div>
														            			@endforeach
													            			</div>
												            			@endforeach
											            			</div>
										            			@endforeach
									            			</div>
								            			@endforeach
							            			</div>
							            		@endforeach
					            			</div>
				            			@endforeach
				            			</div>
			            			@endforeach
			            			</div>
		            			@endforeach
		            			</div>
		            		@endforeach
		            		</div>


		            		<div class="level level-0"><div class="item header"><a>Customer Own Casing (COC) <div class="symbol">&#9654;</div></a></div>
		            		@foreach ($tyreBrand['COC'] as $brand => $brandData) 
		            			<div class="level level-1"><div class="item"><a>{{ $brand }} <div class="symbol">&#9654;</div></a></div>

		            			@foreach ($brandData as $pattern => $patternData)
		            				<div class="level level-2"><div class="item"><a>{{ $pattern }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($patternData as $size => $sizeData)
			            				<div class="level level-3"><div class="item"><a>{{ $size }} <div class="symbol">&#9654;</div></a></div>

			            				@foreach ($sizeData as $serialNo => $serialNoData)
				            				<div class="level level-4"><div class="item"><a>{{ $serialNo }} <div class="symbol">&#9654;</div></a></div>

				            					@foreach ($serialNoData as $customer => $customerData)
					            					<div class="level level-5"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>
							            				
							            				@foreach ($customerData as $vehicleType => $vehicleData)
							            					<div class="level level-6"><div class="item"><a>{{ $vehicleType }} <div class="symbol">&#9654;</div></a></div>
									            				
									            				@foreach ($vehicleData as $vehicleNo => $positionData)
									            					<div class="level level-7"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>
											            				
											            				@foreach ($positionData as $position => $jobs)
											            					<div class="level level-8"><div class="item"><a>Pos {{ $position }} <div class="symbol">&#9654;</div></a></div>
													            				
													            				@foreach ($jobs as $index => $job)
													            					<div class="level level-9"><div class="item">{{ $job }}</div></div>
														            			@endforeach
													            			</div>
												            			@endforeach
											            			</div>
										            			@endforeach
									            			</div>
								            			@endforeach
							            			</div>
							            		@endforeach
					            			</div>
				            			@endforeach
				            			</div>
			            			@endforeach
			            			</div>
		            			@endforeach
		            			</div>
		            		@endforeach
		            		</div>


		            		<div class="level level-0"><div class="item header"><a>Used Tyre (USED) <div class="symbol">&#9654;</div></a></div>
		            		@foreach ($tyreBrand['USED'] as $brand => $brandData) 
		            			<div class="level level-1"><div class="item"><a>{{ $brand }} <div class="symbol">&#9654;</div></a></div>

		            			@foreach ($brandData as $pattern => $patternData)
		            				<div class="level level-2"><div class="item"><a>{{ $pattern }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($patternData as $size => $sizeData)
			            				<div class="level level-3"><div class="item"><a>{{ $size }} <div class="symbol">&#9654;</div></a></div>

			            				@foreach ($sizeData as $serialNo => $serialNoData)
				            				<div class="level level-4"><div class="item"><a>{{ $serialNo }} <div class="symbol">&#9654;</div></a></div>

				            					@foreach ($serialNoData as $customer => $customerData)
					            					<div class="level level-5"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>
							            				
							            				@foreach ($customerData as $vehicleType => $vehicleData)
							            					<div class="level level-6"><div class="item"><a>{{ $vehicleType }} <div class="symbol">&#9654;</div></a></div>
									            				
									            				@foreach ($vehicleData as $vehicleNo => $positionData)
									            					<div class="level level-7"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>
											            				
											            				@foreach ($positionData as $position => $jobs)
											            					<div class="level level-8"><div class="item"><a>Pos {{ $position }} <div class="symbol">&#9654;</div></a></div>
													            				
													            				@foreach ($jobs as $index => $job)
													            					<div class="level level-9"><div class="item">{{ $job }}</div></div>
														            			@endforeach
													            			</div>
												            			@endforeach
											            			</div>
										            			@endforeach
									            			</div>
								            			@endforeach
							            			</div>
							            		@endforeach
					            			</div>
				            			@endforeach
				            			</div>
			            			@endforeach
			            			</div>
		            			@endforeach
		            			</div>
		            		@endforeach
		            		</div>

		            	</div>

	            	</div>
	        	</div>
	    	</div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<script type="text/javascript">
	$(document).ready(function() {
		
		$('div.level').on('click', function(e) {
			e.stopPropagation();

			$(this).children('div.level').slideToggle(500);
			$(this).children('div.item').children('a').children('.symbol').toggleClass('expand');
		});

		$('div.title').on('click', function(e) {
			e.stopPropagation();

			$(this).siblings('div.level').slideToggle(500);
		});
	});
</script>
@append