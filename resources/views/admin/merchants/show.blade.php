@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_merchant_view')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.content_header_merchants')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_merchant_view')</h3>
	            	</div>
	            	<div class="box-body">
	            		<?php
	            			$currencies = (empty($merchant->forex_rate))?0:json_decode($merchant->forex_rate);
							//dd($currencies);
	            		?>
	            		  <div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 form-control-label']) !!}
							    <div class="col-sm-8">
							      {!! html_entity_decode($merchant->name) !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('slug','Slug',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $merchant->slug !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							     {!! nl2br($merchant->address) !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('contact','Contact No.',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $merchant->contact !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('email','Email',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $merchant->email !!}
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('logo','Logo',['class'=>'col-sm-4 form-control-label 	']) !!}
							    <div class="col-sm-8">
							      	<img src="{!! $merchant->logo_url!==''?$merchant->logo_url:'http://placehold.it/350x150' !!}" class="img-thumbnail">
								</div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('gst_reg_no','GST Registration Number',['class'=>'col-sm-4 form-control-label']) !!}
							    <div class="col-sm-8">
							      {!! $merchant->gst_reg_no !!}
							    </div>
							  </div>

							  <div class="form-group row hidden">
							    {!! Form::label('self_invoicing','Self-Invoicing',['class'=>'col-sm-4 form-control-label']) !!}
							    <div class="col-sm-8">
							      {!! $merchant->self_invoicing?'Yes':'No' !!}
							    </div>
							  </div>
						</div>
						<div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('code','Accounting Code',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! !empty($merchant->code)?$merchant->code:'' !!}
							      <span class="text-danger">{{ $errors -> first('code')}}</span>
							    </div>
							  </div>
							  
							  <div class="form-group row">
							    {!! Form::label('ae','Account Manager',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! !empty($merchant->ae()->first()->first_name)?$merchant->ae()->first()->first_name : ''!!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('timezone','Default Timezone',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! !empty($merchant->timezone)?$timezones[$merchant->timezone]:'' !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('currency','Default Currency',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							     {!! ucwords(!empty($merchant->currency)?$currencies_arr[$merchant->currency]:'') !!}
							    </div>
							  </div>
							  <fieldset id="currencies-row">
							  <div class="form-group row">
							    {!! Form::label('supported_currencies','Supported Currencies',['class'=>'col-sm-4 form-control-label']) !!}
							    <?php
							     	$i=0;
							     	if(!empty($currencies)):
							     	foreach($currencies as $currency):
							     		if($i>0) echo '<div class="col-sm-4"></div>';
							    ?>
								<div class="col-sm-5">
								    {!! (!empty($currencies_arr))?$currencies_arr[$currency->currency]:'' !!}
							    </div>
							    <div class="col-sm-3 text-left">
							    	{!! (!empty($currency->rate))?$currency->rate:'' !!}
							    </div>
								<?php
										$i++;
									endforeach;
									endif;
								?>
							  </fieldset>

						</div>
						 <div class="col-sm-12">
								<div class="col-sm-12">
									<div class="form-group row pull-right">
										@if($user->can('edit.merchant'))
                                    		<a href="{{route('admin.merchants.edit',$merchant->slug)}}">
												<button type="button" class="btn btn-default">Update Merchant</button>
											</a>
										@endif
										<!--<button type="button" onclick="javascript:history.back();" class="btn btn-default">Back</button>-->
									</div>
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
jQuery(document).ready(function(){

});
</script>
@append