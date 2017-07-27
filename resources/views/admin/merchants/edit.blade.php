@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_merchant_update')
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
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_merchant_edit')</h3>
	            	</div>
	            	<div class="box-body">
	            		<?php
	            			$forex = array();
	            			$forex = json_decode($merchant->forex_rate);
	            		?>

	            		{!! Form::model($merchant,['route' => ['admin.merchants.update', $merchant->slug], 'method'=>'put', 'id'=>'form', 'files'=>true ]) !!}
	            		  <div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('name',html_entity_decode($merchant->name),['class'=>'form-control','placeholder'=>'Company Name']) !!}
							      <span class="text-danger">{{ $errors -> first('name')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('slug','Slug',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('slug',null,['class'=>'text-lowercase form-control','placeholder'=>'Preferred Short Name']) !!}
							      <span class="text-danger">{{ $errors -> first('slug')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4']) !!}
							    <div class="col-sm-8">
							     {!! Form::textarea('address',null,['class'=>'form-control','placeholder'=>'Company Address']) !!}
							     <span class="text-danger">{{ $errors -> first('address')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('contact','Contact No.',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('contact',null,['class'=>'form-control','placeholder'=>'Support Contact Number']) !!}
							      <span class="text-danger">{{ $errors -> first('contact')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('email','Email',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::email('email',null,['class'=>'form-control','placeholder'=>'Support Email']) !!}
							      <span class="text-danger">{{ $errors -> first('email')}}</span>
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('logo','Logo',['class'=>'col-sm-4']) !!}
							    <div class="col-sm-8">
							    	<img src="{!! $merchant->logo_url!==''?$merchant->logo_url:'http://placehold.it/350x150' !!}" class="img-thumbnail">
							      	  {!! Form::hidden('logo_url', $merchant->logo_url) !!}
									  {!! Form::file('logo',null,['class'=>'form-control']) !!}
									  <span class="file-custom"></span>
									  <span class="text-danger">{{ $errors -> first('logo')}}</span>

							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('gst_reg_no','GST Registration Number',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('gst_reg_no',null,['class'=>'form-control','placeholder'=>'GST Registration Number']) !!}
							      <span class="text-danger">{{ $errors -> first('gst_reg_no')}}</span>
							    </div>
							  </div>

							  <div class="form-group row hidden">
							    {!! Form::label('self_invoicing','Self-Invoicing',['class'=>'col-sm-4']) !!}
							    <div class="col-sm-8">
							      <div class="checkbox">
							        <label>
							          <input type="hidden" name="self_invoicing" value="0">
							          {!! Form::checkbox('self_invoicing',1) !!} Check this if you would like to issue tax invoice under your company name
							        </label>
							      </div>
							    </div>
							  </div>
						</div>
						<div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('code','Accounting Code',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('code', null,['class'=>'form-control','placeholder'=>'Accounting Code']) !!}
							      <span class="text-danger">{{ $errors -> first('code')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('ae','Account Manager',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::select('ae',$aes, null,['class'=>'form-control select2','placeholder'=>'Select Account Manager...']) !!}
							      <span class="text-danger">{{ $errors -> first('ae')}}</span>
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('timezone','Default Timezone',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::select('timezone', $timezones, null, ['class'=>'form-control select2','placeholder' => 'Select Default Timezone...']) !!}
							      <span class="text-danger">{{ $errors -> first('timezone')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('currency','Default Currency',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							     <?php echo Form::select('currency', $currencies_arr, null, ['class'=>'form-control select2','placeholder' => 'Select Default Currency...']); ?>
							     <span class="text-danger">{{ $errors -> first('currency')}}</span>
							    </div>
							  </div>
							  <fieldset id="currencies-row">
							   <div class="form-group row">
							    {!! Form::label('currencies0','Supported Currencies',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-5">
							     <?php echo Form::select('currencies[0]', $currencies_arr, !empty($forex[0]->currency)?$forex[0]->currency:null, ['class'=>'form-control select2','placeholder' => 'Supported Currency&hellip;']); ?>
							     <span class="text-danger">{{ $errors -> first('currencies.0')}}</span>
							    </div>
							    <div class="col-sm-3">
							     <?php echo Form::text('rate[0]',  !empty($forex[0]->rate)?$forex[0]->rate:null, ['class'=>'form-control','placeholder' => 'Rate']); ?>
							     <span class="text-danger">{{ $errors -> first('rate.0')}}</span>
							    </div>
							   </div>

							 <?php
							 	if(!empty(old('currencies')) && count(old('currencies'))-1 > 0):
							 		for($i=1;$i<count(old('currencies'));$i++):
							 ?>
							 	<div class="form-group row">
							 	<div class="col-sm-4"></div>
							    <div class="col-sm-5">
							     <?php echo Form::select('currencies['.$i.']', $currencies_arr, null, ['class'=>'form-control select2','placeholder' => 'Supported Currency&hellip;']); ?>
							     <span class="text-danger">{{ $errors -> first('currencies.'.$i)}}</span>
							    </div>
							    <div class="col-sm-3">
							     <?php echo Form::text('rate['.$i.']',  null, ['class'=>'form-control','placeholder' => 'Rate']); ?>
							     <span class="text-danger">{{ $errors -> first('rate.'.$i)}}</span>
							    </div>
							   </div>
							<?php
									endfor;
								else:
							?>
							<?php
									for($i=1;$i<count($forex);$i++):?>
									<div class="form-group row">
									   <div class="col-sm-4"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
									    <div class="col-sm-5">
									     <?php echo Form::select('currencies['.$i.']', $currencies_arr, $forex[$i]->currency, ['class'=>'form-control select2','placeholder' => 'Supported Currency&hellip;']); ?>
									     <span class="text-danger">{{ $errors -> first('currencies.'.$i)}}</span>
									    </div>
									    <div class="col-sm-3">
									     <?php echo Form::text('rate['.$i.']',  floatval($forex[$i]->rate) , ['class'=>'form-control','placeholder' => 'Rate']); ?>
									     <span class="text-danger">{{ $errors -> first('rate.'.$i)}}</span>
									    </div>
									   </div>
							   <?php
							   		endfor;
								endif;
							?>
							  </fieldset>
							  <div class="form-group row">
							  		<div class="col-sm-4"></div>
							  		<div class="col-sm-8"><button id="btn-add" tittle="Add&hellip;" type="button" class="btn"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></div>
							  </div>

						</div>
						<div class="col-sm-12">
							<div class="col-sm-6">
								<div class="form-group row pull-left">
									@if($user->can('delete.merchant'))
									<button type="button" id="btn-delete" data-href="/admin/merchants/<?php echo str_slug($merchant->slug);?>/delete" class="btn btn-danger">Delete Merchant</button>
									@endif
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group row pull-right">
									<button type="submit" class="btn btn-default">Update Merchant</button>
								</div>
							</div>
						</div>
						{!! Form::close() !!}
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>

@stop


@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function(){
	$('#btn-delete').click(function(){
		var c = confirm('Are you sure?');
		var url = $(this).data('href');
		if(c)
		{
			$('#form input[name="_method"]').val('DELETE');
			$('#form').submit();
		}

	});
	$('#btn-add').click(function(){
		var str = '<div class="form-group row">';
			str +='			<div class="col-sm-4"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			str +='			<div class="col-sm-5">';
			str +='				<?php echo Form::select('currencies1', $currencies_arr, null, ['name'=>'currencies[]','class'=>'form-control select2','placeholder' => 'Supported Currency']);?>';
			str +='			</div>';
			str +='			<div class="col-sm-3">';
			str +='				<?php echo Form::text('rate1',  null, ['name'=>'rate[]','class'=>'form-control','placeholder' => 'Rate']); ?>';
			str +='			</div>';
			str +='</div>';
		$('#currencies-row').append(str);
		return false;
	});
	$('#currencies-row').on('click','.close',function(){
		$(this).parent().parent().remove();
	});
});
</script>
@append