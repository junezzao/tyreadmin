@extends('layouts.master')

@section('header_scripts')
<!-- daterange picker -->
<link href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
@append

@section('title')
	@lang('contracts.contract_calculator')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('contracts.contract_calculator')</h1>
      @include('partials.breadcrumb')
    </section>
    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('contracts.calculate'), 'method' => 'POST', 'id' => 'calculate')) !!}
	            			<div class="col-xs-12">
		            			<div class="col-md-6"> 
                                    <div class="form-group has-feedback">
			            				<label class="col-xs-4 control-label required" for="contract">@lang('contracts.form_contract_type')</label>
			            				<div class="col-xs-8">
	   										{!! Form::select( 'contract_type', config("globals.contract_type"), $contractType, array('class' => 'form-control select2', 'placeholder' => trans("contracts.form_placeholder_select_contract_type")) ) !!}
	   										<div class="error">{{ $errors->first('contract_type') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-4 control-label required" for="contract">@lang('contracts.form_contract')</label>
			            				<div class="col-xs-8">
	   										{!! Form::select( 'contract', $contracts, $contractSelectedID, array('class' => 'form-control select2', 'placeholder' =>  trans('contracts.form_placeholder_select_contract')) ) !!}
	   										<div class="error">{{ $errors->first('contract') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-4 control-label required" for="month">@lang('contracts.form_month')</label>
			            				<div class="col-xs-8">
	   										{!! Form::text('month', $month, array('id' => 'month', 'class'=>'form-control datepicker search-col', 'placeholder' => trans('contracts.form_placeholder_select_month'), 'data-col'=>5)) !!}
	   										<div class="error">{{ $errors->first('month') }}</div>
						                </div>
						            </div>
                                </div>					            
				         	</div>

				         	<div class="col-xs-6">
				         		<div class="form-group pull-right">
				         			<div class="col-xs-8">
					               		<button id="btn_calculate" class="btn btn-primary generate-report">@lang('contracts.button_calculate_fee')</button>
					               	</div>
					            </div> <!-- / .form-actions -->
					        </div>
				        {!! Form::close() !!}
	            	</div>
	            	@if($result['status'] == true)
	            	<hr style="border-top: 2px solid #2342AB">
	            	<div class="box-body">
		            	<div class="col-xs-12">
		            		<div class="col-md-6">
				            	<div class="form-group">
					           		<table width="100%">
					           			<tr>
					           				<td><b style="font-size: 20px"><u>@lang('contracts.label_summary')</u></b></td>
					           			</tr>
					           			<tr>
					           				<td width="50%">@lang('contracts.label_total_order')</td>
					           				<td width="50%" align="right">{{ $result['totalOrderCount'] }}</td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_item')</td>
					           				<td align="right">{{ $result['totalOrderItem'] }}</td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_sale')</td>
					           				<td align="right"><b>{{ $result['totalSalesAmount'] }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_listing')</td>
					           				<td align="right"><b>{{ $result['totalListingAmount'] }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_retail')</td>
					           				<td align="right"><b>{{ $result['totalRetailsAmount'] }}</b></td>
					           			</tr>
					           			@if($result['channel'])
					           			<tr>
					           				<td colspan="2"><u><b>{{ $result['channelName'] }}</b></u></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_channel')</td>
					           				<td align="right"><b>{{ $result['totalFee'] }}</b></td>
					           			</tr>
					           			@else
					           			<tr>
					           				<td>@lang('contracts.label_total_inbound')</td>
					           				<td align="right"><b>{{ !empty($result['inbound'])?$result['inbound']:'0' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_inbound_fee')</td>
					           				<td align="right"><b>{{ !empty($result['inbound_fee'])?$result['inbound_fee']:'0.00' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_outbound')</td>
					           				<td align="right"><b>{{ !empty($result['outbound'])?$result['outbound']:'0' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_outbound_fee')</td>
					           				<td align="right"><b>{{ !empty($result['outbound_fee'])?$result['outbound_fee']:'0.00' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_storage')</td>
					           				<td align="right"><b>{{ !empty($result['storage'])?$result['storage']:'0' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_storage_fee')</td>
					           				<td align="right"><b>{{ !empty($result['storage_fee'])?$result['storage_fee']:'0.00' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_return')</td>
					           				<td align="right"><b>{{ !empty($result['return'])?$result['return']:'0' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_return_fee')</td>
					           				<td align="right"><b>{{ !empty($result['return_fee'])?$result['return_fee']:'0.00' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_shipped')</td>
					           				<td align="right"><b>{{ !empty($result['shipped'])?$result['shipped']:'0' }}</b></td>
					           			</tr>
					           			<tr>
					           				<td>@lang('contracts.label_total_shipped_fee')</td>
					           				<td align="right"><b>{{ !empty($result['shipped_fee'])?$result['shipped_fee']:'0.00' }}</b></td>
					           			</tr>
					           			
					           			<tr>
					           				<td>@lang('contracts.label_total_hubwire')</td>
					           				<td align="right"><b>{{ $result['totalFee'] }}</b></td>
					           			</tr>
					           			@endif
					           		</table>
					           		
				            	</div>
		            		</div>
		            		{!! Form::open(['url' => route('contracts.calculate.export'), 'method' => 'POST', 'id' => 'export']) !!}
			            		<div class="col-md-6 pull-right">
	                                {!! Form::hidden('data', json_encode($result['item'])) !!}
	                                {!! Form::hidden('type', json_encode($result['channel'])) !!}
	                                {!! Form::hidden('endDate', json_encode($result['endDate'])) !!}
	                                {!! Form::submit(trans('contracts.button_export'), ['class' => 'btn btn-primary', 'id' => 'btn_export']) !!}
			            		</div>
                            {!! Form::close() !!}
		            	</div>
		            	@if($result['totalOrderCount'] > 0)
		            	<div class="col-xs-12">
		            		<div class="col-md-6">
		            			<div class="form-group">
		            				<table width="100%">
					           			<tr>
					           				<td><b style="font-size: 20px"><u>@lang('contracts.label_contract_detail')</u></b></td>
					           			</tr>
					           			<tr>
					           				<td width="50%">@lang('contracts.label_min_guarantee')</td>
					           				<td width="50%" align="right">{{ $result['guarantee'] }}</td>
					           			</tr>
					           			<tr>
					           				<td colspan="2">{{ $result['min_guarantee'] }}</td>
					           			</tr>
					           			@if(!$result['channel'])
					           			<tr>
					           				<td width="50%">@lang('contracts.label_inbound')</td>
					           				<td width="50%" align="right">{{ $result['inbound_rate'] }}</td>
					           			</tr>
					           			<tr>
					           				<td width="50%">@lang('contracts.label_outbound')</td>
					           				<td width="50%" align="right">{{ $result['outbound_rate'] }}</td>
					           			</tr>
					           			<tr>
					           				<td width="50%">@lang('contracts.label_storage')</td>
					           				<td width="50%" align="right">{{ $result['storage_rate'] }}</td>
					           			</tr>
					           			<tr>
					           				<td width="50%">@lang('contracts.label_return')</td>
					           				<td width="50%" align="right">{{ $result['return_rate'] }}</td>
					           			</tr>
					           			<tr>
					           				<td width="50%">@lang('contracts.label_shipped')</td>
					           				<td width="50%" align="right">{{ $result['shipped_rate'] }}</td>
					           			</tr>
					           			@endif

					           			<tr>
					           				<td><b style="font-size: 20px"><u>@lang('contracts.label_contract_rule')</u></b></td>
					           			</tr>
					           			@foreach($result['rules'] as $rule)
					           			<tr>
					           				<td colspan="2">{{ $rule }}</td>
					           			</tr>
					           			@endforeach
					           		</table>
		            			</div>
		            		</div>
		            	</div>
		            	@endif
	            	</div>
	            	@endif
	            	<div class="overlay">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                    </div>
	            </div>
	        </div>
	    </div>
   	</section>

@stop
@section('footer_scripts')
<script type="text/javascript">
$(document).ready(function(){

	$('.overlay').addClass('hide');

	$('#month').datepicker({
        format :  'MM-yyyy',
        disableDate: true,
        startView: "months", 
        minViewMode: "months",
        autoclose: true,
    });

    $("select[name='contract_type']").change(function(){
    	$("select[name='contract']").find('option').remove();
        var contractOptions = '<option disabled selected>Select Contract</option>';
        if ($(this).find("option:selected").text() == 'Hubwire Fee') { 
            @foreach($contract['hubwire'] as $key => $value)
                contractOptions += '<option value="{{$key}}">';
                contractOptions += "{{ $value }}";
                contractOptions += '</option>';
            @endforeach
        	$("select[name='contract']").append(contractOptions).trigger('change');
        }else{
            @foreach($contract['channel'] as $key => $value)
                contractOptions += '<option value="{{$key}}">';
                contractOptions += "{{ $value }}";
                contractOptions += '</option>';
            @endforeach
        	$("select[name='contract']").append(contractOptions).trigger('change');

        }
	});

    $('#btn_calculate').click(function() {
        $('.overlay').removeClass('hide');
        $('#calculate').submit();
    });

    $('#btn_export').click(function() {
        $('#export').submit();
    });

});


</script>
@append
