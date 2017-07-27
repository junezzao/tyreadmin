@extends('layouts.master')

@section('title')
	@lang('brands.page_title_brands_edit')
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
	              		<h3 class="box-title">@lang('brands.box_header_brands_edit')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('brands.update', $id), 'method' => 'PUT')) !!}
	            			<div class="col-xs-12">
		            			<div class="col-xs-6">
			            			<div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="name">@lang('brands.brands_form_label_name')</label>
			            				<div class="col-xs-9">
	   										{!! Form::text( 'name', $brand->name, ['class' => 'form-control', 'placeholder' => trans('brands.brands_form_placeholder_name')] ) !!}
	   										<div class="error">{{ $errors->first('name') }}</div>
						                </div>
						            </div>

						            <div id="merchant-field" class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="merchant">@lang('brands.brands_form_label_merchant')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('merchant_id', $merchants, (!empty($selectedMerchant)?$selectedMerchant:$brand->merchant_id), array('class' => 'form-control select2', 'placeholder' => trans('brands.brands_form_placeholder_merchant'))) !!}
						                	<div class="error">{{ $errors->first('merchant_id') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="prefix">@lang('brands.brands_form_label_prefix')</label>
			            				<div class="col-xs-9">
			            					{!! Form::text( 'prefix', $brand->prefix, ['disabled','class' => 'form-control', 'placeholder' => trans('brands.brands_form_placeholder_prefix')] ) !!}
						                	<div class="error">{{ $errors->first('prefix') }}</div>
						                </div>
						            </div>

						            <div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="merchant">@lang('brands.brands_form_label_status')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('active', $statuses, $brand->active, array('class' => 'form-control select2-nosearch')) !!}
						                	<div class="error">{{ $errors->first('status') }}</div>
						                </div>
						            </div>
					            
					            </div>				
				         	</div>

				         	<div class="col-xs-12">
				         		<div class="form-group pull-right">
					               <button type="submit" id="btn_create_new_user" class="btn btn-default">@lang('brands.button_update_brand')</button>
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
		/*

		$("[name=brands_category]").change(function() {
			checkSelectedUserCategory($(this).val());
		});

		function checkSelectedUserCategory (brands_category) {
			var brands_category_with_merchant = ['clientadmin', 'clientuser'];
			if (brands_category != '') {
				if (jQuery.inArray( brands_category, brands_category_with_merchant ) < 0) {
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
		}*/
	});
</script>
@append