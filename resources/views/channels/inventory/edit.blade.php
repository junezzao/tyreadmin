@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
@append

@section('title')
	@lang('product-management.page_title_channel_mgmt_inventory')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('product-management.content_header_channel_inventory_mgmt')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="product-name">
						{{ $product_name }}
						</h3>
	              		@if($user->can('edit.product'))
							<a href="{{route('products.inventory.edit', ['id'=>$product_id, 't' => 'product'])}}" target="_blank" class="btn btn-default pull-right">Go to Product</a>
	            		@endif
	              		<div>
                            <i>{{$brand_name." By ".$merchant_name }}</i>
                        </div>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		@if (count($errors) > 0)
						    <div class="alert alert-danger">
						        <ul>
						            @foreach ($errors->all() as $error)
						                <li>{{ $error }}</li>
						            @endforeach
						        </ul>
						    </div>
						@endif
	            		{!! Form::open(array('url' => route('channels.inventory.update', $product_id), 'method' => 'PUT')) !!}
	            		<table id="skus_list" class="table table-bordered table-striped">
		                    <thead>
		                    	<tr>
									<th style="width:15%;">@lang('product-management.channel_inventory_table_header_sku_details')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_options')</th>
									<th style="width:5%;">@lang('product-management.channel_inventory_table_header_quantity')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_status')</th>
									<th style="width:8%;">@lang('product-management.channel_inventory_table_header_live_price')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_unit_price')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_sale_price')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_sale_price_start')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_sale_price_end')</th>
									<th style="width:15%;">@lang('product-management.channel_inventory_table_header_coordinates')</th>
									<th style="width:10%;">@lang('product-management.channel_inventory_table_header_weight')</th>
		                    	</tr>
		                    </thead>

		                    <tbody>
		                    	@foreach($items as $item)
		                    		<tr>
			                    		<td>{!! $item['sku_details'] !!}</td>
			                    		<td>{!! $item['options'] !!}</td>
			                    		<td align="center">{{ $item['quantity'] }}</td>
			                    		<td>{!! Form::select('status[' . $item['channel_sku_id'] . ']', $statuses, $item['status'], ['class' => 'form-control select2-nosearch']) !!}</td>
			                    		<td>{!! $item['live_price'] !!}</td>
			                    		<td>{!! Form::number('unit_price[' . $item['channel_sku_id'] . ']', $item['unit_price'], ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}</td>
			                    		<td>{!! Form::number('sale_price[' . $item['channel_sku_id'] . ']', $item['sale_price'], ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}</td>
			                    		<td>{!! Form::text('sale_start_date[' . $item['channel_sku_id'] . ']', $item['sale_start_date'], ['class' => 'form-control datepicker']) !!}</td>
			                    		<td>{!! Form::text('sale_end_date[' . $item['channel_sku_id'] . ']', $item['sale_end_date'], ['class' => 'form-control datepicker']) !!}</td>
			                    		<td>{!! Form::text('coordinates[' . $item['channel_sku_id'] . ']', $item['coordinates'], ['class' => 'form-control']) !!}</td>
			                    		<td>{{ $item['weight'] }}</td>
			                    	</tr>
		                    	@endforeach
		                    </tbody>
	                    </table>

	           			<button type="submit" class="btn btn-default pull-right">@lang('product-management.button_channel_inventory_save')</button>
	                    {!! Form::close() !!}
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datepicker/datepicker3.css', env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js', env('HTTPS',false)) }}" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('.datepicker').datepicker({
            format :  'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
});
</script>

@append