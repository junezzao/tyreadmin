@extends('layouts.master')

@section('header_scripts')

@append

@section('title')
	@lang('test.admin_test')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('test.testing')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('test.testing')</h3>
	              		<p>@lang('test.page_description')</p>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<a href="{{ route('admin.testing.run_syncs') }}">@lang('test.label_run_sync')</a><br>
	            		<a href="{{ route('admin.testing.show_admin_error_log') }}">@lang('test.label_admin_log')</a><br>
	            		<a href="{{ route('admin.testing.show_hapi_logs') }}">@lang('test.label_hapi_log')</a><br>
	            		<a href="{{ route('admin.testing.generate_stats') }}">@lang('test.label_gen_dashboard')</a><br>
	            		<a href="{{ route('admin.testing.phpinfo') }}">@lang('test.label_php_info')</a><br/>
	            		<div class="no-padding col-xs-2">
	            			<input class="form-control form-inline" placeholder=@lang('test.form_placeholder_input_id') name="product_sku_id" type="text" />
	            			<input type="radio" name="by_type" value="product" checked> @lang('test.label_product')
							<input type="radio" name="by_type" value="sku"> @lang('test.label_sku')
	            		</div><a id="stock_movements" class="form-inline" href="#">@lang('test.label_get_stock')</a><br/>
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop


@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
	$('#stock_movements').on('click', function() {
		if ($('input[name=product_sku_id]').val() == '') {
			alert('Please enter product or sku id.');
			return false;
		}

		$(this).attr('href', '{{ config("app.url") }}' + 'admin/testing/stock_movement/' + $('input[name=by_type]:checked').val() + '/' + $('input[name=product_sku_id]').val());
	});
});
</script>
@append