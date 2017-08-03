{!! Form::open(array('url' => '/password/forgot', 'id' => 'password-reset-form')) !!}
    <div class="form-group has-feedback">
        {!! Form::text('reset_email', '', array('class' => 'form-control', 'placeholder' => trans('sentence.enter_your_email_address'))) !!}
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
    </div>
    <div class="form-group">
    	<div>Enter the characters you see in the image below:
    	</div>
    </div>
    <div class="form-group">
    	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<div class="captcha">{!! captcha_img('flat') !!}</div>
    </div>
    <div class="form-group">
    	<a class="refresh" href="javascript:refreshCaptcha()">Try a different image</a>
    	<input type="text" name="captcha" class="form-control" />
    </div>
    <div class="form-group">
        {!! Form::submit(strtoupper(trans('sentence.send_password_reset_link')), array('class' => 'signin-btn bg-primary')) !!}
    </div>
{!! Form::close() !!}