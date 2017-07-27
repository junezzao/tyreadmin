@foreach ($data as $brand => $brandData) 
	<div class="level level-1"><div class="item"><a>{{ $brand }} <div class="symbol">&#9654;</div></a></div>

	@foreach ($brandData as $pattern => $patternData)
		<div class="level level-2"><div class="item"><a>{{ $pattern }} <div class="symbol">&#9654;</div></a></div>

		@foreach ($patternData as $size => $sizeData)
			<div class="level level-3"><div class="item"><a>{{ $size }} <div class="symbol">&#9654;</div></a></div>

			@foreach ($sizeData as $serialNo => $serialNoData)
				<div class="level level-4"><div class="item"><a>{{ $serialNo }} <div class="symbol">&#9654;</div></a></div>

				@foreach ($serialNoData as $customer => $customerData)
    				<div class="level level-5"><div class="item"><a>{{ $customer }} <div class="symbol">&#9654;</div></a></div>

    				@foreach ($customerData as $vehicle => $vehicleData)
        				<div class="level level-6"><div class="item"><a>{{ $vehicle }} <div class="symbol">&#9654;</div></a></div>

        				@foreach ($vehicleData as $vehicleNo => $vehicleNoData)
            				<div class="level level-7"><div class="item"><a>{{ $vehicleNo }} <div class="symbol">&#9654;</div></a></div>

            				@foreach ($vehicleNoData as $position => $positionData)
	            				<div class="level level-8"><div class="item"><a>{{ $position }} <div class="symbol">&#9654;</div></a></div>

	            				@foreach ($positionData as $jobsheetDate => $jobsheetDateData)
		            				<div class="level level-9"><div class="item"><a>{{ $jobsheetDate }} <div class="symbol">&#9654;</div></a></div>

		            				@foreach ($jobsheetDateData as $index => $job)
			            				<div class="level level-10"><div class="item">{{ $job }}</div></div>
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
@endforeach