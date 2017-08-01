@extends('layouts.master')

@section('title')
	@lang('admin/user.page_title_user_update')
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
	              		<h3 class="box-title">@lang('admin/user.box_header_user_edit')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body channels channel-page channel-edit">
	            		<div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('admin/user.tab_title_details')</a></li>
                                @if($user->category == 'channelmanager')
                                	<li role="presentation"><a href="#channels" aria-controls="channels" role="tab" data-toggle="tab">@lang('admin/user.tab_title_channels')</a></li>
                            	@endif
                            </ul>

                            {!! Form::open(array('url' => route('admin.users.update', [$id]), 'method' => 'PUT', 'class'=>'edit-user', 'id'=>'edit-user-form')) !!}
		            		<div class="tab-content">

                                <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                	<div class="col-xs-12">
				            			<div class="col-xs-6">
					            			<div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="first_name">@lang('admin/user.user_form_label_first_name')</label>
					            				<div class="col-xs-9">
			   										{!! Form::text( 'first_name', $user->first_name, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_first_name')] ) !!}
			   										<div class="error">{{ $errors->first('first_name') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="last_name">@lang('admin/user.user_form_label_last_name')</label>
					            				<div class="col-xs-9">
					            					{!! Form::text( 'last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_last_name')] ) !!}
								                	<div class="error">{{ $errors->first('last_name') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label" for="email">@lang('admin/user.user_form_label_email')</label>
					            				<div class="col-xs-9">
					            					{!! Form::text( 'email', $user->email, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_email'), 'readonly'] ) !!}
								                	<div class="error">{{ $errors->first('email') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label" for="contact_no">@lang('admin/user.user_form_label_contact_no')</label>
					            				<div class="col-xs-9">
					            					{!! Form::text( 'contact_no', $user->contact_no, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_contact_no')] ) !!}
								                	<div class="error">{{ $errors->first('contact_no') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3" for="address">@lang('admin/user.user_form_label_address')</label>
					            				<div class="col-xs-9">
					            					{!! Form::textarea( 'address', '', ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_address')] ) !!}
								                	<div class="error">{{ $errors->first('address') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="user_category">@lang('admin/user.user_form_label_user_category')</label>
					            				<div class="col-xs-9">
					            					{!! Form::select('category', $roles, strtolower(str_replace(' ','',$user->category)), array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_user_category'))) !!}
								                	<div class="error">{{ $errors->first('category') }}</div>
								                </div>
								            </div>

								            <div id="merchant_field" class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="merchant">@lang('admin/user.user_form_label_merchant')</label>
					            				<div class="col-xs-9">
					            					{!! Form::select('merchant', $merchants, !empty($user->merchant->slug) ? $user->merchant->slug : '', array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_merchant'))) !!}
								                	<div class="error">{{ $errors->first('merchant') }}</div>
								                </div>
								            </div>
							            </div>

							            <div class="col-xs-6">
								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="default_timezone">@lang('admin/user.user_form_label_default_timezone')</label>
					            				<div class="col-xs-9">
					            					{!! Form::select('timezone', $timezones, $user->timezone, array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_default_timezone'))) !!}
								                	<div class="error">{{ $errors->first('timezone') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="default_currency">@lang('admin/user.user_form_label_default_currency')</label>
					            				<div class="col-xs-9">
					            					{!! Form::select('currency', $currencies, $user->currency, array('class' => 'form-control select2', 'placeholder' => trans('admin/user.user_form_placeholder_default_currency'))) !!}
								                	<div class="error">{{ $errors->first('currency') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-xs-3 control-label required" for="status">@lang('admin/user.user_form_label_status')</label>
					            				<div class="col-xs-9">
					            					@if(strcasecmp($user->status, 'Unverified') == 0)
					            						{!! Form::text('status', $user->status, array('class' => 'form-control', 'readonly' => true)) !!}
						            				@else
							            				{!! Form::select('status', $statuses, $user->status, array('class' => 'form-control select2-nosearch')) !!}
									                @endif
								                	<div class="error">{{ $errors->first('status') }}</div>
								                </div>
								            </div>
							            </div>
					         		</div>

						         	<div class="col-xs-12">
						         		<div class="form-group pull-right">
							            	<button type="button" class="submit-btn btn btn-default">@lang('admin/user.button_edit_user')</button>
							            </div>
							        </div>
					        	</div>


					        	@if($user->category == 'channelmanager')
					        	<div role="tabpanel" class="tab-pane clearfix" id="channels">
                                    <div class="col-xs-12">
                                        <div class="form-group has-feedback">
                                            <div class="col-xs-6">
                                                <h4 class="merchant-header">@lang('admin/user.box_header_channel_user')</h4>
                                            </div>
                                            <div class="col-xs-6">
                                                <span class="pull-right">
                                                    <ul id="js-pagination"></ul>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <div class="col-xs-6">
                                                <label class="col-xs-3 control-label" for="filterChannels">@lang('admin/user.user_form_label_filter_channel')</label>
                                                <div class="col-xs-8">
                                                    {!! Form::text( 'filterChannels', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.user_form_placeholder_filter_channel')] ) !!}
                                                </div>
                                            </div>
                                            <div class="col-xs-2">
                                                <label class="nobold-label">
                                                    {!! Form::checkbox('filterName', 1, 1) !!}
                                                    @lang('admin/user.user_form_label_sort_channel')
                                                </label>
                                            </div>
                                            <div class="col-xs-2">
                                                <label class="nobold-label">
                                                    {!! Form::checkbox('filterChecked', 1, 1) !!}
                                                    @lang('admin/user.user_form_label_sort_channel_checked')
                                                </label>
                                            </div>
                                        </div>

                                        <div id="merchants-list"></div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group pull-right">
                                        	<button type="button" class="submit-btn btn btn-default">@lang('admin/user.button_edit_user')</button>
                                        </div>
                                    </div>
                                </div>
                                @endif

					        </div>
					        {!! Form::close() !!}

	            		</div>
	            	</div>
	        	</div>
	    	</div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<script src="{{ asset('js/jquery.twbsPagination.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/channel-merchant-table.js', env('HTTPS', false)) }}"></script>
<!-- for select2 -->
<link href="{{ asset('plugins/select2/select2.min.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/select2.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/select2/select2.full.min.js', env('HTTPS', false)) }}" type="text/javascript"></script>

<style type="text/css">
	.form-group {
		display: flex;
	}

	textarea {
		resize: none;
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		checkSelectedUserCategory($("[name=category]").val());

		$("[name=category]").change(function() {
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
			else {
				$("#merchant_field").hide();
			}
		}

		/** For Channel Manager's Channels tab - START **/
		// for channel pagination
        var user_channels = [@foreach($user_channels as $user_channel){{$user_channel->id}},@endforeach]; // get selected channels
        var channels = []; // get all channels
        @foreach($channels as $channel)
            channels[{{$channel->id}}] = {'id':{{$channel->id}}, 'name': '{{$channel->name}}'};
        @endforeach

        // to retain checked values
        $('body').on('complete.merchant', function(){
            $('input[name="merchant_id[]"]').change(function() {
                var value = parseInt($(this).val());
                if($(this).is(':checked')){
                    if($.inArray(value, selectedChannels) == -1){
                        selectedChannels.push(value);
                    }
                }else{
                    if($.inArray(value, selectedChannels) != -1){
                        var index = selectedChannels.indexOf(value);
                        selectedChannels.splice(index, 1);
                    }
                }
            });
        });

        // Generate channel list
        drawMerchantList(channels, user_channels);

        // get selected channel list on load
        var selectedChannels = $('input[name="merchant_id[]"]:checked').map(function() {
            return parseInt(this.value);
        }).get();

        // Set filters event
        $('input[name="filterChannels"]').on('keyup', function(){
            var filterName = $('input[name="filterChannels"]').val();
            drawMerchantList(channels, selectedChannels, filterName, $('input[name="filterName"]').is(':checked'), $('input[name="filterChecked"]').is(':checked'));
        });

        $('input[name="filterName"],input[name="filterChecked"]').change(function() {
            var filterName = $('input[name="filterChannels"]').val();
            drawMerchantList(channels, selectedChannels, filterName, $('input[name="filterName"]').is(':checked'), $('input[name="filterChecked"]').is(':checked'));
        });
        /** For Channel Manager's Channels tab - END **/

        $('.submit-btn').on('click', function(){
        	@if($user->category == 'channelmanager')
        	drawMerchantList(channels, selectedChannels);
        	@endif
        	$('#edit-user-form').submit();
        });
	});
</script>
@append