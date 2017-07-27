@extends('layouts.master')

@section('title')
	@lang('admin/user.page_title_user_create')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/user.content_header_users')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/user.box_header_user_create')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('admin.users.store'), 'method' => 'POST')) !!}
	            			<div class="col-xs-12">
		            			<div class="col-xs-6">
			            			<div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="first_name">@lang('admin/user.user_form_label_first_name')</label>
			            				<div class="col-xs-9">
	   										{!! Form::text( 'first_name', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_first_name')] ) !!}
	   										<div class="error">{{ $errors->first('first_name') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="last_name">@lang('admin/user.user_form_label_last_name')</label>
			            				<div class="col-xs-9">
			            					{!! Form::text( 'last_name', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_last_name')] ) !!}
						                	<div class="error">{{ $errors->first('last_name') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="email">@lang('admin/user.user_form_label_email')</label>
			            				<div class="col-xs-9">
			            					{!! Form::text( 'email', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_email')] ) !!}
						                	<div class="error">{{ $errors->first('email') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label" for="contact_no">@lang('admin/user.user_form_label_contact_no')</label>
			            				<div class="col-xs-9">
			            					{!! Form::text( 'contact_no', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_contact_no')] ) !!}
						                	<div class="error">{{ $errors->first('contact_no') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3" for="address">@lang('admin/user.user_form_label_address')</label>
			            				<div class="col-xs-9">
			            					{!! Form::textarea( 'address', null, ['class' => 'form-control no-resize', 'placeholder' => trans('admin/user.user_form_placeholder_address')] ) !!}
						                	<div class="error">{{ $errors->first('address') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="user_category">@lang('admin/user.user_form_label_user_category')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('user_category', $roles, (!empty($selected_merchant) ? 'clientuser' : null), array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_user_category'))) !!}
						                	<div class="error">{{ $errors->first('user_category') }}</div>
						                </div>
						            </div>

						            <div id="merchant_field" class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="merchant">@lang('admin/user.user_form_label_merchant')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('merchant', $merchants, (!empty($selected_merchant) ? $selected_merchant : null), array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_merchant'))) !!}
						                	<div class="error">{{ $errors->first('merchant') }}</div>
						                </div>
						            </div>
					            </div>
					            
					            <div class="col-xs-6">
						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label" for="default_timezone">@lang('admin/user.user_form_label_default_timezone')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('default_timezone', $timezones, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_default_timezone'))) !!}
						                	<div class="error">{{ $errors->first('default_timezone') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label" for="default_currency">@lang('admin/user.user_form_label_default_currency')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('default_currency', $currencies, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_default_currency'))) !!}
						                	<div class="error">{{ $errors->first('default_currency') }}</div>
						                </div>
						            </div>
					            </div>
				         	</div>

				         	<div class="col-xs-12">
				         		<div class="form-group pull-right">
					               <button type="submit" id="btn_create_new_user" class="btn btn-default">@lang('admin/user.button_create_new_user')</button>
					            </div> <!-- / .form-actions -->
					        </div>
				        {!! Form::close() !!}
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<!-- for select2 -->
<link href="{{ asset('plugins/select2/select2.min.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/select2.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/select2/select2.full.min.js', env('HTTPS', false)) }}" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".select2").select2();
		
		var selected_merchant = "{{ (!empty($selected_merchant) ? $selected_merchant : '') }}";
		if (selected_merchant == '') {
			$("#merchant_field").hide();
			checkSelectedUserCategory($("[name=user_category]").val());
		}

		$("[name=user_category]").change(function() {
			checkSelectedUserCategory($(this).val());
		});

		function checkSelectedUserCategory (user_category) {
			var user_category_with_merchant = ['clientadmin', 'clientuser', 'mobilemerchant'];
			if (user_category != '') {
				if (jQuery.inArray( user_category, user_category_with_merchant ) < 0) {
					$("#merchant_field").hide();
					$('label[for=default_timezone]').addClass('required');
					$('label[for=default_currency]').addClass('required');
				}
				else {
					$("#merchant_field").show();
					$('label[for=default_timezone]').removeClass('required');
					$('label[for=default_currency]').removeClass('required');
				}
			}
		}
	});
</script>
@append