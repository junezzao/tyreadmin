@extends('layouts.master')

@section('title')
@lang('titles.edit_user')
@stop

@section('content')
	
	<section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/user.box_header_user_edit')</h3>
	            	</div>
	            	<div class="box-body channels channel-page channel-edit">
	            		<div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">{{ trans('terms.details') }}</a></li>
                                <li role="presentation"><a href="#subscriptions" aria-controls="subscriptions" role="tab" data-toggle="tab">{{ trans('terms.subscriptions') }}</a></li>
                            </ul>

                            {!! Form::open(array('url' => route('admin.users.update', [$user->id]), 'method' => 'PUT', 'class'=>'edit-user', 'id'=>'edit-user-form')) !!}
		            		<div class="tab-content">

                                <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                	<div class="col-xs-12">
				            			<div class="col-md-6 col-sm-12 col-xs-12">
					            			<div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="first_name">{{ trans('terms.first_name') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
			   										{!! Form::text( 'first_name', $user->first_name, ['class' => 'form-control', 'placeholder' => trans('terms.first_name')] ) !!}
			   										<div class="error">{{ $errors->first('first_name') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="last_name">{{ trans('terms.last_name') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					{!! Form::text( 'last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => trans('terms.last_name')] ) !!}
								                	<div class="error">{{ $errors->first('last_name') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="email">{{ trans('terms.email_address') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					{!! Form::text( 'email', $user->email, ['class' => 'form-control', 'placeholder' => trans('terms.email_address'), 'readonly'] ) !!}
								                	<div class="error">{{ $errors->first('email') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="company_name">{{ trans('terms.company_name') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					{!! Form::text( 'company_name', $user->company_name, ['class' => 'form-control', 'placeholder' => trans('terms.company_name')] ) !!}
								                	<div class="error">{{ $errors->first('company_name') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="contact_no">{{ trans('terms.contact_number') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					{!! Form::text( 'contact_no', $user->contact_no, ['class' => 'form-control', 'placeholder' => trans('terms.contact_number')] ) !!}
								                	<div class="error">{{ $errors->first('contact_no') }}</div>
								                </div>
								            </div>

								            <div class="form-group">
							                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
							                        <label class="control-label required required">{{ trans('terms.address') }}</label>
							                    </div>
							                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
							                        <div class="field-group">
							                            {!! Form::text( 'address_line_1', $user->address_line_1, ['class' => 'form-control', 'placeholder' => trans('terms.address_line_1')] ) !!}
							                            <div class="error">{{ $errors->first('address_line_1') }}</div>
							                        </div>
							                        <div class="field-group">
							                            {!! Form::text( 'address_line_2', $user->address_line_2, ['class' => 'form-control', 'placeholder' => trans('terms.address_line_2')] ) !!}
							                            <div class="error">{{ $errors->first('address_line_2') }}</div>
							                        </div>
							                        <div class="field-group col-xs-12 no-padding">
							                            <div class="col-xs-6 no-padding">
							                                {!! Form::text( 'address_city', $user->address_city, ['class' => 'form-control', 'placeholder' => trans('terms.city')] ) !!}
							                                <div class="error">{{ $errors->first('address_city') }}</div>
							                            </div>
							                            <div class="col-xs-6 pad-left no-padding">
							                                {!! Form::text( 'address_postcode', $user->address_postcode, ['class' => 'form-control', 'placeholder' => trans('terms.postcode')] ) !!}
							                                <div class="error">{{ $errors->first('address_postcode') }}</div>
							                            </div>
							                        </div>
							                        <div class="field-group col-xs-12 no-padding">
							                            <div class="col-xs-6 no-padding">
							                                {!! Form::text( 'address_state', $user->address_state, ['class' => 'form-control', 'placeholder' => trans('terms.state')] ) !!}
							                                <div class="error">{{ $errors->first('address_state') }}</div>
							                            </div>
							                            <div class="col-xs-6 pad-left no-padding country-div">
							                                {!! Form::select( 'address_country', $countryList, $user->address_country, ['class' => 'form-control select2', 'placeholder' => trans('terms.select_country')] ) !!}
							                                <div class="error">{{ $errors->first('address_country') }}</div>
							                            </div>
							                        </div>
							                    </div>
							                </div>

								            
							            </div>

							            <div class="col-md-6 col-sm-12 col-xs-12">
								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="operation_type">{{ trans('terms.operation_type') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					{!! Form::select('operation_type', $operationTypes, $user->operation_type, array('class' => 'form-control select2-nosearch')) !!}
								                	<div class="error">{{ $errors->first('operation_type') }}</div>
								                </div>
								            </div>

								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="status">{{ trans('terms.status') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					@if(strcasecmp($user->status, 'Unverified') == 0)
					            						{!! Form::text('status', $user->status, array('class' => 'form-control', 'readonly' => true)) !!}
						            				@else
							            				{!! Form::select('status', $statuses, $user->status, array('class' => 'form-control select2-nosearch')) !!}
									                @endif
								                	<div class="error">{{ $errors->first('status') }}</div>
								                </div>
								            </div>


								            <div class="form-group has-feedback">
					            				<label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 control-label required" for="category">{{ trans('terms.subscription_plan') }}</label>
					            				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
					            					{!! Form::text( 'category', $user->category, ['class' => 'form-control', 'placeholder' => trans('terms.subscription_plan'), 'readonly'] ) !!}
								                	<div class="error">{{ $errors->first('category') }}</div>
								                </div>
								            </div>
							            </div>
					         		</div>

						         	<div class="col-xs-12">
						         		<div class="form-group pull-right">
							            	{!! Form::submit(strtoupper(trans('terms.save_changes')), array('class' => 'signin-btn bg-primary'))!!}
							            </div>
							        </div>
					        	</div>


					        	<div role="tabpanel" class="tab-pane clearfix" id="subscriptions">
                                    <div class="col-xs-12">
							            <table id="history_tbl" class="table table-bordered table-striped">
						                    <thead>
						                    	<tr>
							                        <th>Plan</th>
							                        <th>Start on</th>
							                        <th>End on</th>
							                        <th>Status</th>
						                      	</tr>
						                    </thead>
						                    <tbody>
						                    	@foreach ($user->subscriptions as $subs)
								                <tr>
								                    <td>{{ $subs->role->name }}</td>
								                    <td>{{ $subs->start_date }}</td>
								                    <td>{{ $subs->end_date }}</td>
								                    <td>{{ $subs->status }}</td>
								                </tr>
								            	@endforeach

								            	@if(empty($user->subscriptions))
								            	<tr>
								            		<td colspan="4">No subscription record.</td>
								            	</tr>
								            	@endif
						                    </tbody>
					                    </table>
					                </div>
                                </div>

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
<script src="{{ asset('plugins/select2/select2.full.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>
@append