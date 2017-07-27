@extends('layouts.master')

@section('title')
	@lang('titles.edit_profile')
@stop

@section('content')
	<section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('user.box_header_edit_profile') - {{ $user->first_name }}</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('users.update', [$id]), 'role'=>'form', 'method' => 'PUT')) !!}
	            			<div class="col-md-12"> 
								<div class="col-md-6"> 
									<div class="form-group">
										<label class="col-sm-3 control-label required">@lang('user.user_form_label_first_name')</label>
										<div class="col-sm-9">
									    	{!! Form::text('first_name', $user->first_name, array('class'=>'form-control', 'placeholder'=>trans('user.user_form_placeholder_first_name'))) !!}
									    	<span class="text-danger">{{ $errors -> first('first_name')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label required">@lang('user.user_form_label_last_name')</label>
										<div class="col-sm-9">
									    	{!! Form::text('last_name', $user->last_name, array('class'=>'form-control', 'placeholder'=>trans('user.user_form_placeholder_last_name'))) !!}
									    	<span class="text-danger">{{ $errors -> first('last_name')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label required">@lang('user.user_form_label_email')</label>
										<div class="col-sm-9">
									    	{!! Form::email('email', $user->email, array('class'=>'form-control', 'readonly'=>true)) !!}
									    	<span class="text-danger">{{ $errors -> first('email')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">@lang('user.user_form_label_new_password')</label>
										<div class="col-sm-9">
											{!! Form::password('new_password', array('class'=>'form-control', 'placeholder'=>trans('user.user_form_placeholder_new_password'))) !!}
									    	<span class="text-danger">{{ $errors -> first('new_password')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">@lang('user.user_form_label_new_pwd_confirm')</label>
										<div class="col-sm-9">
									  		{!! Form::password('new_password_confirmation', array('class'=>'form-control', 'placeholder'=>trans('user.user_form_placeholder_new_pwd_confirm'))) !!}
									    	<span class="text-danger">{{ $errors -> first('new_password_confirmation')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">@lang('user.user_form_label_contact_no')</label>
										<div class="col-sm-9">
									    	{!! Form::text('contact_no', $user->contact_no, array('class'=>'form-control', 'placeholder'=>trans('user.user_form_placeholder_contact_no'))) !!}
									    	<span class="text-danger">{{ $errors -> first('contact_no')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3">@lang('user.user_form_label_address')</label>
										<div class="col-sm-9">
									    	{!! Form::textarea('address', $user->address, array('class'=>'form-control', 'placeholder'=>trans('user.user_form_placeholder_address'))) !!}
									    	<span class="text-danger">{{ $errors -> first('address')}}</span>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="col-sm-3 control-label required">@lang('user.user_form_label_default_timezone')</label>
										<div class="col-sm-9">
								    		{!! Form::select('timezone', $timezone, $user->timezone, array('class'=>'form-control')) !!}
								    		<span class="text-danger">{{ $errors -> first('timezone')}}</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label required">@lang('user.user_form_label_default_currency')</label>
										<div class="col-sm-9">
								    		{!! Form::select('currency', $currency, $user->currency, array('class'=>'form-control')) !!}
								    		<span class="text-danger">{{ $errors -> first('currency')}}</span>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-12"> 
								<button class="btn btn-default pull-right" type="submit">@lang('user.button_update_profile')</button>
							</div>
				        {!! Form::close() !!}
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<style type="text/css">
	textarea {
		resize: none;
	}
</style>

<script type="text/javascript">

</script>
@append