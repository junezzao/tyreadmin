@extends('layouts.plain')

@section('title')
    @lang('titles.login')
@stop

@section('content')
    <div class="login-box clearfix">
        <!-- <div class="col-md-4 col-sm-12-offset-2 signin-info">
            {!! Html::image("images/signin-logo.png", "Logo", array('class'=>'img-responsive center-block login-logo'),env('HTTPS',false)) !!}
            <p>
                We take pride in helping our clients grow, thrive and prosper and we enjoy the relationships we build along the way.
                <br/><br/><br/>&copy; {{ date('Y') }} Hubwire.
            </p>
        </div> -->

        <div class="login-box-body col-xs-12">
            <p class="login-box-msg">{{ trans('sentence.login_header') }}</p>

            {!! Form::open(array('url' => 'auth/login', 'id'=>'login-form')) !!}
                <div class="form-group has-feedback">
                    {!! Form::text('email', null, array('class' => 'form-control', 'placeholder' => trans('terms.username'))) !!}
                    <div class="error">{{ $errors->first('email') }}</div>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div> 
                <div class="form-group has-feedback">
                    {!! Form::password('password', array('class' => 'form-control', 'placeholder' => trans('terms.password'))) !!}
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
                <div style="text-align:center">
                    <small class="help-text">{{ trans('sentence.01_dont_have_an_account_yet') }} {!! Html::link('auth/register', trans('sentence.01_sign_up')) !!} {{ trans('sentence.01_for_one_today') }}!</small>
                </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- Password reset form -->
    <div class="signin-form col-lg-5 col-md-6 col-sm-8 col-xs-11" id="password-reset-container">
        <div class="close">&times;</div>
        <p>{{ trans('sentence.forgot_password') }}</p>
        @include('auth.password')
    </div>
    <!-- / Password reset form -->
@stop

@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function($){
    $("input[name=email]").focus();
    $('#forgot-password-link').click(function(){
        $('#password-reset-container').fadeIn(300);
        $("input[name=email]").focus();
    });
    $('#password-reset-container .close').click(function () {
        $('#password-reset-container').hide();
    });
});
</script>
@append

