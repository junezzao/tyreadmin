@extends('layouts.plain')

@section('title')
@lang('titles.login')
@stop

@section('content')
    <div class="login-box clearfix">
        <div class="login-box-body col-xs-12">
            <div class="login-box-logo">
                <img src="{{ asset('images/logo-login.png',env('HTTPS',false)) }}" />
            </div>
            <!--<p class="login-box-msg">{{ trans('sentence.login_header') }}</p>-->

            {!! Form::open(array('url' => 'auth/login', 'id'=>'login-form')) !!}
                <div class="form-group has-feedback">
                    {!! Form::text('email', null, array('class' => 'form-control', 'placeholder' => trans('terms.username'), 'autocomplete' => 'off')) !!}
                    <div class="error">{{ $errors->first('email') }}</div>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div> 
                <div class="form-group has-feedback">
                    {!! Form::password('password', array('class' => 'form-control', 'placeholder' => trans('terms.password'), 'autocomplete' => 'off')) !!}
                    <div class="error">{{ $errors->first('password') }}</div>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                
                <div class="icheck row checkbox">
                    <div class="col-xs-6 col-sm-5 col-md-6">
                        <label><input type="checkbox"> {{ trans('terms.remember_me') }}</label>
                    </div>
                    <div class="col-xs-6 col-sm-7 col-md-6" style="text-align:right">
                        <a class="forgot-password" id="forgot-password-link">{{ trans('sentence.forgot_your_password') }}</a>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::submit(strtoupper(trans('terms.sign_in')), array('class' => 'signin-btn bg-primary'))!!}
                </div>
                <div class="align-center">
                    <small class="help-text">{{ trans('sentence.01_dont_have_an_account_yet') }} {!! Html::link('auth/register', trans('sentence.01_sign_up')) !!} {{ trans('sentence.01_for_one_today') }}!</small>
                </div>

            {!! Form::close() !!}

        </div>
    </div>

    <div class="signin-form col-lg-4 col-md-5 col-sm-7 col-xs-10" id="password-reset-container">
        <div class="close">&times;</div>
        <p>Please enter your email address.<br/>You will receive a link to reset your password.</p>
        @include('auth.password')
    </div>
@stop

@section('footer_scripts')
<script type="text/javascript">

function refreshCaptcha(){
    $.ajax({
        url: '{{ URL::to("captcha/refresh") }}',
        type: 'get',
        dataType: 'html',        
        success: function(json) {
            $('.captcha').html(json);
            $('input[name="captcha"]').focus();
        },
        error: function(data) {
            alert('Try Again.');
        }
    });
}

jQuery(document).ready(function(){
    $("input[name=email]").focus();
    $('#forgot-password-link').click(function(){
        $('#password-reset-container').fadeIn(300);
        $("input[name=reset_email]").focus();
    });
    $('#password-reset-container .close').click(function () {
        $('#password-reset-container').hide();
    });

    $("input[name=reset_email]").focusout(function (){ 
        $('input[name="captcha"]').focus();
    });
});
</script>
@append

