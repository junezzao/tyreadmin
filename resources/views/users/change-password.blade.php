@extends('layouts.master')

@section('title')
@lang('titles.change_password')
@stop

@section('content')
	<section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">{{ trans('terms.change_password') }}</h3>
	            	</div>
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('user.changePassword.submit'), 'role'=>'form', 'method' => 'PUT')) !!}
			                <div class="form-group has-feedback">
			                    <label class="col-md-4 col-sm-5 col-xs-12 control-label" for="email">{{ trans('terms.email_address') }}</label>
			                    <div class="col-md-8 col-sm-7 col-xs-12">
			                        {!! Form::text( 'email', $user->email, ['class' => 'form-control', 'placeholder' => trans('terms.email_address'), 'readonly'=>true] ) !!}
			                        <div class="error">{{ $errors->first('email') }}</div>
			                    </div>
			                </div>

			                <div class="form-group has-feedback">
			                    <label class="col-md-4 col-sm-5 col-xs-12 control-label" for="old_password">{{ trans('terms.old_password') }}</label>
			                    <div class="col-md-8 col-sm-7 col-xs-12">
			                        {!! Form::password('old_password', ['class' => 'form-control', 'placeholder' => trans('terms.old_password'), 'autocomplete' => 'off'] ) !!}
			                        <div class="error">{{ $errors->first('old_password') }}</div>
			                    </div>
			                </div>

			                <div class="form-group has-feedback">
			                    <label class="col-md-4 col-sm-5 col-xs-12 control-label" for="new_password">{{ trans('terms.new_password') }}</label>
			                    <div class="col-md-8 col-sm-7 col-xs-12">
			                        {!! Form::password('new_password', ['class' => 'form-control', 'placeholder' => trans('terms.new_password'), 'autocomplete' => 'off'] ) !!}
			                        <div class="error">{{ $errors->first('new_password') }}</div>
			                    </div>
			                </div>

			                <div class="form-group has-feedback">
			                    <label class="col-md-4 col-sm-5 col-xs-12 control-label" for="new_password_confirmation">{{ trans('terms.confirm_new_password') }}</label>
			                    <div class="col-md-8 col-sm-7 col-xs-12">
			                        {!! Form::password('new_password_confirmation', ['class' => 'form-control', 'placeholder' => trans('terms.confirm_new_password'), 'autocomplete' => 'off'] ) !!}
			                        <div class="error">{{ $errors->first('new_password_confirmation') }}</div>
			                    </div>
			                </div>

			                <div class="form-group">
			                    {!! Form::submit(strtoupper(trans('terms.submit')), array('class' => 'signin-btn bg-primary'))!!}
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
    $("input[name=first_name]").focus();
});
</script>
@append