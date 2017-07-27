@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
@append

@section('title')
	@lang('product-management.page_title_channel_mgmt_inventory') {{$channel->name}}
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
	              		<h3 class="box-title">@lang('product-management.box_title_bulk_update') &raquo; {{$channel->name}}</h3>
	              		<a class="margin btn bg-purple pull-right" role="button" href="{{ route('channels.inventory.index', ['pid'=>implode(',', $product_ids), 'cid' => $channel_id]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
						<div class="row table-wrapper">
							<div class="col-xs-12">	
								<p style="display:none;">
								<!-- <button name="save" id="save">Save</button> -->
								<label><input type="checkbox" name="autosave" id="autosave" checked="checked" autocomplete="off"> Autosave</label>
								</p>

								<form class="form-inline">
									<div class="form-group">
									    <label for="exampleInputName2">Hide Fields: </label>
									</div>
								</form>

								<pre id="example1console" class="bg-info text-info">Loading data...</pre>

								<div id="bulk-update-table"></div>
								<div style="text-align:right">
		                            <a class="btn bg-purple margin" role="button" href="{{ route('channels.inventory.index', ['pid'=>implode(',', $product_ids), 'cid'=>$channel_id]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
		                        </div>
							</div>
						</div>
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop


@section('footer_scripts')
<script src="{{ asset('plugins/handsontable/handsontable.full.js',env('HTTPS',false)) }}"></script>
<link href="{{ asset('plugins/handsontable/handsontable.full.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/bulk_update.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script>
	$(document).ready(function() {		
		var
	      getID = function(id) {
	          return document.getElementById(id);
	      },
	      container = getID('bulk-update-table'),
	      exampleConsole = getID('example1console'),
	      autosave = getID('autosave'),
	      save = getID('save'),
	      autosaveNotification,
	      hot,
	      categoryValidator;

	    <?php
			$js_array = json_encode($product_ids);
			echo "var products = ". $js_array . ";\n";
		?>

		var colsToHide = [];
		var customFieldsDetails = [];
		var hideByDefault = ["System SKU", "Supplier SKU", "Qty", "Retail Price", "Listing Price", "Warehouse Coordinates", "Tags", "Options"];

		columnHeaders = ["Product Name", "System SKU", "Hubwire SKU", "Category ID", "Category", "Supplier SKU", "Qty", "3rd Party Status", "Retail Price", "Listing Price", "Sale Start", "Sale End", "Warehouse Coordinates", "Tags", "Options"];
		cols = [{data: "name", type: 'text', readOnly: true, width: "250"},
				{data: "sku_id", type: 'text', readOnly: true},
				{data: "hubwire_sku", type: 'text', readOnly: true},
				{data: "cat_id", type: 'text', readOnly: true, width: "80"},
				{data: "cat_name", type: 'text', readOnly: true, width: "150"},
				{data: "sku_supplier_code", type: 'text', readOnly: true},
				{data: "channel_sku_quantity", type: 'numeric', format: '0', readOnly: true},
				{data: "channel_sku_active", type: 'dropdown', source:['INACTIVE', 'ACTIVE']},
				{data: "channel_sku_price", type: 'numeric', format: '0.00'},
				{data: "channel_sku_promo_price", type: 'numeric', format: '0.00'},
				{data: "promo_start_date", type: 'date', dateFormat: 'YYYY-MM-DD'},
				{data: "promo_end_date", type: 'date', dateFormat: 'YYYY-MM-DD'},
				{data: "channel_sku_coordinates", type: 'text'},
				{data: "tags", type: 'text', readOnly: true, width: "125"},
				{data: "options", type: 'text', readOnly: true, renderer: "html"}];

		if ("{{$is_marketplace}}"=='false') {
			columnHeaders.splice(1,1);
			cols.splice(1,1);
		}
		$.each(columnHeaders, function(key, val) {
			htmlString = '';
			if (hideByDefault.indexOf(val)>-1) {
				htmlString = '<div class="checkbox"><label>'+
						'<input type="checkbox" class="hide-fields" data-name="'+val+'" checked> '+val+
			    		'&nbsp;&nbsp;</label></div>';
			    colsToHide.push(val);
			}
			else {
				htmlString = '<div class="checkbox"><label>'+
						'<input type="checkbox" class="hide-fields" data-name="'+val+'"> '+val+
			    		'&nbsp;&nbsp;</label></div>';
			}

			$(".form-inline").append(htmlString);
		});

		function updateColumns() {
			var newCols = [];
			var newColHeaders = [];
			$.each(columnHeaders, function(key, val){
				if (colsToHide.indexOf(val) === -1) {
	                newCols.push(cols[key]);
	                newColHeaders.push(columnHeaders[key]);
	            }
			});
	        hot.updateSettings({
	            columns: newCols,
	            colHeaders: newColHeaders
	        });
		}

		$(".hide-fields").change(function() {
			var fieldname = $(this).data('name');

			if($(this).is(":checked")) {
	            colsToHide.push(fieldname);
	        }
	        else {
	        	colsToHide = $.grep(colsToHide, function(value) {
	        		return value != fieldname;
	        	});
	        }
	        updateColumns();
		});

	    hot = new Handsontable(container, {
	    	startCols: columnHeaders.length,
	        colHeaders: columnHeaders,
	        rowHeaders: true,
	        manualColumnResize: true,
	    	manualRowResize: true,
	    	fixedColumnsLeft: 1,
	    	height: '600',
			contextMenu: ["undo", "redo"],
			stretchH: "all",
			trimWhitespace: false,
			columns: cols,
			afterChange: function (change, source) {
				if (!autosave.checked) {
				  	return;
				}
				if (source === 'loadData') {
					return;
				}
				var valid = true;
				var errorMsg = 'An error has occurred while saving your changes due to the following: <br />';
				
				$.each(change, function(key, val) {					
					dataset = hot.getSourceDataAtRow(val[0]);
					
					// modify change variable to include product id
					change[key][0] = dataset.id+","+dataset.sku_id+","+dataset.channel_sku_id;

					found = false;
					mandatoryCategories = [];
					
					// get custom field details for current cell and construct a list of mandatory categories
					details = jQuery.grep(customFieldsDetails, function( customField, i ) {
						return (customField.field_name+'|'+customField.id).toUpperCase() === change[key][1].toUpperCase();
					});
					
					if (details.length > 0) {
						// custom field id
						//change[key][4] = details[0].id;
						
						for (var i = 0; i<details.length; i++) {
							mandatoryCategories.push(details[i].category);
						}
						if (dataset[details[0].id]!==undefined)
							change[key][0] += "," + dataset[details[0].id]['field_data_id'];
						//console.log(dataset.cat_id);
						// check to see if category is in list of mandatory categories
						if (dataset.cat_id!=null) {
							for (var i = 0; i<mandatoryCategories.length; i++) {
								if (dataset.cat_id.indexOf(mandatoryCategories[i]) > -1 || mandatoryCategories[i] == "All") {
									found = true;
									break;
								}
							}
							if (!found) {
								valid = false;

								errorMsg += '<br/>"' + details[0].field_name + ' (' + details[0].category + ')' + '" is only applicable for ' 
											+ mandatoryCategories.join(', ');
							}
						}
						else if ("{{$channel->channel_type->id}}"!=6) {
							valid = false;
							errorMsg += 'The product you\'re trying to edit does not have a category. Please assign a category to the product.';
						}
					}
					//console.log(change);
				});

				if (valid) {
					clearTimeout(autosaveNotification);
					$.ajax({
					   	type: "POST",
					   	dataType: 'json',
					   	url: '{{route("channel.inventory.bulk_update.save")}}',
					   	data: {data: change, channel_id: "{{$channel->id}}"},
					   	beforeSend: function() {
					   		$(exampleConsole).html("Saving your changes. Please wait...");
					   		exampleConsole.className = 'bg-info text-info';
					   	},
					    success: function(response) {
					    	if (response.success) {
					    		$(exampleConsole).html(response.message);
					    		exampleConsole.className = 'bg-success text-success';

						    	autosaveNotification = setTimeout(function() {
						    		exampleConsole.innerText ='Changes will be autosaved.';
						    		exampleConsole.className = 'bg-info text-info';
						    	}, 2000);

						    	// workaround for custom field reverting to original value - doesn't fix loading issue on a code-level
						    	setTimeout(function() {
						    		// reload data
							    	$.ajax({ 
										url: '{{route("channel.inventory.bulk_update.load")}}',
										dataType: 'json',
										data: {product_ids: products, channel_id: "{{$channel->id}}"},
										type: 'POST',
										success: function(res) {
											console.log(res);
											if (res.success)
												hot.loadData(res.data.products);
											
											else {
												$(exampleConsole).html(res.error);
												exampleConsole.className = 'bg-danger text-danger';
											}
										},
										error: function(response) {
									    	$(exampleConsole).html('An error has occurred while loading data from the server. Please try again.');
									    	exampleConsole.className = 'bg-danger text-danger';
									    }
									});
						    	}, 350);
						    	
					    	}
					    	else {
					    		$(exampleConsole).html(response.error);
					    		exampleConsole.className = 'bg-danger text-danger';
					    	}
					    },
						error: function(response) {
					    	$(exampleConsole).html('An error has occurred while autosaving your changes. Please try again.');
					    	exampleConsole.className = 'bg-danger text-danger';
					    }
					});
				}
				else {			
					$(exampleConsole).html(errorMsg);
					exampleConsole.className = 'bg-danger text-danger';
				}
			}
	    });

		// load data via ajax
		$.ajax({ //loads data to Handsontable
			url: '{{route("channel.inventory.bulk_update.load")}}',
			dataType: 'json',
			data: {product_ids: products, channel_id: "{{$channel->id}}"},
			type: 'POST',
			success: function(res) {
				//console.log(res);
				if (res.success) {
					if (res.data.custom_fields!==null) {
						customFields(res.data.custom_fields);
						customFieldsDetails = res.data.custom_fields;
					}
					updateColumns();
					hot.loadData(res.data.products);
					hot.updateSettings({
			            startRows: res.data.products.length
			        });   
			        $(exampleConsole).html("Changes will be autosaved.");
				}
				else {
					$(exampleConsole).html(res.error);
					exampleConsole.className = 'bg-danger text-danger';
				}
				
			},
			error: function(response) {//console.log(response);
		    	$(exampleConsole).html('An error has occurred while loading data from the server. Please try again.');
		    	exampleConsole.className = 'bg-danger text-danger';
		    }
		});
		

		function customFields(customFields) {
			var newCols = [];
			var newColHeaders = [];
			
			$.each(customFields, function(key, val) {
				//console.log(val);
				//if (newColHeaders.indexOf(val.field_name) === -1) {
	                newCols.push({data: val.field_name+"|"+val.id, type: 'text', placeholder: val.default_value});

	                var colHeader =  val.field_name+' ('+val.category+')';
	                if (val.compulsory=='Yes') 
	                	colHeader += '<span class="required"></span>';

	                newColHeaders.push(colHeader);
	            //}
			});
			//console.log(newCols);
			cols = cols.concat(newCols);
	        columnHeaders = columnHeaders.concat(newColHeaders);

	        hot.updateSettings({
	            columns: cols,
	            colHeaders: columnHeaders
	        });
		}
	});
</script>
@append
