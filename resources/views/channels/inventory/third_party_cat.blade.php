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
	              		<h3 class="box-title">@lang('product-management.box_title_assign_categories') &raquo; {{$channel->name}}</h3>
	              		<button type="button" id="display-categories" class="margin btn btn-black pull-right">Display Categories</button>
                        <a class="margin btn bg-purple pull-right" role="button" href="{{ route('channels.inventory.index', ['pid'=>implode(',', $product_ids), 'cid' => $channel_id]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<div id="categories-div" class="hide">
							<table id="categories" class="table table-bordered table-striped" width="100%">
					        <thead>
					            <tr>
					                <th>Category Name</th>
					                <th>Category ID</th>
					            </tr>
					        </thead>
					        <tbody>
					        	@foreach($categoriesWithID as $key=>$val)
									@if($val!=0)
										<tr>
											<td>{{$key}}</td>
											<td>{{$val}}</td>
										</tr>
									@endif
								@endforeach
					        </tbody>
					        </table>
						</div>

						<div class="row table-wrapper">
							<div class="col-xs-12">
								<p style="display:none;">
								<!-- <button name="save" id="save">Save</button> -->
								<label><input type="checkbox" name="autosave" id="autosave" checked="checked" autocomplete="off"> Autosave</label>
								</p>

								<form class="form-inline" id="checkboxes">
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<style>
	#categories {
		height:100px;
		margin-bottom: 10px;
	}
	.handsontable .wtHolder, .wtHider, .htCore {
		width: inherit!important;
	}
</style>
<script>
	$(document).ready(function() {
		$("#categories").DataTable();
		$("#display-categories").click(function() {
			if($("#categories-div").is(":visible")) {
				$("#categories-div").addClass("hide");
				$("#display-categories").html('Display Categories');
			}
			else {
				$("#categories-div").removeClass('hide');
				$("#display-categories").html('Hide Categories');
			}
		});

		var
	      getID = function(id) {
	          return document.getElementById(id);
	      },
	      container = getID('bulk-update-table'),
	      exampleConsole = getID('example1console'),
	      autosave = getID('autosave'),
	      save = getID('save'),
	      autosaveNotification,
	      hot;

	    <?php
			$js_array = json_encode($product_ids);
			echo "var products = ". $js_array . ";\n";

			$js_categories = json_encode($categories);
			echo "var categories = ". $js_categories . ";\n";
		?>

		var colsToHide = [];

		columnHeaders = ["Product Name", "System SKU", "Hubwire SKU", "Supplier SKU","Category ID", "Category"];
		cols = [
				{data: "name", type: 'text', readOnly: true, width: "150"},
				{data: "sku_id", type: 'text', readOnly: true, width: "50"},
				{data: "hubwire_sku", type: 'text', readOnly: true, width: "100"},
				{data: "sku_supplier_code", type: 'text', readOnly: true},
				{data: "cat_id", type: 'text', readOnly: true},
				{
					data: 'cat_name',
					type: 'autocomplete',
					strict: true,
					allowInvalid: false,
					visibleRows: 10,
					width: "150",
					source: categories
				}];

		$.each(columnHeaders, function(key, val) {
			htmlString = '<div class="checkbox"><label>'+
						'<input type="checkbox" class="hide-fields" data-name="'+val+'"> '+val+
			    		'&nbsp;&nbsp;</label></div>';

			$("#checkboxes").append(htmlString);
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
	    	renderAllRows:false,
			contextMenu: ["undo", "redo"],
			stretchH: "all",
			height: '600',
			columns: cols,
			afterChange: function (change, source) {
				if (!autosave.checked) {
				  	return;
				}
				if (source === 'loadData') {
					return;
				}
				// modify change variable to include product id and channel id
				$.each(change, function(key, val) {
					dataset = hot.getSourceDataAtRow(val[0]);
					change[key][0] = dataset.id+","+"{{$channel->id}},"+dataset.sku_id;
				});
				clearTimeout(autosaveNotification);
				$.ajax({
				   	type: "POST",
				   	url: '{{route("channel.inventory.categories.save")}}',
				   	dataType: 'json',
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
					    	loadData();
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
	    });
	  	loadData();

	    // manually adjust width of handsontable


		// load data via ajax
		function loadData() {
			$.ajax({ 
				url: '{{route("channel.inventory.categories.load")}}',
				dataType: 'json',
				data: {product_ids: products, channel_id: "{{$channel->id}}"},
				type: 'GET',
				success: function(response) {
					if (response.success==true) {

			    		hot.loadData(response.data.products);
						hot.updateSettings({
				            startRows: response.data.products.length
				        });
				        $(exampleConsole).html("Changes will be autosaved.");
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
	});
</script>
@append
