{!! Form::open(array('url' => '/password/forgot', 'id' => 'password-reset-form')) !!}
    <div class="form-group has-feedback" style="width:80%">
        {!! Form::text('reset_email', '', array('class' => 'form-control', 'placeholder' => trans('sentence.enter_your_email_address'))) !!}
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback" style="width:80%">
    	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    	<div class="refereshrecapcha">
    		{!! captcha_img('flat') !!}
    		
    	</div>
    	<a href="javascript:refreshCaptcha()">Refresh</a>
    	<input type="text" name="captcha" class="form-control" style="margin-top:20px" />
    </div>
    <div class="form-group" style="width:80%">
        {!! Form::submit(strtoupper(trans('sentence.send_password_reset_link')), array('class' => 'signin-btn bg-primary')) !!}
    </div>
{!! Form::close() !!}