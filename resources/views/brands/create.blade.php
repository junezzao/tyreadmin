@extends('layouts.master')

@section('title')
	@lang('brands.page_title_brands_create')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('brands.content_header_brands')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('brands.box_header_brands_create')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('brands.store'), 'method' => 'POST')) !!}
	            			<div class="col-xs-12">
		            			<div class="col-xs-6">
			            			<div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="name">@lang('brands.brands_form_label_name')</label>
			            				<div class="col-xs-9">
	   										{!! Form::text( 'name', null, ['class' => 'form-control', 'placeholder' => trans('brands.brands_form_placeholder_name')] ) !!}
	   										<div class="error">{{ $errors->first('name') }}</div>
						                </div>
						            </div>

						            <div id="merchant-field" class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="merchant">@lang('brands.brands_form_label_merchant')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('merchant_id', $merchants, (!empty($selectedMerchant)?$selectedMerchant:null), array('class' => 'form-control select2', 'placeholder' => trans('brands.brands_form_placeholder_merchant'))) !!}
						                	<div class="error">{{ $errors->first('merchant_id') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="prefix">@lang('brands.brands_form_label_prefix')</label>
			            				<div class="col-xs-9">
			            					{!! Form::text( 'prefix', null, ['class' => 'form-control', 'placeholder' => trans('brands.brands_form_placeholder_prefix')] ) !!}
			            					<div class="help-text">{{trans('brands.brands_form_help_text_prefix')}}</div>
						                	<div class="error">{{ $errors->first('prefix') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="merchant">@lang('brands.brands_form_label_status')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('active', $statuses, null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('brands.brands_form_placeholder_status'))) !!}
						                	<div class="error">{{ $errors->first('active') }}</div>
						                </div>
						            </div>   
					            </div>				
				         	</div>

				         	<div class="col-xs-12">
				         		<div class="form-group pull-right">
					               <button type="submit" id="btn_create_new_user" class="btn btn-default">@lang('brands.button_create_new_brand')</button>
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
<style type="text/css">
	textarea {
		resize: none;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		var selectedMerchant = "<?php echo(!empty($selectedMerchant) ? $selectedMerchant : ''); ?>";
		if (selectedMerchant != '') {
			$("#merchant-field").hide();
		}
	});
</script>
@append