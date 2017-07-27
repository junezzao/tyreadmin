@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/user.page_title_access_management')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/user.content_header_access_management')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/user.box_header_role_create')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<!-- Nav tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('admin/user.tab_title_details')</a></li>
                                <li role="presentation"><a href="#permissions" aria-controls="permissions" role="tab" data-toggle="tab">@lang('admin/user.tab_title_permissions')</a></li>
                            </ul>

                            <!-- Tab panes -->
                            {!! Form::open(array('url' => route('admin.roles.store'), 'method' => 'POST')) !!}
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                    <div class="col-xs-12">
                                    	<div class="col-xs-8">
	                                    	<div class="form-group">
					            				<label class="col-xs-3 control-label required" for="name">@lang('admin/user.role_form_label_name')</label>
					            				<div class="col-xs-9">
			   										{!! Form::text( 'name', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.role_form_label_name')] ) !!}
			   										<div class="error">{{ $errors->first('name') }}</div>
								                </div>
								            </div>

								            <div class="form-group">
					            				<label class="col-xs-3 control-label required" for="slug">@lang('admin/user.role_form_label_slug')</label>
					            				<div class="col-xs-9">
			   										{!! Form::text( 'slug', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.role_form_label_slug')] ) !!}
			   										<div class="error">{{ $errors->first('slug') }}</div>
								                </div>
								            </div>

								            <div class="form-group">
					            				<label class="col-xs-3 control-label required" for="level">@lang('admin/user.role_form_label_level')</label>
					            				<div class="col-xs-3">
			   										{!! Form::number( 'level', null, ['class' => 'form-control', 'min' => 1, 'max' => (Auth::user()->level() - 1), 'placeholder' => trans('admin/user.role_form_label_level')] ) !!}
			   										<div class="error">{{ $errors->first('level') }}</div>
								                </div>
								            </div>

								            <div class="form-group">
					            				<label class="col-xs-3 control-label required" for="status">@lang('admin/user.role_form_label_status')</label>
					            				<div class="col-xs-3">
					            					{!! Form::select('status', $statuses, 'Active', array('class' => 'form-control select2-nosearch')) !!}
			   										<div class="error">{{ $errors->first('status') }}</div>
								                </div>
								            </div>

								            <div class="form-group">
					            				<label class="col-xs-3 control-label" for="description">@lang('admin/user.role_form_label_description')</label>
					            				<div class="col-xs-9">
			   										{!! Form::text( 'description', null, ['class' => 'form-control', 'placeholder' => trans('admin/user.role_form_label_description')] ) !!}
			   										<div class="error">{{ $errors->first('description') }}</div>
								                </div>
								            </div>
								        </div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane clearfix" id="permissions">
                                	<div id="permission-panels" class="col-xs-12">
                                		@foreach ($permissions as $group => $permissionItems)
											<div class="col-xs-4">
												<div class="panel panel-default">
												    <div class="panel-heading" id="heading{{$group}}">
												          	{{ $group }}
												    </div>

												    <div id="{{$group}}">
												      	<div class="panel-body custom-panel-body">
													      	@foreach($permissionItems as $p)
													      		<div class="col-xs-9">
													      			{{ $p['name'] }}
													      			@if (!empty($p['description']))
													      				<span class="help-block">&mdash; {{ $p['description'] }}</span>
													      			@endif
													      		</div>

													      		<div class="col-xs-3">
													      			<div class="onoffswitch">
														      			<input type="checkbox" name="permission_ids[]" value="{{ $p['id'] }}" class="onoffswitch-checkbox" id="js-permission-id-{{ $p['id'] }}">
														      			<label class="onoffswitch-label" for="js-permission-id-{{ $p['id'] }}">
														      				<span class="onoffswitch-inner"></span>
														      				<span class="onoffswitch-switch"></span>
														      			</label>
														      		</div>
													      		</div>
													        @endforeach
												      	</div>
												    </div>
											    </div>
											</div>
										@endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
				         		<div class="form-group pull-right">
					            	<button type="submit" class="btn btn-default">@lang('admin/user.button_create_role')</button>
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
<script type="text/javascript">
	$(document).ready(function(){

	});
</script>
@append