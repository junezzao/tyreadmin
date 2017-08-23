@extends('layouts.plain')

@section('title')
    @lang('titles.register')
@stop

@section('content')
    <div class="register-box clearfix">
        <div class="register-box-body col-xs-12">
            <div class="register-box-msg">
                <h4><b>{{ trans('sentence.account_registration_header') }}</b></h4>
                <div>{{ trans('sentence.account_registration_sub_header') }}</div>
            </div>

            {!! Form::open( array('url' => 'auth/register', 'id'=>'register-form') ) !!}
                <div class="form-group" style="text-align:center">
                    <label class="col-xs-12 required">{{ trans('terms.operation_type') }}</label>
                    <small class="help-text col-xs-12">{{ trans('terms.please_pick_1') }}</small>
                    <div class="error col-xs-12">{{ $errors->first('operation_type') }}</div>
                    <div class="col-xs-12" style="margin-top:15px">
                        {!! Form::hidden( 'operation_type', null ) !!}
                        <div class="col-sm-5 col-xs-12 operation-type-btn" data-value="Tyre Service Centre">{{ trans('terms.tyre_service_centre') }}</div>
                        <div class="col-sm-2 col-xs-12"></div>
                        <div class="col-sm-5 col-xs-12 operation-type-btn" data-value="Fleet Operation Team">{{ trans('terms.fleet_operation_team') }}</div>
                    </div>
                </div>

                <hr/>

                <div class="form-group" style="text-align:center">
                    <label class="col-xs-12">{{ trans('terms.profile') }}</label>
                </div>

                <div class="form-group has-feedback">
                    <label class="col-md-4 col-sm-5 col-xs-12 control-label required" for="first_name">{{ trans('terms.first_name') }}</label>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        {!! Form::text( 'first_name', null, ['class' => 'form-control', 'placeholder' => trans('terms.first_name')] ) !!}
                        <div class="error">{{ $errors->first('first_name') }}</div>
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label class="col-md-4 col-sm-5 col-xs-12 control-label required" for="last_name">{{ trans('terms.last_name') }}</label>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        {!! Form::text( 'last_name', null, ['class' => 'form-control', 'placeholder' => trans('terms.last_name')] ) !!}
                        <div class="error">{{ $errors->first('last_name') }}</div>
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label class="col-md-4 col-sm-5 col-xs-12 control-label required" for="email">{{ trans('terms.email_address') }}</label>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        {!! Form::text( 'email', null, ['class' => 'form-control', 'placeholder' => trans('terms.email_address'), 'autocomplete' => 'off'] ) !!}
                        <div class="error">{{ $errors->first('email') }}</div>
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label class="col-md-4 col-sm-5 col-xs-12 control-label required" for="contact_no">{{ trans('terms.contact_number') }}</label>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        {!! Form::text( 'contact_no', null, ['class' => 'form-control', 'placeholder' => trans('terms.contact_number')] ) !!}
                        <div class="error">{{ $errors->first('contact_no') }}</div>
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label class="col-md-4 col-sm-5 col-xs-12 control-label required" for="company_name">{{ trans('terms.company_name') }}</label>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        {!! Form::text( 'company_name', null, ['class' => 'form-control', 'placeholder' => trans('terms.company_name')] ) !!}
                        <div class="error">{{ $errors->first('company_name') }}</div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4 col-sm-5 col-xs-12">
                        <label class="control-label required">{{ trans('terms.address') }}</label>
                    </div>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        <div class="field-group">
                            {!! Form::text( 'address_line_1', null, ['class' => 'form-control', 'placeholder' => trans('terms.address_line_1')] ) !!}
                            <div class="error">{{ $errors->first('address_line_1') }}</div>
                        </div>
                        <div class="field-group">
                            {!! Form::text( 'address_line_2', null, ['class' => 'form-control', 'placeholder' => trans('terms.address_line_2')] ) !!}
                            <div class="error">{{ $errors->first('address_line_2') }}</div>
                        </div>
                        <div class="field-group col-xs-12 no-padding">
                            <div class="col-xs-6 no-padding">
                                {!! Form::text( 'address_city', null, ['class' => 'form-control', 'placeholder' => trans('terms.city')] ) !!}
                                <div class="error">{{ $errors->first('address_city') }}</div>
                            </div>
                            <div class="col-xs-6 pad-left no-padding">
                                {!! Form::text( 'address_postcode', null, ['class' => 'form-control', 'placeholder' => trans('terms.postcode')] ) !!}
                                <div class="error">{{ $errors->first('address_postcode') }}</div>
                            </div>
                        </div>
                        <div class="field-group col-xs-12 no-padding">
                            <div class="col-xs-6 no-padding">
                                {!! Form::text( 'address_state', null, ['class' => 'form-control', 'placeholder' => trans('terms.state')] ) !!}
                                <div class="error">{{ $errors->first('address_state') }}</div>
                            </div>
                            <div class="col-xs-6 pad-left no-padding country-div">
                                {!! Form::select( 'address_country', $countryList, null, ['class' => 'form-control select2', 'placeholder' => trans('terms.select_country')] ) !!}
                                <div class="error">{{ $errors->first('address_country') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::submit(strtoupper(trans('terms.sign_up')), array('class' => 'signin-btn bg-primary'))!!}
                </div>

                <div style="text-align:center">
                    <small class="help-text">{{ trans('sentence.00_already_have_an_account')}} {!! Html::link('/', trans('sentence.00_log_in')) !!} {{ trans('sentence.00_here') }}!</small>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function($){
    $("input[name=first_name]").focus();
    
    $('.operation-type-btn').click(function(){
       $('.operation-type-btn').removeClass('active');
        $(this).addClass('active');
        $('input[name="operation_type"]').val($(this).data('value'));
    });
});
</script>
@append

