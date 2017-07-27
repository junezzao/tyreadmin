@extends('layouts.master')

@section('title')
	@lang('admin/fulfillment.page_title_create_order')
@stop

@section('header_scripts')
<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}">
<link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.css',env('HTTPS',false)) }}">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
@append

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/fulfillment.content_header_manual_order')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/fulfillment.box_header_manual_order')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('orders.create'), 'role'=>'form', 'method' => 'POST', 'id' => 'manual-order-form')) !!}
	            			@var($old_inputs = Input::old())
	            			{!! Form::hidden('i_count', 0) !!}
	            			<div class="col-xs-12">
		            			<div class="col-xs-12">
		            				<h4 class="row"><u>Order Details:</u></h4>
		            				{{-- <div class="form-group">
			            				{!! Form::label('merchant', trans('admin/fulfillment.manual_order_form_label_merchant'), ['class'=>'col-xs-2 control-label required']) !!}
			            				<div class="col-xs-4 input-group">
	   										{!! Form::select( 'merchant', $merchantList, null, ['class' => 'form-control select2', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_merchant')] ) !!}
	   										<div class="error">{{ $errors->first('merchant') }}</div>
						                </div>
						            </div> --}}

	        						<div class="form-group">
			            				{!! Form::label('channel', trans('admin/fulfillment.manual_order_form_label_channel'), ['class'=>'col-xs-2 control-label required']) !!}
			            				<div class="col-xs-4" style="padding-left: 0; padding-right: 0;">
	   										@if(!$admin->is('channelmanager'))
	   											{!! Form::selectWithAttr( 'channel', $channelList, $channelId, ['class' => 'form-control select2', 'id' => 'channel', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_channel')] ) !!}
	   										@else
	   											{!! Form::selectWithAttr( 'channel', $channelList, $channelId, ['class' => 'form-control select2', 'id' => 'channel'] ) !!}
	   										@endif
	   										<div class="error">{{ $errors->first('channel') }}</div>
						                </div>
						            </div>

			            			<div class="form-group">
			            				{!! Form::label('tp_code', trans('admin/fulfillment.manual_order_form_label_tp_code'), ['class'=>'col-xs-2 control-label']) !!}
			            				<div class="col-xs-4 input-group">
	   										{!! Form::text( 'tp_code', $tpCode, ['class' => 'form-control', 'id' => 'tp_code', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_tp_code')] ) !!}
	   										<div class="error">{{ $errors->first('tp_code') }}</div>
						                </div>
						                <!-- if 11 street / shopify show get order button -->
						                <div class="col-xs-1 11-street shopify"><button id="get-order-btn" class="btn btn-info">Get Order</button></div>
						            </div>
						            <div class="form-group">
			            				{!! Form::label('order_date', trans('admin/fulfillment.manual_order_form_label_order_date'), ['class'=>'col-xs-2 control-label required']) !!}
			            				<div class="col-sm-2 col-xs-4 no-padding">
			            					<div class="input-group date">
				            					<div class="input-group-addon">
							                    	<i class="fa fa-calendar"></i>
							                  	</div>
		   										{!! Form::text( 'order_date', null, ['class' => 'form-control datepicker', 'id' => 'order_date', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_order_date')] ) !!}
	   										</div>
	   										<div class="error">{{ $errors->first('order_date') }}</div>
						                </div>

						                <div class="col-sm-2 col-xs-4 input-group bootstrap-timepicker timepicker">
						                    <div class="input-group-addon">
						                    	<i class="fa fa-clock-o"></i>
						                    </div>
						                    {!! Form::text( 'order_time', null, ['class' => 'form-control timepicker', 'id' => 'order_time'] ) !!}
						                </div>
						            </div>
						        </div>
						        <div class="col-xs-12">
						        	<div class="col-md-4 col-xs-12">
						            	<div class="control-label"><u>Shipping Details</u></div>
						            	<div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('recipient_name', trans('admin/fulfillment.manual_order_form_label_recipient_name'), ['class'=>'control-label required']) !!}
	   										{!! Form::text( 'recipient_name', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_name')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_name') }}</div>
							            </div>
							            <div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('recipient_contact', trans('admin/fulfillment.manual_order_form_label_recipient_contact'), ['class'=>'control-label required']) !!}
	   										{!! Form::text( 'recipient_contact', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_contact')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_contact') }}</div>
							            </div>
							            <div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('recipient_address_1', trans('admin/fulfillment.manual_order_form_label_recipient_address'), ['class' => 'control-label required']) !!}

	   										{!! Form::text( 'recipient_address_1', null, ['class' => 'form-control', 'id' =>'recipient_address_1', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_address_1')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_address_1') }}</div>
	   										{!! Form::text( 'recipient_address_2', null, ['class' => 'form-control', 'id' =>'recipient_address_2', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_address_2')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_address_2') }}</div>
	   										{!! Form::text( 'recipient_address_city', null, ['class' => 'form-control', 'id' => 'recipient_address_city', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_address_city')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_address_city') }}</div>
	   										{!! Form::text( 'recipient_address_state', null, ['class' => 'form-control', 'id' => 'recipient_address_state', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_address_state')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_address_state') }}</div>
	   										{!! Form::text( 'recipient_address_postcode', null, ['class' => 'form-control', 'id' => 'recipient_address_postcode', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_address_postcode')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_address_postcode') }}</div>
	   										{!! Form::text( 'recipient_address_country', null, ['class' => 'form-control', 'id' => 'recipient_address_country', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_recipient_address_country')] ) !!}
	   										<div class="error">{{ $errors->first('recipient_address_country') }}</div>
							            </div>
						            </div>
						            <div class="col-md-4 col-xs-12">
						            	<div class="control-label"><u>Billing Details</u></div>
						            	<div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('customer_name', trans('admin/fulfillment.manual_order_form_label_customer_name'), ['class'=>'control-label required']) !!}
	   										{!! Form::text( 'customer_name', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_name')] ) !!}
	   										<div class="error">{{ $errors->first('customer_name') }}</div>
							            </div>
							            <div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('customer_email', trans('admin/fulfillment.manual_order_form_label_customer_email'), ['class'=>'control-label']) !!}
	   										{!! Form::text( 'customer_email', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_email')] ) !!}
	   										<div class="error">{{ $errors->first('customer_email') }}</div>
							            </div>
							            <div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('customer_contact', trans('admin/fulfillment.manual_order_form_label_customer_contact'), ['class'=>'control-label required']) !!}
	   										{!! Form::text( 'customer_contact', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_contact')] ) !!}
	   										<div class="error">{{ $errors->first('customer_contact') }}</div>
							            </div>
							            <div class="input-group col-xs-12 col-sm-12">
				            				{!! Form::label('customer_address_1', trans('admin/fulfillment.manual_order_form_label_customer_address'), ['class' => 'control-label']) !!}
	   										{!! Form::text( 'customer_address_1', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_address_1')] ) !!}
	   										<div class="error">{{ $errors->first('customer_address_1') }}</div>
	   										{!! Form::text( 'customer_address_2', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_address_2')] ) !!}
	   										<div class="error">{{ $errors->first('customer_address_2') }}</div>
	   										{!! Form::text( 'customer_address_city', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_address_city')] ) !!}
	   										<div class="error">{{ $errors->first('customer_address_city') }}</div>
	   										{!! Form::text( 'customer_address_state', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_address_state')] ) !!}
	   										<div class="error">{{ $errors->first('customer_address_state') }}</div>
	   										{!! Form::text( 'customer_address_postcode', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_address_postcode')] ) !!}
	   										<div class="error">{{ $errors->first('customer_address_postcode') }}</div>
	   										{!! Form::text( 'customer_address_country', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_customer_address_country')] ) !!}
	   										<div class="error">{{ $errors->first('customer_address_country') }}</div>
							            </div>
						            </div>
						            <div class="col-md-4 col-xs-12">
						            	<div class="control-label"><u>Payment Details</u></div>
						            	<div class="row">
				            				<div class="col-xs-5">{!! Form::label('payment_type', trans('admin/fulfillment.manual_order_form_label_payment_type'), ['class'=>'control-label required']) !!}</div>
	   										<div class="col-xs-7">
	   											{!! Form::select( 'payment_type', $paymentTypeList, null, ['class' => 'form-control', 'id' => 'payment-input']) !!}
	   											{{-- {!! Form::text( 'payment_type', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_payment_type')] ) !!} --}}
	   										<div class="error">{{ $errors->first('payment_type') }}</div></div>
							            </div>
							            {!! Form::label('amount_paid', trans('admin/fulfillment.manual_order_form_label_amount_paid'), ['class'=>'control-label required']) !!}
							            <div class="row">
	   										<div class="col-xs-5">{!! Form::select( 'currency', $currencyList, null, ['class' => 'form-control select2', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_currency')] ) !!}</div>
	   										<div class="col-xs-7">{!! Form::number( 'amount_paid', 0, ['class' => 'form-control', 'id' => 'amount_paid', 'min' => '0', 'step' => 'any', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_amount_paid')] ) !!}
	   										<div class="error">{{ $errors->first('currency') }}</div></div>
	   										<div class="col-xs-12 "><p class="help-block">@lang('admin/fulfillment.manual_order_form_help_amount_paid')</p><p class="help-block">@lang('admin/fulfillment.manual_order_form_help_amount_paid_total')</p></div>
							            </div>
							            <br/>
							            <div class="row">
				            				<div class="col-xs-5">{!! Form::label('total_tax', trans('admin/fulfillment.manual_order_form_label_total_tax'), ['class'=>'control-label required']) !!}</div>
	   										<div class="col-xs-7">{!! Form::number( 'total_tax', 0, ['class' => 'form-control', 'min' => '0', 'step' => 'any', 'id' => 'total_tax', 'readonly', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_total_tax')] ) !!}
	   										<div class="error">{{ $errors->first('total_tax') }}</div></div>
	   										<div class="col-xs-12 "><p class="help-block">@lang('admin/fulfillment.manual_order_form_help_total_tax')</p></div>
							            </div>
							            <br/>
							            <div class="row">
				            				<div class="col-xs-5">{!! Form::label('cart_discount', trans('admin/fulfillment.manual_order_form_label_cart_discount'), ['class'=>'control-label required']) !!}</div>
	   										<div class="col-xs-7">{!! Form::number( 'cart_discount', 0, ['class' => 'form-control', 'min' => '0', 'step' => 'any', 'readonly', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_cart_discount')] ) !!}
	   										<div class="error">{{ $errors->first('cart_discount') }}</div></div>
	   										<div class="col-xs-12 "><p class="help-block">@lang('admin/fulfillment.manual_order_form_help_cart_discount')</p></div>
							            </div>
							            <hr/>
							            <div class="row">
				            				<div class="col-xs-5">{!! Form::label('shipping_fee', trans('admin/fulfillment.manual_order_form_label_shipping_fee'), ['class'=>'control-label required']) !!}</div>
	   										<div class="col-xs-7">{!! Form::number( 'shipping_fee', 0, ['class' => 'form-control', 'id' => 'shipping_fee', 'min' => '0', 'step' => 'any', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_shipping_fee')] ) !!}
	   										<div class="error">{{ $errors->first('shipping_fee') }}</div></div>
							            </div>
							            <br/>
							            <div class="row">
							            	<div class="col-xs-5">{!! Form::label('shipping_provider', trans('admin/fulfillment.manual_order_form_label_shipping_provider'), ['class'=>'control-label']) !!}</div>
							            	<div class="col-xs-7">{!! Form::text( 'shipping_provider', null, ['class' => 'form-control', 'id' => 'shipping_provider', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_shipping_provider'), 'readonly' => true] ) !!}</div>
							            	<input type="hidden" name="shipping_provider_cod" id="shipping_provider_cod" value="">
							            </div>
							            <br/>
							            <div class="row 11-street">
				            				<div class="col-xs-5">{!! Form::label('shipping_no', trans('admin/fulfillment.manual_order_form_label_shipping_no'), ['class'=>'control-label required']) !!}</div>
	   										<div class="col-xs-7">{!! Form::text( 'shipping_no', null, ['class' => 'form-control', 'id' => 'shipping_no', 'readonly' => true, 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_shipping_no'), 'readonly' => true] ) !!}
	   										<div class="error">{{ $errors->first('shipping_no') }}</div></div>
							            </div>
							            <div class="row shopify">
				            				<div class="col-xs-5">{!! Form::label('promotion_code', trans('admin/fulfillment.manual_order_form_label_promotion_code'), ['class'=>'control-label required']) !!}</div>
	   										<div class="col-xs-7">{!! Form::text( 'promotion_code', null, ['class' => 'form-control', 'id' => 'promotion_code', 'readonly' => true, 'placeholder' => trans('admin/fulfillment.manual_order_form_label_promotion_code'), 'readonly' => true] ) !!}
	   										<div class="error">{{ $errors->first('promotion_code') }}</div></div>
							            </div>
							            <div id="integrated">
							            	@if(!empty(Input::old('subtotal')))
							            		<input type="hidden" value="{{ $old_inputs['subtotal'] }}" name="subtotal"/>
							            		<input type="hidden" value="{{ $old_inputs['total_discount'] }}" name="total_discount"/>
							            		<input type="hidden" value="{{ $old_inputs['tp_order_code'] }}" name="tp_order_code"/>
							            	@endif
							            </div>
						            </div>
						        </div>
						        <div class="clearfix"></div>
						        <hr/>
						        <div class="col-xs-12">
						        	<div class="form-group">
						        		<div class="col-xs-4">
	   										{!! Form::text('find-sku', null, ['class' => 'form-control col-sm-4', 'id' => 'find-sku', 'placeholder' => trans('admin/fulfillment.manual_order_form_placeholder_find_sku')]) !!}
	   										<div class="error"><span id="find-sku-error"></span></div>
						                </div>
						        		<div class="col-xs-1"><button id="add-item-btn" class="btn btn-info">Add Item</button></div>
						        	</div>
						        	<div>
						        		<table class="table table-striped" id="order-items-table">
							                <tbody>
							                	<tr>
							                		<th style="width: 10px">#</th>
							                		<th style="width: 200px">Hubwire SKU</th>
							                		<th style="width: 200px">Product Name</th>
							                		<th>Retail Price</th>
							                		<th>Listing Price</th>
							                		<th>Sold Price</th>
							                		<th>Discount<br/><span class="small"> (Sold Price - Listing Price)</span></th>
							                		<th>Weighted Cart Discount</th>
							                		<th class="text-center">Quantity</th>
							                		<th class="text-center">Item Tax</th>
							                		<th class="text-center">Third Party Item ID</th>
							                		<th style="width: 40px"></th>
							                	</tr>
												<!--@var($old_inputs = Input::old())-->
								          		@if(!empty($old_inputs['hubwire_sku']))
								          			@for($i = 0; $i < count($old_inputs['hubwire_sku']); $i++)
								          				@if(isset($old_inputs['hubwire_sku'][$i]))
															<tr>
								          						<td>{{ ($i+1) }}</td>
								          						<td>
								          							@if(!empty($old_inputs['ref_type']))
													            		<input type="hidden" value="{{ $old_inputs['ref_type'][$i] }}" name="ref_type[]" />
													            		<input type="hidden" value="{{ $old_inputs['tax_inclusive'][$i] }}" name="tax_inclusive[]" />
													            		<input type="hidden" value="{{ $old_inputs['tax_rate'][$i] }}" name="tax_rate[]" />
													            		<input type="hidden" value="{{ $old_inputs['tax'][$i] }}" name="tax[]" />
													            		<input type="hidden" value="{{ $old_inputs['tp_discount'][$i] }}" name="tp_discount[]" />
													            		<input type="hidden" value="{{ $old_inputs['tp_item_id'][$i] }}" name="tp_item_id[]" />
													            	@endif
													            	@if(isset($old_inputs['channel_sku']))
													            		<input type="hidden" value="{{ $old_inputs['channel_sku'][$i] }}" name="channel_sku[]" />
													            	@endif
													            	@if($old_inputs['hubwire_sku'][$i] == 'undefined' || $old_inputs['hubwire_sku'][$i] == '')
													            	<input type="text" value="{{ $old_inputs['hubwire_sku'][$i] }}" name="hubwire_sku[]" class="hubwire_sku"/>
													            	<div class="error">{{ $errors->first('hubwire_sku.'.$i) }}</div>
													            	@else
								          							<input type="hidden" value="{{ $old_inputs['hubwire_sku'][$i] }}" name="hubwire_sku[]" class="hubwire_sku"/>{{ $old_inputs['hubwire_sku'][$i] }}
								          							@endif
								          						</td>
								          						<td><input type="hidden" value="{{ $old_inputs['product_name'][$i] }}" name="product_name[]" /><span id="product_name_{{ $i }}">{{ $old_inputs['product_name'][$i] }}</span></td>
								          						<td>
								          							<input type="hidden" name="unit_price[]" value="{{ $old_inputs['unit_price'][$i] }}"/><span id="unit_price_{{ $i }}">{{ $old_inputs['unit_price'][$i] }}</span>
								          							<div class="error">{{ $errors->first('unit_price.'.$i) }}</div>
								          						</td>
								          						<td>
								          							<input type="hidden" name="sale_price[]" value="{{ $old_inputs['sale_price'][$i] }}"/><span id="sale_price_{{ $i }}">{{ $old_inputs['sale_price'][$i] }}</span>
								          							<div class="error">{{ $errors->first('sale_price.'.$i) }}</div>
								          						</td>
								          						<td>
								          							<input type="text" placeholder="0.00" name="sold_price[]" value="{{ $old_inputs['sold_price'][$i] }}" class="form-control sold-price"/>
								          							<div class="error">{{ $errors->first('sold_price.'.$i) }}</div>
								          						</td>
								          						<td>
								          							<input type="text" placeholder="0.00" name="discount[]" value="{{ $old_inputs['discount'][$i] }}" class="form-control discount"/>
								          							<div class="error">{{ $errors->first('discount.'.$i) }}</div>
								          						</td>
								          						<td>
								          							<input type="text" placeholder="0.00" name="weighted_discount[]" value="{{ $old_inputs['weighted_discount'][$i] }}" class="form-control weigthed_discount"/>
								          							<div class="error">{{ $errors->first('weigthed_discount.'.$i) }}</div>
								          						</td>
								          						<td>
								          							<input type="hidden" placeholder="1" name="quantity[]" value="{{ $old_inputs['quantity'][$i] }}" class="form-control qty text-center"/>{{ $old_inputs['quantity'][$i] }}
								          						</td>
								          						<td>
								          							<input type="text" name="tax[]" value="{{ $old_inputs['tax'][$i] }}" class="form-control tax"/>
								          							<div class="error">{{ $errors->first('tax.'.$i) }}</div>
								          						</td>
								          						<td>
								          							<input type="text" name="tp_item_id[]" value="{{ $old_inputs['tp_item_id'][$i] }}" class="form-control tp_item_id"/>
								          							<div class="error">{{ $errors->first('tp_item_id.'.$i) }}</div>
								          						</td>
								          						<td><a class="btn remove-item"><i class="fa fa-times"></i></a></td>

								          					</tr>
														@endif
								          			@endfor
								          		@endif
						        			</tbody>
						        		</table>
						        	</div>

					        	</div>

						    </div>
						    {!! Form::submit('Create Order', ['class' => 'btn btn-primary pull-right']) !!}
	            		{!! Form::close() !!}
	            	</div>
	           	</div>
	        </div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
{{-- payment type --}}=

<script type="text/javascript">
jQuery(document).ready(function($){
    $("#payment-input").select2({
        placeholder: "Payment type",
		tokenSeparators: [','],
		tags: true,
    });

	$(window).keydown(function(event){
        if(event.keyCode == 13)
        {
        	if(event.target.id == 'find-sku'){
        		$('#add-item-btn').click();
        	}

        	if($(event.target).attr('class') == 'hubwire_sku'){
        		getItemDetails($(event.target));
        	}
          event.preventDefault();
          return false;
        }
    });

	$('.11-street').hide();
	$('.shopify').hide();
	var i = $('input[name="i_count"]').val();
	$('.timepicker').timepicker({
		minuteStep: 1,
		secondStep: 1,
		showSeconds: true,
        showMeridian: false,
        maxHours: 24,
        showInputs: false,
	});

	$('.datepicker').datepicker({
		setDate: new Date(),
		autoclose: true,
		format: 'yyyy-mm-dd',
	});

	{{-- $('#merchant').change(function(){
		waitingDialog.show('Getting channel list...', {dialogSize: 'sm'});
		$('#channel').find('option').remove().end().append('<option value="">Select Channel</option>');
		$.ajax({
            url: "/admin/channels/merchant/"+$(this).val(),
            type: 'GET',
            success: function(data) {
                if($.isEmptyObject(data.channels)){
                    // show placeholder
                    $('#channel').append('<option>N/A</option>');
                }else{
                    // loop thru response and build new select options
                    var channelOptions = '';
                    $.each(data.channels, function( index, channel ) {
                        channelOptions += '<option value="'+channel.id+'" data-type="'+channel.channel_type_id+'" >';
                        channelOptions += channel.name;
                        channelOptions += '</option>';
                    });
                    $('#channel').append(channelOptions);
                }
            },
            complete: function(){
            	waitingDialog.hide();
            }
        });
	});--}}


	$('#channel').change(function(){
		$("#order-items-table .order-item").empty();
		$('input[type=text]').not('#order_time').val('');
		$('input[type=number]').val(0);
		$('select').not('#channel').val('').trigger('change');
		$('#shipping_provider').val($('#channel option:selected').attr('shipping_provider'));
		$('#shipping_provider_cod').val($('#channel option:selected').attr('shipping_provider_cod'));

		$('.11-street').hide();
		$('.shopify').hide();
		$('label[for="tp_code"]').removeClass('required');
			
		if($('#channel option:selected').attr('data-type') == 10){
			$('#shipping_provider').val($('#channel option:selected').attr('shipping_provider'));
			$('.11-street').show();
			$('label[for="tp_code"]').addClass('required');
		}else if($('#channel option:selected').attr('data-type') == 6){
			$('#shipping_provider').val($('#channel option:selected').attr('shipping_provider'));
			$('.shopify').show();
			$('label[for="tp_code"]').addClass('required');
		}
	});

    $("#add-item-btn").click(function(){
    	var hw_sku = $('#find-sku').val();
    	$('#find-sku-error').text('');
		var response;

    	if(hw_sku == ''){
    		alert('Please enter a valid Hubwire SKU.');
    	}else{
    		waitingDialog.show('Retrieving product details...', {dialogSize: 'sm'});
    		$.ajax({
				method: "GET",
			  	url: "/products/get_product_details",
			  	data: { merchantId: $('#merchant').val(), channelId: $('#channel').val() , hubwireSku: hw_sku },
			  	success: function(data){
			  		if(data.success == true){
			  			i++;
						$("#order-items-table").append('<tr class="order-item"><td>'+i+'</td><td><input type="hidden" value="'+data['channel_sku_id']+'" name="channel_sku[]" /><input type="hidden" value="'+data['hubwire_sku']+'" name="hubwire_sku[]" />'+data['hubwire_sku']+'</td><td><input type="hidden" value="'+data['name']+'" name="product_name[]" />'+data['name']+'</td><td><input type="hidden" name="unit_price[]" value="'+data['unit_price']+'"/>'+data['unit_price']+'</td><td><input type="hidden" name="sale_price[]" value="'+data['sale_price']+'"/>'+data['sale_price']+'</td><td><input type="text" placeholder="0.00" value="0.00" name="sold_price[]" class="form-control sold-price"/></td><td><input type="text" placeholder="0.00" value="0.00" name="discount[]" class="form-control discount"/></td><td><input type="text" name="weighted_discount[]" placeholder="0.00" value="0.00" class="form-control weighted_discount"/></td><td class="text-center"><input type="hidden" name="quantity[]" value="1" class="form-control qty"/>1</td><td><input type="text" placeholder="0.00" value="0.00" name="tax[]" class="form-control tax"/></td><td><input type="text" placeholder="TP item ID" name="tp_item_id[]" class="form-control tp_item_id"/></td><td><a class="btn remove-item"><i class="fa fa-times"></i></a></td></tr>');
					}else{
			  			response = data;
			  			$('#find-sku-error').text(data.error);
			  		}
			  	},
			  	complete: function(){
			  		waitingDialog.hide();
			  		$('#find-sku').val('');
			  	}

			});
    		// TODO: if item currently exist in table, update it
    	}
    	return false;
    });

    $("#order-items-table").on('click','.remove-item',function(){
        $(this).parent().parent().remove();
        i--;
    });

    // TODO: on change discount and sold price values, update the discount and amount paid accordingly

    $('#get-order-btn').click(function(e){
    	e.preventDefault();
    	var orderCode = parseInt($('#tp_code').val());
    	var channel = $('#channel option:selected').val();
    	var tax = 0;

    	if(i > 0){
    		$('.remove-item').each(function(){
	    		$(this).parent().parent().remove();
	        	i--;
	    	});
    	}

    	if(isNaN(orderCode)) {
 			alert('Please enter a valid order id. The order id should only contain integers.');
 			return false;
 		}
 		waitingDialog.show('Retrieving order details...', {dialogSize: 'sm'});
 		$.ajax({
 			'method': 'GET',
 			'url': '/channels/'+channel+'/getorder/'+orderCode,
 			dataType: 'json',
 			success: function(response){
 				/*{"response":{"201607240548349":{"success":true,"order":{"subtotal":59.85,"total":"59.85","shipping_fee":"0","total_discount":59.85,"cart_discount":0,"tp_order_id":"201607240548349","tp_order_code":"201607240548349","tp_order_date":"2016-07-24 01:06:34","tp_source":"auto","status":21,"paid_status":true,"paid_date":"2016-07-24 01:08:33","cancelled_status":false,"payment_type":"unknown","shipping_recipient":"NORHASNIZAH BT HASAN","shipping_phone":"0193464640","shipping_street_1":"42700 Banting, Selangor","shipping_street_2":"MAJLIS DAERAH KUALA LANGAT PERSIARAN MAJLIS JLN SULTAN ALAM SHAH","shipping_postcode":"42700","shipping_city":"","shipping_state":"","shipping_country":"","shipping_provider":"Skynet","consignment_no":"","currency":"MYR","forex_rate":1,"tp_extra":"{\"created_at\":\"2016-07-24 01:06:34\",\"shipping_no\":\"8003397884\",\"sale_status\":\"901\",\"member_id\":\"52179561\",\"sale_shipping_type\":\"03\",\"amount_paid\":\"59.85\",\"shipping_remarks\":{},\"clearance_email\":{},\"order_amount\":\"119.70\",\"mail_seq\":\"17032\",\"confirm_date\":\"2016-07-26 01:04:58\"}"},"member":{"member_name":"Niza","member_type":1,"member_email":"niz******@yahoo.com.my","member_mobile":"0193464640"},"items":[{"ref_type":"ChannelSKU","sold_price":19.95,"tax_inclusive":1,"tax_rate":0.06,"tax":1.13,"original_quantity":1,"quantity":1,"discount":19.95,"tp_discount":0,"weighted_cart_discount":0,"tp_item_id":"1","channel_sku_ref_id":"1662636419","product_name":"Modernform 50% Clearance Sales"},{"ref_type":"ChannelSKU","sold_price":19.95,"tax_inclusive":1,"tax_rate":0.06,"tax":1.13,"original_quantity":1,"quantity":1,"discount":19.95,"tp_discount":0,"weighted_cart_discount":0,"tp_item_id":"1","channel_sku_ref_id":"1662636419","product_name":"Modernform 50% Clearance Sales"},{"ref_type":"ChannelSKU","sold_price":19.95,"tax_inclusive":1,"tax_rate":0.06,"tax":1.13,"original_quantity":1,"quantity":1,"discount":19.95,"tp_discount":0,"weighted_cart_discount":0,"tp_item_id":"1","channel_sku_ref_id":"1662636419","product_name":"Modernform 50% Clearance Sales"}]}}}*/
 				if(response.success != undefined && response.success == false){
 					alert('Order not found. Make sure the Order Reference Number is valid.');
 					return false;
 				}else{
	 				var order = response.order;


	 				if (order.cancelled_status || order.status>=24 || order.status<=11) {
	 					alert("This order has been cancelled or shipped. Due to limitations on 11street's API, the order details retrieved might not be accurate. Please contact 11street directly for inquires.");
	 				}
	 				else {
	 					var date = new Date(order.tp_order_date);
		 				$('#order_no').val(order.tp_order_id);
		 				$('#order_date').datepicker('setDate', new Date(order.tp_order_date));
		 				$('#order_time').timepicker('setTime', date.getHours()+':'+date.getMinutes()+':'+date.getSeconds());

					    if ($("#payment-input").find("option[value='" + order.payment_type + "']").length) {
					        $("#payment").val(order.payment_type).trigger("change");
					    } else {
					        var newOption = new Option(order.payment_type, order.payment_type, true, true);
					        $("#payment-input").append(newOption).trigger('change');
					    }

		 				$('#recipient_name').val(order.shipping_recipient);
		 				$('#recipient_contact').val(order.shipping_phone);
		 				$('#recipient_address_1').val(order.shipping_street_1);
		 				$('#recipient_address_2').val(order.shipping_street_2);
		 				$('#recipient_address_city').val(order.shipping_city);
		 				$('#recipient_address_state').val(order.shipping_state);
		 				$('#recipient_address_postcode').val(order.shipping_postcode);
		 				$('#recipient_address_country').val(order.shipping_country);

		 				$('#customer_name').val(response.member.member_name);
		 				$('#customer_email').val(response.member.member_email);
		 				$('#customer_contact').val(response.member.member_mobile);

						$('#amount_paid').val(order.total);
		 				$('#cart_discount').val(order.cart_discount);
		 				$('select[name=currency]').val(order.currency).trigger('change');
		 				$('#total_tax').val(order.tax);
		 				$('#shipping_provider').val(order.shipping_provider);
		 				$('#shipping_fee').val(order.shipping_fee);
		 				var tp_extra = $.parseJSON(order.tp_extra);
		 				$('#shipping_no').val(tp_extra.shipping_no);
		 				$('#promotion_code').val(tp_extra.discount_codes);

		 				//additional fields
		 				$('#integrated').append('<input type="hidden" value="'+order.subtotal+'" name="subtotal"/><input type="hidden" value="'+order.total_discount+'" name="total_discount"/><input type="hidden" value="'+order.tp_order_code+'" name="tp_order_code"/>');

		 				//items
		 				if (response.items !== undefined) {
		 					//console.log(response.items);
			 				$.each(response.items, function(j, item){
			 					i++;
			 					var sku = $.parseJSON(item.sku);
			 					if(sku.hubwire_sku == undefined)
									$('#order-items-table').append('<tr class="order-item"><td>'+i+'</td><td><input type="hidden" value="'+sku.channel_sku_id+'" name="channel_sku[]" /><input type="hidden" value="'+item.ref_type+'" name="ref_type[]" /><input type="hidden" value="'+item.tax_inclusive+'" name="tax_inclusive[]" /><input type="hidden" value="'+item.tax_rate+'" name="tax_rate[]" /><input type="hidden" value="'+item.tp_discount+'" name="tp_discount[]" /><input type="text" value="'+sku.hubwire_sku+'" name="hubwire_sku[]" class="hubwire_sku" /></td><td><input type="hidden" value="'+item.product_name+'" name="product_name[]"/><span id="product_name_'+i+'">'+item.product_name+'</span></td><td><input type="hidden" name="unit_price[]" value="'+sku.unit_price+'" id="unit_price_'+i+'"/><span id="unit_price_'+i+'">'+sku.unit_price+'</span></td><td><input type="hidden" name="sale_price[]" value="'+sku.sale_price+'" id="sale_price_'+i+'"/><span id="sale_price_'+i+'">'+sku.sale_price+'</span></td><td><input type="text" placeholder="0.00" value="'+((item.tax_inclusive == false) ?  parseFloat(item.sold_price+item.tax).toFixed(2) : item.sold_price)+'" name="sold_price[]" class="form-control sold-price"/></td><td><input type="text" placeholder="0.00" value="'+item.discount+'" name="discount[]" class="form-control discount"/></td><td><input type="text" name="weighted_discount[]" placeholder="0.00" value="'+item.weighted_cart_discount+'" class="form-control weighted_discount"/></td><td class="text-center"><input type="hidden" name="quantity[]" value="'+item.quantity+'" class="form-control qty"/>'+item.quantity+'</td><td><input type="text" placeholder="0.00" value="'+parseFloat(item.tax).toFixed(2)+'" name="tax[]" class="form-control tax"/></td><td><input type="text" placeholder="TP item ID" name="tp_item_id[]" class="form-control tp_item_id" value="'+item.tp_item_id+'"/></td><td><a class="btn remove-item"><i class="fa fa-times"></i></a></td></tr>');
								else
									$('#order-items-table').append('<tr class="order-item"><td>'+i+'</td><td><input type="hidden" value="'+sku.channel_sku_id+'" name="channel_sku[]" /><input type="hidden" value="'+item.ref_type+'" name="ref_type[]" /><input type="hidden" value="'+item.tax_inclusive+'" name="tax_inclusive[]" /><input type="hidden" value="'+item.tax_rate+'" name="tax_rate[]" /><input type="hidden" value="'+item.tp_discount+'" name="tp_discount[]" /><input type="text" value="'+sku.hubwire_sku+'" name="hubwire_sku[]" class="hubwire_sku" /></td><td><input type="hidden" value="'+item.product_name+'" name="product_name[]"/><span id="product_name_'+i+'">'+item.product_name+'</span></td><td><input type="hidden" name="unit_price[]" value="'+sku.unit_price+'" id="unit_price_'+i+'"/><span id="unit_price_'+i+'">'+sku.unit_price+'</span></td><td><input type="hidden" name="sale_price[]" value="'+sku.sale_price+'" id="sale_price_'+i+'"/><span id="sale_price_'+i+'">'+sku.sale_price+'</span></td><td><input type="text" placeholder="0.00" value="'+((item.tax_inclusive == false) ?  parseFloat(item.sold_price+item.tax).toFixed(2) : item.sold_price)+'" name="sold_price[]" class="form-control sold-price"/></td><td><input type="text" placeholder="0.00" value="'+item.discount+'" name="discount[]" class="form-control discount"/></td><td><input type="text" name="weighted_discount[]" placeholder="0.00" value="'+item.weighted_cart_discount+'" class="form-control weighted_discount"/></td><td class="text-center"><input type="hidden" name="quantity[]" value="'+item.quantity+'" class="form-control qty"/>'+item.quantity+'</td><td><input type="text" placeholder="0.00" value="'+parseFloat(item.tax).toFixed(2)+'" name="tax[]" class="form-control tax"/></td><td><input type="text" placeholder="TP item ID" name="tp_item_id[]" class="form-control tp_item_id" value="'+item.tp_item_id+'"/></td><td><a class="btn remove-item"><i class="fa fa-times"></i></a></td></tr>');
			 				});
		 				}


		 				$('.tax').each(function(){
							tax = tax + (parseFloat($(this).val()) != '' ? parseFloat($(this).val()) : 0);
						});
						$('#total_tax').val(tax.toFixed(2));
	 				}
				}
 			},
 			complete: function() {
 				waitingDialog.hide();
 			}
 		});

    });

	@if(!is_null($channelId))
		$('#channel').val({{ $channelId }}).trigger('change');
		@if(!is_null($tpCode))
			$('#tp_code').val('{{ $tpCode }}');
		@endif
	@endif

    $('#manual-order-form').submit(function (event) {
    	var error = false;
    	var channelTypeId = $('#channel option:selected').attr('data-type');

    	// shopify must have all TP item id filled in
    	if(channelTypeId == 6) {
    		var check
	    	$('.tp_item_id').each(function(){
				if($(this).val().length <= 0) {
					error = true;
					alert('Third Party Item ID must be filled in for Shopify order.');
					return false;
				}
			});
		}
    	
        // amount paid = total sold price + shipping fee - cart discount
	    var amountPaid = parseFloat($.trim($('input[name=amount_paid').val()));
	    var sumPrice = 0;
	    var shippingFee = parseFloat($('#shipping_fee').val());
	    var cartDiscount = parseFloat($('#cart_discount').val());
	    var tax = parseFloat($('#total_tax').val());
	    
	    $('input[name="i_count"]').val(i);

	    $('.sold-price').each(function(){
			sumPrice += parseFloat($(this).val());
		});

		if ((sumPrice + shippingFee).toFixed(2) != amountPaid.toFixed(2)) {
	        alert('Amount paid does not tally with total sold price, tax and shipping fee. Please check that the numbers entered are correct.');
	        error = true;
	    }

	    //if(checkTaxSum() != 'passed'){
	    //	error = true;
	    //}
	    var sumTax = 0;
	    $('.tax').each(function(){
			sumTax = sumTax + (parseFloat($(this).val()) != '' ? parseFloat($(this).val()) : 0);
			$('#total_tax').val(sumTax);
		});

	    var sumDiscounts = 0;
	    $('.weighted_discount').each(function(){
			sumDiscounts = sumDiscounts + (parseFloat($(this).val()) != '' ? parseFloat($(this).val()) : 0);
			$('#cart_discount').val(sumDiscounts);
		});
	    //if(checkDiscountSum() != 'passed'){
	    //	error = true;
	    //}

	    if(error == false) {
	    	$('#manual-order-form').append('<input type="hidden" value="' + channelTypeId + '" name="channel_type" />');
 	    	return true;
	    }

	    //console.log('sumDiscount: '+sumDiscounts+ '  cart_discount: '+ $('#cart_discount').val() + '  sumTax: '+sumTax+ '   total_tax: '+$('#total_tax').val());
	    event.preventDefault();
	});

    function checkTaxSum() {
    	var totalTax = (parseFloat($('#total_tax').val()) != '' ? parseFloat($('#total_tax').val()) : 0);
    	var sumTax = 0;

    	$('.tax').each(function(){
			sumTax = sumTax + (parseFloat($(this).val()) != '' ? parseFloat($(this).val()) : 0);
		});
		if (sumTax != totalTax) {
	        alert('Sum of item tax values do not match with total tax amount.');
	        return false;
	    }
	    return 'passed';
    }

	function checkDiscountSum() {
	    var totalDiscount = (parseFloat($('#cart_discount').val()) != '' ? parseFloat($('#cart_discount').val()) : 0);
	    var sumDiscounts = 0;

	    $('.weighted_discount').each(function(){
			sumDiscounts = sumDiscounts + (parseFloat($(this).val()) != '' ? parseFloat($(this).val()) : 0);
		});

		if (sumDiscounts != totalDiscount) {
	        alert('Sum of weighted cart discount values do not match with total cart discount amount.');
	        return false;
	    }

	    return 'passed';
	}

	function getItemDetails(field){
		var hw_sku = $(field).val();
		$('#find-sku-error').text('');
		var item_seq = $(field).parent().parent().index();

    	if(hw_sku == ''){
    		alert('Please enter a valid Hubwire SKU.');
    	}else{
    		waitingDialog.show('Retrieving product details...', {dialogSize: 'sm'});
    		$.ajax({
				method: "GET",
			  	url: "/products/get_product_details",
			  	data: { merchantId: $('#merchant').val(), channelId: $('#channel').val() , hubwireSku: hw_sku },
			  	success: function(data){
			  		var row = $('table#order-items-table tr:eq('+item_seq+')');
			  		if(data.success == true){
			  			row.find('input[name="channel_sku[]"]').val(data.channel_sku_id);
			  			row.find('input[name="product_name[]"]').val(data.name);
			  			row.find('span#product_name_'+item_seq).text(data.name);
			  			row.find('input[name="unit_price[]"]').val(data.unit_price);
			  			row.find('span#unit_price_'+item_seq).text(data.unit_price);
			  			row.find('input[name="sale_price[]"]').val(data.sale_price);
			  			row.find('span#sale_price_'+item_seq).text(data.sale_price);

			  			row.find('input[name="hubwire_sku[]"]').next('.error').text('');
			  			row.find('span#unit_price_'+item_seq).next('.error').text('');
			  			row.find('span#sale_price_'+item_seq).next('.error').text('');
			  		}else{
			  			row.find('input[name="hubwire_sku[]"]').val('undefined');
			  			row.find('input[name="product_name[]"]').val('undefined');
			  			row.find('span#product_name_'+item_seq).text('undefined');
			  			row.find('input[name="unit_price[]"]').val('undefined');
			  			row.find('span#unit_price_'+item_seq).text('undefined');
			  			row.find('input[name="sale_price[]"]').val('undefined');
			  			row.find('span#sale_price_'+item_seq).text('undefined');

			  			$('#find-sku-error').text(data.error);
			  		}
			  	},
			  	complete: function(){
			  		waitingDialog.hide();
			  	}

			});
    	}
    	return false;
	}
});
</script>
@append
