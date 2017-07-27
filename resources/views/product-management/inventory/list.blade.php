@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
@append

@section('title')
	@lang('product-management.page_title_product_mgmt_inventory')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('product-management.content_header_product_mgmt')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('product-management.box_header_invenrory')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<div class="col-xs-12 no-left-padding no-right-padding" style="padding-bottom: 0px;">
	            			<!-- Filters -->
	            			{!! Form::open(array('id' => 'search', 'role'=> 'form', 'method'=>'GET')) !!}
	            				<div class="col-xs-12 no-right-padding no-left-padding">
		            				<div class="col-xs-5 no-left-padding">
   										{!! Form::text( 'keyword', null, ['class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_keyword')] ) !!}
					                </div>
							        <button type="submit" id="btn_search" class="btn btn-default">@lang('product-management.button_inventory_filter_search')</button>
							        <button type="button" id="btn_advance_search" class="btn btn-link">@lang('product-management.inventory_filter_advance_filters_link')</button>

							        <div id="options" class="pull-right">
							        	<button type="button" id="btn_select_all" class="btn btn-default">@lang("product-management.button_inventory_filter_select_all")</button>
										<button type="button" id="btn_unselect_all" class="btn btn-default">@lang("product-management.button_inventory_filter_unselect_all")</button>
										<button type="button" id="btn_selected" class="btn btn-default">@lang("product-management.button_inventory_filter_selected") <span class="badge info" id="count-selected">0</span></button>
										@if($admin->can('edit.product'))
										<div class="dropdown">
											<button type="button" id="btn_misc_options" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												@if($admin->can('delete.product'))
												<li><a href="#" class="bulk-action" data-action="delete">Delete Product</a></li>
												@endif
												<li><a href="#" class="bulk-action" data-action="export">@lang('product-management.inventory_export')</a></li>
												<li><a href="#" class="bulk-action" data-action="reject">Reject SKU Quantity</a></li>
												<li><a href="#" class="bulk-action" data-action="update">Bulk Update</a></li>
												<li><a href="#" class="bulk-action" data-action="print-barcode">Print Barcode</a></li>
											</ul>
										</div>
										@endif
							        </div>
						        </div>

						        <div id="advance-filters" class="col-xs-12 no-left-padding no-right-padding">
						        	<h5>@lang('product-management.inventory_filter_advance_filters_link')</h5>
						        	<fieldset>
						        		<div class="col-xs-12">
						        			<label class="control-label pull-left">@lang('product-management.inventory_filter_label_price_range')</label>
				            				<div class="col-xs-1" style="padding-right: 5px;">
		   										{!! Form::number( 'min_price', null, ['class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_min')] ) !!}
							                </div>
							                <div class="col-xs-1" style="padding-left: 5px;">
		   										{!! Form::number( 'max_price', null, ['class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_max')] ) !!}
							                </div>
							                <label class="control-label pull-left" style="padding-right: 10px;">@lang('product-management.inventory_filter_label_coordinate')</label>

							                <div class="percent-20">
				            					{!! Form::text('coordinate', null,  array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_coordinate'))) !!}
							                </div>
							                <!-- <div class="percent-20">
				            					{!! Form::select('gst', [], null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_gst'))) !!}
							                </div> -->
							                <div class="percent-20">
				            					{!! Form::select('category_id', $categories, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_categories'))) !!}
							                </div>
							                <div class="percent-20">
							          			<label style="font-weight:500;">{!! Form::checkbox('no_image', 1, null, array('class' => 'inventory-filter')) !!} @lang('product-management.inventory_filter_checkbox_no_image')</label>
											</div>
						        		</div>
						        		<div class="col-xs-12">
							        		<div class="percent-20">
				            					{!! Form::select('channel', $channel_by_types, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_channel'))) !!}
							                </div>

							                @if(!$hide_merchant)
							                <div class="percent-20">
				            					{!! Form::select('merchant', $merchants, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_merchant'))) !!}
							                </div>
							                @endif

							                <div class="percent-20">
				            					{!! Form::select('supplier', $suppliers, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_supplier'))) !!}
							                </div>
							                <div class="percent-20">
				            					{!! Form::select('status', $statuses, 1, array('class' => 'form-control inventory-filter select2-nosearch', 'placeholder' => trans('product-management.inventory_filter_placeholder_status'))) !!}
							                </div>
							            </div>
							            <div class="col-xs-12">
							        		<div class="percent-20">
				            					{!! Form::select('stock_status', $stock_statuses, null, array('class' => 'form-control inventory-filter select2-nosearch', 'placeholder' => trans('product-management.inventory_filter_placeholder_stock_status'))) !!}
							                </div>
							                <div id="div-tags">
				            					{!! Form::text('tags', null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_tags'))) !!}
							                </div>
							                <div class="percent-20">
				            					{!! Form::number('procurement_batch', null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_batch'))) !!}
							                </div>

							                <!-- <div class="percent-20">
				            					{!! Form::select('sort_by', [], null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_sort_by'))) !!}
							                </div> -->
							                <div class="col-xs-2 pull-right no-right-padding">
							           			<button type="button" id="btn_reset_filter" class="btn btn-default pull-right">@lang('product-management.button_inventory_filter_reset')</button>
							                </div>
							            </div>
						        	</fieldset>
						        </div>
	            			{!! Form::close() !!}
	            		</div>

	            		<div id="div_items_list" class="inventory-list-div">
		            		<table id="items_list" class="table table-bordered table-striped inventory_list">
			                    <thead>
			                    	<tr>
										<th></th>
										<th></th>
			                    	</tr>
			                    </thead>

			                    <tbody>
			                    </tbody>
		                    </table>
		                </div>

		                <div id="div_selected_list" class="inventory-list-div">
		                    <table id="selected_list" class="table table-bordered table-striped inventory_list">
			                    <thead>
			                    	<tr>
										<th></th>
										<th></th>
			                    	</tr>
			                    </thead>

			                    <tbody>
			                    </tbody>
		                    </table>
		                </div>
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop


@section('footer_scripts')
<!-- for autocomplete -->
<link href="{{ asset('plugins/jquery-ui-1.12.0/jquery-ui.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/jquery-ui-1.12.0/jquery-ui.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<!-- for tags -->
<link href="{{ asset('plugins/jQuery-tagEditor-master/jquery.tag-editor.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/jQuery-tagEditor-master/jquery.tag-editor.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('plugins/caret-master/jquery.caret.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script type="text/javascript">

jQuery(document).ready(function(){
	var checked = new Array;
	var unchecked = new Array;
	var selected_list_data = new Array;
	var available_keywords = {!! json_encode($keywords) !!};
	var available_tags = {!! json_encode($tags) !!};
	var status = $("select[name='status'] option:selected").val();

	@if(isset($selectedProds['data']))
		var selected_prods = {!! json_encode($selectedProds['data']) !!};
	@else
		var selected_prods = '';
	@endif

	$("#advance-filters").hide();

	$("#btn_advance_search").on('click', function() {
		if ($("#advance-filters").is(":visible")) {
			$("#advance-filters").hide();
		}
		else {
			$("#advance-filters").show();
		}
	});

	$("input[name='keyword']").autocomplete({
		source: available_keywords,
		minLength: 3
	}).keyup(function (e) {
        if(e.which === 13) {
            $(".ui-autocomplete").hide();
        }
    }).focus();

	$("input[name='tags']").tagEditor({
		autocomplete: {
			source: available_tags,
			minLength: 3
		},
		placeholder: "Tags",
		delimiter: '|',
		forceLowercase: false,
		onChange: function (field, editor, tags) {
			$.each(tags, function () {
				if(jQuery.inArray(this.toString(), available_tags) < 0) {
					$("input[name='tags']").tagEditor('removeTag', this);
				}
			});
		}
	});

	$('ul.tag-editor').addClass('form-control');

	$("#btn_reset_filter").on('click', function () {
		$(".inventory-filter").val('').trigger('change');
		$("input[name='no_image']:checkbox").prop('checked', false);

		$("input[name='tags']").next('.tag-editor').find('.tag-editor-delete').click();

		$("#search").submit();
	});

	var channel_id = '{{ $channel_id }}';
	var category_id = '{{isset($_GET["category_id"]) ? $_GET["category_id"] : ""}}';
	console.log(category_id);
    var items_list = $('#items_list').DataTable({
	 	"autoWidth": false,
		"pageLength": 20,
		"dom": '<"clearfix"ip>t<"clearfix"ip>',
		"processing": false,
		"serverSide": true,
		"ajax": '{{ route("inventory.search") }}?status=' + status + (channel_id.length > 0 ? '&channel='+channel_id : '') + (category_id.length > 0 ? '&category_id='+category_id : ''),
		"columns":[
			{ "name": "content", "targets": 0, "className": "item-content" }
		],
		"rowCallback": function( row, data, index ) {
			var product_data = jQuery.parseJSON(data[1]);
			var index = checked.indexOf(product_data['id'] + "");

			if(index >= 0){
				$('.inventory-item', row).addClass('selected');
			}
		},
		"preDrawCallback" : function ( settings ) {
			waitingDialog.show('Retrieving products....', {dialogSize: 'sm'});
		},
		"drawCallback": function( settings ) {
			$('#items_list .inventory-item .img').on('click', function() {
				var item = $(this).closest('.inventory-item');
				item.toggleClass('selected');

				var product_id = item.children('.chk_select').val();
				var index = checked.indexOf(product_id + "");
				var row_index = items_list.row($(this).closest('tr')).index();

				if(index < 0) {
					checked.push(product_id);
					selected_list_data[product_id] = items_list.row($(this).closest('tr')).data();
				}
				else {
					checked.splice(index, 1);
					delete selected_list_data[product_id];
				}

				$('#count-selected').text(checked.length);
			});

			waitingDialog.hide();
			$("input[name='keyword']").focus().select();
		},
	});

	jQuery('#items_list').offset().top;

	var selected_list = $('#selected_list').DataTable({
	 	"autoWidth": false,
		"pageLength": 20,
		"dom": '<"clearfix"ip>t<ip>',
		"columns":[
			{ "name": "content", "targets": 0, "className": "item-content" }
		],
		"rowCallback": function( row, data, index ) {
			$('.inventory-item', row).addClass('selected');
		},
		"drawCallback": function( settings ) {
			$('#selected_list .inventory-item .img').on('click', function() {
				var item = $(this).closest('.inventory-item');
				item.removeClass('selected');

				var product_id = item.children('.chk_select').val();
				var index = checked.indexOf(product_id + "");
				// console.log(checked);
				// console.log(product_id);
				// console.log(index);
				if (index >= 0) {
					checked.splice(index, 1);

					selected_list.row($(this).closest('tr')).remove().draw();
					delete selected_list_data[product_id];

					unchecked.push(product_id);
				}

				$('#count-selected').text(checked.length);
			});
		},
	});

	$("#btn_select_all").on('click', function() {
		$("#btn_unselect_all").show();

		$('#items_list .inventory-item .img').each(function() {
			var item = $(this).closest('.inventory-item');

			if (!item.hasClass('selected')) {
				$(this).click();
			}
		});
	});

	$("#btn_unselect_all").on('click', function() {
		$("#btn_select_all").show();

		$('#items_list .inventory-item .img').each(function() {
			var item = $(this).closest('.inventory-item');

			if (item.hasClass('selected')) {
				$(this).click();
			}
		});
	});

	if(selected_prods.length > 0){
		for(var key in selected_prods){
			selected_list.row.add(selected_prods[key]);
		}

		// add into selected_list_data
		$.each(selected_prods, function( index, value ) {
			if(!(value === undefined)){
				var productObj = jQuery.parseJSON(value[1]);
				selected_list_data[productObj.id] = value;
				checked.push(productObj.id + "");
			}
		});

		// console.log(selected_list_data);

		selected_list.draw();

		$('#count-selected').text(checked.length);

		$('#div_items_list').toggle();
		$('#div_selected_list').toggle();

		if ($("#div_selected_list").is(":visible")) {
			$("#btn_select_all").hide();
			$("#btn_unselect_all").hide();
		}

		$('#selected_list .inventory-item .img').each(function(){
			$(this).addClass('selected');
		});
	}

	$("#btn_selected").on('click', function() {
		$('#div_items_list').toggle();
		$('#div_selected_list').toggle();

		if ($("#div_selected_list").is(":visible")) {
			$("#btn_select_all").hide();
			$("#btn_unselect_all").hide();
			selected_list.clear();

			if (selected_list_data.length > 0) {
				for (var key in selected_list_data) {
					selected_list.row.add(selected_list_data[key]);
				}

				selected_list.draw();
			}
		}

		if ($("#div_items_list").is(":visible")) {
			$("#btn_select_all").show();

			if (unchecked.length > 0) {
				$.each(unchecked, function () {
					 var checkbox = $('#items_list .chk_select[value="' + this + '"]');

					 if (checkbox != null) {
					 	checkbox.closest('.inventory-item').removeClass('selected');
					 }
				});

				unchecked = new Array;
			}
		}
	});

	$("#search").submit(function(event) {
    	event.preventDefault();

    	var params = $("#search .inventory-filter:visible").filter(function () {
			if ($(this).val() !== "") {
				return this;
			}
		}).serialize();
    	console.log(params);
    	items_list.ajax.url("{{route('inventory.search')}}?" + params).load();
    });

    $('.bulk-action').on('click', function(){
    	var action = $(this).data('action');
    	//console.log(action);
    	switch(action) {
		    case 'export':
		    	$form = $('<form id="reject-form" action="{{route('inventory.export')}}" method="POST"></form>');
		    	$.each(selected_list_data, function( index, value ) {
					if(!(value === undefined)){
						productObj = jQuery.parseJSON(value[1]);
						var input = $('<input type="hidden" name="products[]"></input>');
						input.val(JSON.stringify(productObj));
						$form.append(input);
					}
				});
				$('body').append($form);
				$form.submit();
		        break;
		    case 'delete':
		        $form = $('<form id="reject-form" action="{{route('inventory.delete.create')}}" method="POST"></form>');
		    	$.each(selected_list_data, function( index, value ) {
					if(!(value === undefined)){
						productObj = jQuery.parseJSON(value[1]);
						var input = $('<input type="hidden" name="products[]"></input>');
						input.val(JSON.stringify(productObj));
						$form.append(input);
					}
				});
				$('body').append($form);
				$form.submit();
		        break;
		    case 'reject':
		    	$form = $('<form id="reject-form" action="{{route('inventory.reject.create')}}" method="POST"></form>');
		    	$.each(selected_list_data, function( index, value ) {
					//var product_obj = jQuery.parseJSON(value);
					if(!(value === undefined)){
						productObj = jQuery.parseJSON(value[1]);
						var input = $('<input type="hidden" name="products['+productObj.id+']"></input>');
						input.val(JSON.stringify(productObj));
						$form.append(input);
					}
				});
				$('body').append($form);
				$form.submit();
		        break;
		    case 'update':
		    	$form = $('<form id="bulk-update-form" action="{{route('inventory.bulk_update')}}" method="POST"></form>');
		    	$.each(selected_list_data, function( index, value ) {
					//var product_obj = jQuery.parseJSON(value);
					if(!(value === undefined)){
						//console.log(value);
						productObj = jQuery.parseJSON(value[1]);
						var input = $('<input type="hidden" name="products[]"></input>');
						input.val(productObj.id);
						$form.append(input);
					}
				});
				$('body').append($form);
				$form.submit();
		        break;
		    case 'print-barcode':
		    	$form = $('<form id="bulk-update-form" action="{{route('inventory.print_barcode')}}" method="POST"></form>');
		    	$.each(selected_list_data, function( index, value ) {
					if(!(value === undefined)){
						productObj = jQuery.parseJSON(value[1]);
						var input = $('<input type="hidden" name="products[]"></input>');
						input.val(productObj.id);
						$form.append(input);
					}
				});
				$('body').append($form);
				$form.submit();
		        break;
		};
    });
});
</script>
@append