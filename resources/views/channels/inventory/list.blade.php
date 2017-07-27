@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
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
	              		<h3 class="box-title">@lang('product-management.box_header_invenrory')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<div class="col-xs-12 no-left-padding no-right-padding" style="padding-bottom: 0px;">
	            			<!-- Filters -->
	            			{!! Form::open(array('id' => 'search', 'role'=> 'form', 'method'=>'GET')) !!}
	            				<div class="col-xs-12 no-right-padding no-left-padding">
		            				<div class="col-xs-6 no-left-padding no-right-padding">
			            				<div class="col-xs-8 no-left-padding">
	   										{!! Form::text( 'keyword', null, ['class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_keyword')] ) !!}
						                </div>
								        <button type="submit" id="btn_search" class="btn btn-default">@lang('product-management.button_inventory_filter_search')</button>
								        <button type="button" id="btn_advance_search" class="btn btn-link">@lang('product-management.inventory_filter_advance_filters_link')</button>
						       		</div>
							        <div id="options" class="pull-right col-xs-6 no-left-padding no-right-padding">
							        	@if(!$admin->is('channelmanager'))
							        		<select class="form-control inventory-filter select2" name="channel">
							        			@foreach($channel_by_types as $channel_type=>$channels)
							        				<optgroup label="{{$channel_type}}">
							        					@foreach($channels as $channel)
							        						<option value="{{ $channel['id'] }}" data-channel-type="{{ $channel['type'] }}" data-third-party="{{ $channel['third_party'] }}">{{ $channel['name'] }}</option>
							        					@endforeach
							        				</optgroup>
							        			@endforeach
							        		</select>
							        	@else
							        		<select class="form-control inventory-filter hide" name="channel">
							        			@foreach($channel_by_types as $channel_type=>$channels)
								        			<optgroup label="{{$channel_type}}">
								        			@foreach($channels as $channel)
								        				<option value="{{ $channel['id'] }}" data-channel-type="{{ $channel['type'] }}" data-third-party="{{ $channel['third_party'] }}">{{ $channel['name'] }}</option>
								        			@endforeach
								        			</optgroup>
							        			@endforeach
							        		</select>
							        	@endif
							        	<button type="button" id="btn_select_all" class="btn btn-default">@lang("product-management.button_inventory_filter_select_all")</button>
										<button type="button" id="btn_unselect_all" class="btn btn-default">@lang("product-management.button_inventory_filter_unselect_all")</button>
										<button type="button" id="btn_selected" class="btn btn-default">@lang("product-management.button_inventory_filter_selected") <span class="badge info" id="count-selected">0</span></button>
										<div class="dropdown">
											<button type="button" id="btn_misc_options" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="#" id="btn_export_product_list">@lang("product-management.channel_inventory_option_export_channel_product_list")</a></li>
												<li><a href="#" class="bulk-update third-party-only" data-action="assign_categories">@lang("product-management.channel_inventory_option_assign_categories")</a></li>
												<li><a href="#" class="bulk-update" data-action="update_products">@lang("product-management.channel_inventory_option_update_products")</a></li>
												@if($admin->is('channelmanager'))
													<li><a href="#" class="sync-products third-party-only" data-url="{{ route('byChannel.channel.inventory.sync_products', ['channel_id'=>$channel_id, 'type'=>'create']) }}">@lang("product-management.channel_inventory_option_sync_new_products")</a></li>
													<li><a href="#" class="sync-products third-party-only" data-url="{{ route('byChannel.channel.inventory.sync_products', ['channel_id'=>$channel_id, 'type'=>'update']) }}">@lang("product-management.channel_inventory_option_sync_existing_products")</a></li>
												@else
													<li><a href="#" class="sync-products third-party-only" data-url="{{ route('channel.inventory.sync_products', 'create') }}">@lang("product-management.channel_inventory_option_sync_new_products")</a></li>
													<li><a href="#" class="sync-products third-party-only" data-url="{{ route('channel.inventory.sync_products', 'update') }}">@lang("product-management.channel_inventory_option_sync_existing_products")</a></li>
												@endif
											</ul>
										</div>
							        </div>
						        </div>

						        <div id="advance-filters" class="col-xs-12 no-left-padding no-right-padding">
						        	<h5>@lang('product-management.inventory_filter_advance_filters_link')</h5>
						        	<fieldset>
						        		<div class="col-xs-12">
						        			<label class="control-label percent-10">@lang('product-management.inventory_filter_label_price_range')</label>
				            				<div class="percent-15">
		   										{!! Form::number( 'min_price', null, ['class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_min')] ) !!}
							                </div>
							                <div class="percent-15">
		   										{!! Form::number( 'max_price', null, ['class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_max')] ) !!}
							                </div>
							                <!-- <div class="percent-20">
				            					{!! Form::select('gst', [], null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_gst'))) !!}
							                </div> -->
							                <div class="percent-20">
				            					{!! Form::select('category_id', $categories, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_categories'))) !!}
							                </div>
											<div class="percent-20">
				            					{!! Form::number('procurement_batch', null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_batch'))) !!}
							                </div>
							                <div id="sync_status_filter" class="percent-20 third-party-only">
				            					{!! Form::select('sync_status', $sync_statuses, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_sync_status'))) !!}
							                </div>
							                <div class="percent-15">
							          			<label style="font-weight:500;">{!! Form::checkbox('no_image', 1, null, array('class' => 'inventory-filter')) !!} @lang('product-management.inventory_filter_checkbox_no_image')</label>
											</div>
						        		</div>
						        		<div class="col-xs-12">
						        			 @if(!$hide_merchant)
							                <div class="percent-20">
				            					{!! Form::select('merchant', $merchants, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_merchant'))) !!}
							                </div>
							                @endif
							                <div class="percent-20">
				            					{!! Form::select('supplier', $suppliers, null, array('class' => 'form-control inventory-filter select2', 'placeholder' => trans('product-management.inventory_filter_placeholder_supplier'))) !!}
							                </div>
							                <div class="percent-20">

				            					{!! Form::select('channel_sku_active', $statuses, 1, array('class' => 'form-control inventory-filter select2-nosearch', 'placeholder' => trans('product-management.inventory_filter_placeholder_status'))) !!}
							                </div>
							                <div class="percent-20">
				            					{!! Form::select('stock_status', $stock_statuses, null, array('class' => 'form-control inventory-filter select2-nosearch', 'placeholder' => trans('product-management.inventory_filter_placeholder_stock_status'))) !!}
							                </div>
							                <div class="col-xs-2 pull-right no-right-padding">
							           			<button type="button" id="btn_reset_filter" class="btn btn-default pull-right">@lang('product-management.button_inventory_filter_reset')</button>
							                </div>
							            </div>
							            <!-- <div class="col-xs-12">

							                <div class="percent-20">
				            					{!! Form::select('sort_by', [], null, array('class' => 'form-control inventory-filter', 'placeholder' => trans('product-management.inventory_filter_placeholder_sort_by'))) !!}
							                </div>

							            </div> -->
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
<script type="text/javascript">
jQuery(document).ready(function(){
	var checked = new Array;
	var unchecked = new Array;
	var selected_list_data = new Array;
	var available_keywords = {!! json_encode($keywords) !!};
	var default_channel = $("select[name='channel'] option:selected").val();
	var default_channel_type = '';
	var default_is_third_party = '';
	var channel_sku_active = $("select[name='channel_sku_active'] option:selected").val();

	@if(isset($selectedProds['data']))
		var selected_prods = {!! json_encode($selectedProds['data']) !!};
	@else
		var selected_prods = '';
	@endif

	checkChannelIsThirdparty();

	if (default_channel == '') {
		$("select[name='channel']").prop('disabled', true);
		$("input[name='keyword']").prop('disabled', true);
		$("#btn_advance_search").prop('disabled', true);
		$("#btn_select_all").prop('disabled', true);
		$("#btn_selected").prop('disabled', true);
		$("#btn_search").prop('disabled', true);
		$("#btn_misc_options").prop('disabled', true);
	}
	else {
		default_channel_type = $("select[name='channel'] option:selected").attr('data-channel-type');
		default_is_third_party = $("select[name='channel'] option:selected").attr('data-third-party');
	}

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

	$("#btn_reset_filter").on('click', function () {
		$(".inventory-filter:not(select[name='channel'])").val('').trigger('change');
		$("input[name='no_image']:checkbox").prop('checked', false);

		$("#search").submit();
	});

	var channel_id = '{{ $channel_id }}';
	if(channel_id.length > 0) {
		default_channel = channel_id;
	}

    var items_list = $('#items_list').DataTable({
	 	"autoWidth": false,
		"pageLength": 20,
		"dom": '<"clearfix"ip>t<ip>',
		"processing": false,
		"serverSide": true,
		"ajax": '{{ route("channel.inventory.search") }}?channel=' + default_channel + '&channel_type=' + default_channel_type + '&third_party=' + default_is_third_party + '&channel_sku_active=' + channel_sku_active,
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

	$("select[name='channel']").on('change', function () {
		checkChannelIsThirdparty();

		var params = $("#search .inventory-filter:visible").filter(function () {
			if ($(this).val() !== "") {
				return this;
			}
		}).serialize();

		var channel_type = $("select[name='channel'] option:selected").attr('data-channel-type');
		var is_third_party = $("select[name='channel'] option:selected").attr('data-third-party');

		checked = new Array;
		$('#count-selected').text(checked.length);
		selected_list_data = new Array;
		selected_list.clear();
		selected_list.draw();

    	items_list.ajax.url("{{route('channel.inventory.search')}}?" + params + '&channel_type=' + channel_type + '&third_party=' + is_third_party).load();
	});

	if(selected_prods.length > 0){
		@if(isset($cid))
			var cid = {{ $cid }};
			$("select[name='channel']").val(cid);
			$("select[name='channel']").trigger('change');
			checkChannelIsThirdparty();
		@endif


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

		//console.log(selected_list_data);

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

	$("#search").submit(function(event) {
    	event.preventDefault();

    	var params = $("#search .inventory-filter:visible").filter(function () {
			if ($(this).val() !== "") {
				return this;
			}
		}).serialize();

		var channel_type = $("select[name='channel'] option:selected").attr('data-channel-type');
		var is_third_party = $("select[name='channel'] option:selected").attr('data-third-party');

    	items_list.ajax.url("{{route('channel.inventory.search')}}?" + params + '&channel_type=' + channel_type + '&third_party=' + is_third_party).load();
    });

    // Bulk Actions dropdown
	// Channel bulk update/bulk assign categories
	$(".bulk-update").on("click", function() {
		if (checked.length>0){
			var url = "";
			var channelId = $("select[name='channel']").val();
			if(channel_id.length > 0) {
				channelId = channel_id;
			}
			if ($(this).data("action")=="update_products") {
				url = "{{route('channel.inventory.bulk_update')}}";
				if(channel_id.length > 0) {
					url = "{{route('byChannel.channel.inventory.bulk_update', $channel_id)}}";
				}
			}
			else if ($(this).data("action")=="assign_categories") {
				url = "{{route('channel.inventory.categories')}}";
				if(channel_id.length > 0) {
					url = "{{route('byChannel.channel.inventory.categories', $channel_id)}}";
				}
			}

			$form = $('<form id="test" action="'+url+'" method="POST"></form>');
			var channel = $('<input type="hidden" name="channel_id" value="'+channelId+'"></input>');
			$form.append(channel);
			$.each(checked, function( index, value ) {
				var input = $('<input type="hidden" name="products[]"></input>');
				input.val(value);
				$form.append(input);
			});
			$('body').append($form);
			$form.submit();
		}
		else {
			alert("Please select at least one SKU.");
		}
	});

	$('.sync-products').on('click', function () {
		if (checked.length > 0) {
			waitingDialog.show('Creating syncs....', {dialogSize: 'sm'});

			var url = $(this).attr('data-url');
			var channelId = $("select[name='channel']").val();
			if(channel_id.length > 0) {
				channelId = channel_id;
			}
			$form = $('<form action="' + url + '" method="POST"></form>');
			$form.append('<input type="hidden" name="channel" value="' + channelId + '"></input>');
			$.each(checked, function( index, value ) {
				$form.append('<input type="hidden" name="products[]" value="' + value + '"></input>');
			});

			$('body').append($form);
			$form.submit();
		}
		else {
			alert("Please select at least one product.");
		}
	});

	$('#btn_export_product_list').on('click', function(){
		var channelId = $("select[name=channel]").val();
		$(this).attr('href', '{{ URL::to("channels/inventory/generate_product_list/") }}/'+channelId);
	});
});

function checkChannelIsThirdparty() {
	if($("select[name='channel'] option:selected").attr('data-third-party') == 1) {
		$(".third-party-only").show();
	}
	else {
		$(".third-party-only").hide();
	}
}

function toEditPage(url) {
	var selected_channel = $("select[name='channel']").val();
	var channel_id = '{{ $channel_id }}';
	if(channel_id.length > 0) {
		selected_channel = channel_id;
	}
	window.open(url + '?channel=' + selected_channel, '_blank');
}
</script>
@append