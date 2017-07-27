@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('user.page_title_account_details')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('user.page_title_account_details')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('user.page_title_account_details')</h3>
	            	</div>
	            	<div class="box-body">
	            		{!! Form::open(array('url' => 'account_details', 'files' => true, 'id' => 'edit-account-details-form')) !!}
	            			{!! Form::hidden('id', $merchant->id) !!}
	            			{!! Form::hidden('slug', $merchant->slug) !!}
	            		  <div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('name', $merchant->name,['class'=>'form-control','placeholder'=>'Company Name']) !!}
							      <span class="text-danger">{{ $errors->first('name')}}</span>
							    </div>
							  </div>
							  
							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4 required']) !!}
							    <div class="col-sm-8">
							     {!! Form::textarea('address', $merchant->address,['class'=>'form-control','placeholder'=>'Company Address']) !!}
							     <span class="text-danger">{{ $errors->first('address')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('contact','Contact No.',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('contact', $merchant->contact,['class'=>'form-control','placeholder'=>'Support Contact Number']) !!}
							      <span class="text-danger">{{ $errors->first('contact')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('email','Email',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::email('email', $merchant->email,['class'=>'form-control','placeholder'=>'Support Email']) !!}
							      <span class="text-danger">{{ $errors->first('email')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg_no','GST Registration Number',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('gst_reg_no',$merchant->gst_reg_no,['class'=>'form-control','placeholder'=>'GST Registration Number']) !!}
							      <span class="text-danger">{{ $errors->first('gst_reg_no')}}</span>
							    </div>
							  </div>
							  
							  <div class="form-group row">
							    {!! Form::label('self_invoicing','Self-Invoicing',['class'=>'col-sm-4']) !!}
							    <div class="col-sm-8">
							      <div class="checkbox">
							        <label>
							          {!! Form::checkbox('self_invoicing', true) !!} Check this if you would like to issue tax invoice under your company name
							        </label>
							      </div>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('timezone','Default Timezone',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::select('timezone', $timezones, $merchant->timezone, ['class'=>'form-control']) !!}
							      <span class="text-danger">{{ $errors->first('timezone') }}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('currency','Default Currency', ['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
								    {!! Form::select('currency', $currencies_arr, $merchant->currency, ['class'=>'form-control','placeholder' => 'Select Default Currency...']) !!}
							     <span class="text-danger">{{ $errors->first('currency') }}</span>
							    </div>
							  </div>
						</div>
						<div class="col-sm-6">
							<div class="form-group row">
							    {!! Form::label('ae','Account Manager', ['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							    	{!! Form::text('ae', $ae, ['class'=>'form-control', 'disabled' => 'disabled']) !!}
							     	<span class="text-danger">{{ $errors->first('currency') }}</span>
							    </div>
							  </div>
							<div class="form-group row">
							    {!! Form::label('logo','Logo',['class'=>'col-sm-4 required']) !!}
							    <div class="col-sm-8">
							      	<div class="col-xs-12" style="background:transparent url({{ $merchant->logo_url }}) no-repeat center; background-size: 100%; width: 200px; height: 200px;border: 1px solid #ececec; margin-bottom: 10px;"></div>
							      	<label class="file">
									  {!! Form::file('logo', ['class'=>'control-label']) !!}
									  <span class="file-custom"></span>
									  <span class="text-danger">{{ $errors->first('logo')}}</span>
									</label>
							    </div>
							  </div>
							  
							  <div class="form-group row">
							  	<div class="col-sm-4">{!! Form::label('supported_currencies','Supported Currencies', ['class'=>'col-sm-4 control-label']) !!}</div>
							  	<div class="col-sm-8" id="currencies-row">
							  		@if( !empty(old('currencies')) )
								 		@for($i = 0; $i < count(old('currencies')); $i++)
										 	<div class="form-group row">
										 	<div class="col-sm-1"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
										    <div class="col-sm-7">
										     {!! Form::select('currencies['.$i.']', $currencies_arr, null, ['class'=>'form-control','placeholder' => 'Supported Currency']) !!}
										     <span class="text-danger">{{ $errors->first('currencies.'.$i) }}</span>
										    </div>
										    <div class="col-sm-4">
										     {!! Form::text('rate['.$i.']',  null, ['class'=>'form-control','placeholder' => 'Rate']) !!}
										     <span class="text-danger">{{ $errors->first('rate.'.$i) }}</span>
										    </div>
										   </div>
										@endfor
									@else
								  		@if(!empty($supported_currencies))
								  			@for($i = 0; $i < count($supported_currencies); $i++)
								  				<div class="form-group row">
									  				<div class="col-sm-1"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
										  			<div class="col-sm-7">
										  				{!! Form::select('currencies['.$i.']', $currencies_arr, $supported_currencies[$i]['currency'], [ 'name'=>'currencies[]', 'class' =>'form-control', 'placeholder' => 'Supported Currency']) !!}
										  				<span class="text-danger">{{ $errors -> first('currencies.'.$i) }}</span>
										  			</div>
										  			<div class="col-sm-4">
										  				{!! Form::text('rate['.$i.']', $supported_currencies[$i]['rate'], ['name'=>'rate[]', 'class' => 'form-control', 'placeholder' => 'Rate']) !!}
										  				<span class="text-danger">{{ $errors -> first('rate.'.$i) }}</span>
										  			</div>
										  		</div>
									  		@endfor
								  		@else
								  			<div class="form-group row">
									  				<div class="col-sm-1"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
										  			<div class="col-sm-7">
										  				{!! Form::select('currencies[]', $currencies_arr, null, [ 'class' =>'form-control', 'placeholder' => 'Supported Currency']) !!}
										  				<span class="text-danger">{{ $errors -> first('currencies') }}</span>
										  			</div>
										  			<div class="col-sm-4">
										  				{!! Form::text('rate[]', null, ['class' => 'form-control', 'placeholder' => 'Rate']) !!}
										  				<span class="text-danger">{{ $errors -> first('rate') }}</span>
										  			</div>
										  		</div>
								  		@endif
								  	@endif
							  	</div>
							  </div>
							
							  <div>
                            <div class="col-xs-9"></div>
                                <div class="col-xs-3">
                                    <button id="btn-add" class="pull-right" tittle="Add&hellip;" type="button">
                                        <i class="fa fa-2x fa-plus-circle" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
							  
						</div>
						<div class="col-sm-12">
							<div class="form-group row pull-right">
								<button type="submit" class="btn btn-default">Update Account Details</button>
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
			str +='			<div class="col-sm-1"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			str +='			<div class="col-sm-7">';
			str +='				<?php echo Form::select('currencies1', $currencies_arr, null, ['name'=>'currencies[]', 'class'=>'form-control', 'placeholder' => 'Supported Currency']);?>';
			str +='			</div>';
			str +='			<div class="col-sm-4">';
			str +='				<?php echo Form::text('rate1',  null, ['name'=>'rate[]', 'class'=>'form-control', 'placeholder' => 'Rate']); ?>';
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