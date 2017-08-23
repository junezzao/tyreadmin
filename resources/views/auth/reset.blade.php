@extends('layouts.plain')

@section('title')
    @lang('titles.account_activation')
@stop

@section('content')
    <div class="login-box clearfix" style="width:45%">
        <div class="login-box-body col-xs-12">
            <div class="login-box-msg">
                <h4><b>{{ trans('sentence.account_activation_header') }}</b></h4>
            </div>

            {!! Form::open(array('url' => 'password/reset', 'id'=>'login-form')) !!}
                {!! Form::hidden( 'token', $token ) !!}

                <div class="form-group has-feedback">
                    <label class="col-lg-5 col-xs-12 control-label required" for="email_address">{{ trans('terms.email_address') }}</label>
                    <div class="col-lg-7 col-xs-12">
                        {!! Form::text( 'email', null, ['class' => 'form-control', 'placeholder' => trans('terms.email_address'), 'autocomplete' => 'off'] ) !!}
                        <div class="error">{{ $errors->first('email') }}</div>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label class="col-lg-5 col-xs-12 control-label required" for="password">{{ trans('terms.password') }}</label>
                    <div class="col-lg-7 col-xs-12">
                        {!! Form::password('password', array('class' => 'form-control', 'placeholder' => trans('terms.password'), 'autocomplete' => 'off')) !!}
                        <div class="error">{{ $errors->first('password') }}</div>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label class="col-lg-5 col-xs-12 control-label required" for="password_confirmation">{{ trans('terms.confirm_password') }}</label>
                    <div class="col-lg-7 col-xs-12">
                        {!! Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => trans('terms.confirm_password'), 'autocomplete' => 'off')) !!}
                        <div class="error">{{ $errors->first('password_confirmation') }}</div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::submit(strtoupper(trans('terms.activate_account')), array('class' => 'signin-btn bg-primary'))!!}
                </div>
            {!! Form::close() !!}

        </div>
    </div>
@stop

@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function($){
    $("input[name=email]").focus();
});
</script>
@append