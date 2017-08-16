@extends('layouts.master')

@section('title')
	@lang('titles.reports')
@stop

@section('content')
    <section class="content">
	    <div class="row report">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              	</div>
	            	<div class="box-body">
	            		@if($no_access == true)
	            		<div class="limited-access-alert col-xs-12">
	              			Upgrade your subscription plan to access reports below!
	              		</div>
	              		@endif

	            		<div class="col-xs-12">
	            			<div class="col-md-6 col-xs-12">
	            				<div class="card">
		            				<div class="title title-1">Truck Tyre Cost</div>
		            				<div class="setting">
			            				<p>
			            					<b>This analysis will check:</b><br/>
			            					Total tyre cost of the trucks:<br/>
			            					Sort by: {!! Form::select('sort', ['desc' => 'Highest to Lowest', 'asc' => 'Lowest to Highest'], null) !!}<br/>
			            					Show only top {!! Form::text( 'limit', '10', ['style'=>'width:45px; text-align:right'] ) !!} records
			            				</p>
			            				<div class="footer align-right @if($no_access == true) hide @endif">
		                                	<a href="javascript:truckTyreCost();">Generate</a>
		                                </div>
		                            </div>
	            				</div>
	            			</div>
	            			<div class="col-md-6 col-xs-12">
	            				<div class="card">
		            				<div class="title title-2">Truck Service Record</div>
		            				<div class="setting">
			            				<p>
			            					<b>This analysis will check:</b><br/>
			            					Service record of the trucks
			            				</p>
			            				
			            				<div class="footer align-right @if($no_access == true) hide @endif">
		                                	<a href="{{ route('reports.truckServiceRecord') }}">Generate</a>
		                                </div>
		                            </div>
	            				</div>
	            			</div>
	            		</div>

	            		<div class="col-xs-12">
	            			<div class="col-md-6 col-xs-12">
	            				<div class="card">
		            				<div class="title title-3">Serial No. Analysis</div>
		            				<div class="setting">
			            				<p>
			            					<b>This analysis will check:</b><br/>
			            					1. Serial No. column with missing entry.<br/>
			            					2. Serial No. being repeated. (Being fitted twice without removing)
			            				</p>
			            				<div class="footer align-right @if($no_access == true) hide @endif">
		                                	<a href="{{ route('reports.serialNoAnalysis') }}">Generate</a>
		                                </div>
		                            </div>
	            				</div>
	            			</div>
	            			<div class="col-md-6 col-xs-12">
	            				<div class="card">
		            				<div class="title title-4">Odometer Analysis</div>
		            				<div class="setting">
			            				<p>
			            					<b>This analysis will check:</b><br/>
			            					1. Odometer column with missing value.
			            				</p>
			            				<div class="checkbox" style="margin:-15px 0px -5px 50px">
			            					<label>
			            						<input id="odometer-check" type="checkbox" checked>Check trailer odometer<br/>(if unselected, report will not check if trailer service only)
			            					</label>
			            				</div>
			            				<p>2. Latter odometer is less than previous record.</p>
			            				
			            				<div class="footer align-right @if($no_access == true) hide @endif">
		                                	<a href="javascript:odometerAnalysis();">Generate</a>
		                                </div>
		                            </div>
	            				</div>
	            			</div>
	            		</div>

	            		<div class="col-xs-12">
	            			<div class="col-md-6 col-xs-12">
	            				<div class="card">
		            				<div class="title title-5">Tyre Removal Record</div>
		            				<div class="setting">
			            				<p>
			            					<b>This analysis will check:</b><br/>
			            					1. Only Tyre In/Out Record<br/>(Entry with only Tyre In but no Tyre Out, vice versa)<br/>
			            					2. Tyre In/Out Conflict<br/>(Removed tyre info does not match with previous fitting record)
			            				</p>
			            				<div class="footer align-right @if($no_access == true) hide @endif">
		                                	<a href="{{ route('reports.tyreRemovalRecord') }}">Generate</a>
		                                </div>
		                            </div>
	            				</div>
	            			</div>
	            			<div class="col-md-6 col-xs-12">
	            				<div class="card">
		            				<div class="title title-6">Tyre Removal Mileage</div>
		            				<div class="setting">
			            				<p>
			            					<b>This analysis will check:</b><br/>
			            					Tyre Removed Mileage by Truck.<br/>
			            					The last remove Tyre Out Reason <b>MUST BE "Worn Out"</b>.<br/>
			            				</p>
			            				<p>
			            					Please note: Multi-life Tyre is traced by the following criteria:<br/>
			            					1. Tyre In Brand<br/>
			            					2. Tyre In Pattern<br/>
			            					3. Tyre In Serial No<br/>
			            				</p>
			            				
			            				<div class="footer align-right @if($no_access == true) hide @endif">
		                                	<a href="{{ route('reports.tyreRemovalMileage') }}">Generate</a>
		                                </div>
		                            </div>
	            				</div>
	            			</div>
	            		</div>
	            	</div>
	        	</div>
	    	</div>
	    </div>
   	</section>
@stop

@include('includes.datatables')
@section('footer_scripts')

<script type="text/javascript">
	$(document).ready(function() {
		$('.title').on('click', function() {
			// $(this).siblings('.setting').slideToggle(500);
		});

		$('input[name="limit"]').on('click', function() {
			$(this).select();
		});
	});

	function odometerAnalysis() {
		window.location.href = '{{ route('reports.odometerAnalysis') }}' + '?check_trailer=' + ($('#odometer-check').is(":checked") ? 'Y' : 'N');
	}

	function truckTyreCost() {
		var limit = isNaN($('input[name="limit"]').val()) ? 10 : Math.abs(parseInt($('input[name="limit"]').val()));
		window.location.href = '{{ route('reports.truckTyreCost') }}' + '?sort=' + $('select[name="sort"]').val() + '&limit=' + limit;
	}
</script>
@append