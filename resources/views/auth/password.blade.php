{!! Form::open(array('url' => '/password/forgot', 'id' => 'password-reset-form')) !!}
    <div class="form-group has-feedback">
        {!! Form::text('email', '', array('class' => 'form-control', 'placeholder' => trans('sentence.enter_your_email_address'))) !!}
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
    </div>
    <div class="form-group">
        {!! Form::submit(strtoupper(trans('sentence.send_password_reset_link')), array('class' => 'signin-btn bg-primary')) !!}
    </div>
{!! Form::close() !!}