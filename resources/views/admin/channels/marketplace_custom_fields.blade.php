<div class="tab-pane clearfix" id="mp-custom-fields">
	<div class="col-xs-12">
	<p style="display:none;">
	<label><input type="checkbox" name="autosave" id="autosave" checked="checked" autocomplete="off"> Autosave</label>
	</p>

	<pre id="example1console" class="bg-info text-info">Loading data...</pre>
	<div id="custom-fields-table"></div>
	</div>
</div>

<style type="text/css">
.handsontable {
	overflow:visible;
}
.wtHolder, .wtHider, .htCore {
	width: inherit!important;
}

#custom-fields-table {
	table-layout: fixed!important;
}

</style>
<script>
	$(document).ready(function() {
		<?php
            $js_categories = json_encode($categories);
            echo "var categories = ". $js_categories . ";\n";
        ?>

		var
	    container = document.getElementById('custom-fields-table'),
	    autosaveNotification,
	    exampleConsole = document.getElementById('example1console'),
	    hot;

		hot = new Handsontable(container, {
			colHeaders: ['ID', 'Field Name', 'Mandatory', 'Default Value', 'Category'],
			//colHeaders: ['ID', 'Field Name', 'Category'],
			startRows: 1,
		    minRows: 1,
		    minSpareRows: 1,
		    renderAllRows: false,
		    contextMenu: ["remove_row", "undo", "redo"],
			columnSorting: {
				column: 3,
			  	sortOrder: true // true = ascending, false = descending, undefined = original order
			}, 
			stretchH: 'last',
			height: '400',
		    columns: [
		    	{
		    		data: 'id',
		    		type: 'text',
		    		readOnly: true,
		    		width: 150,
		    	},
				{
					data: 'field_name',
					type: 'text',
					width: 200,
				},
				{
					data: 'compulsory',
					type: 'dropdown',
					source: ["Yes", "No"],
					width: 120,
				},
				{
					data: 'default_value',
					type: 'text',
					width: 150,
				},
				{
					data: 'category',
					type: 'autocomplete',
					strict: true,
					allowInvalid: false,
					visibleRows: 10,
					width: "150",
					source: categories
				}
		    ],
		    afterChange: function (change, source) {
		    	if (!autosave.checked) {
				  	return;
				}
				if (source === 'loadData') {
					return;
				}
				clearTimeout(autosaveNotification);
				var changes = [];

				// prevent from sending null/empty values to the server if previous value is null/empty
				$.each(change, function(key, val) {
					if (!(val[2] || val[3])) { }
					else {
						changes.push(val);
					}
				});

				if (changes.length>0) {
					$.ajax({
					   	type: "POST",
					   	url: '{{route("custom_fields.update", [$id])}}',
					   	data: {changes: change, data: getData()},
					   	beforeSend: function() {
					   		$(exampleConsole).html("Saving your changes. Please wait...");
					   	},
					    success: function(response) {
					    	if (response.success) {
					    		$(exampleConsole).html(response.message);
					    		exampleConsole.className = 'bg-success text-success';

						    	autosaveNotification = setTimeout(function() {
						    		exampleConsole.innerText ='Changes will be autosaved.';
						    		exampleConsole.className = 'bg-info text-info';
						    	}, 3000);

						    	if (response.data!==undefined) {
						    		hot.loadData(response.data);
									hot.updateSettings({
							            startRows: response.data.length
							        });
						    	}
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
			},
			beforeRemoveRow: function(index, amount) {
				if (!autosave.checked) {
				  	return;
				}
				clearTimeout(autosaveNotification);
				$.ajax({
				   	type: "POST",
				   	url: '{{route("custom_fields.delete", [$id])}}',
				   	data: {toDelete: getDataIndex(index, index+(amount-1))},
				   	beforeSend: function() {
				   		$(exampleConsole).html("Deleting selected fields. Please wait...");
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
				    	}
				    	else {
				    		$(exampleConsole).html(response.error);
				    		exampleConsole.className = 'bg-danger text-danger';
				    	}
				    },
				    error: function(response) {
				    	$(exampleConsole).html('An error has occurred while deleting. Please try again.');
				    	exampleConsole.className = 'bg-danger text-danger';
				    }
				});
			}
		});

		// load data via ajax
		$.ajax({ //loads data to Handsontable
			url: '{{route("custom_fields.get", [$id])}}',
			dataType: 'json',
			type: 'GET',
			success: function(response){
				if (response.success) {
		        	if (response.data!=null) {
		        		hot.loadData(response.data);
						hot.updateSettings({
				            startRows: response.data.length
				        });
		        	}
		        	$(exampleConsole).html('Changes will be autosaved.');
		    	}
		    	else {
		    		$(exampleConsole).html(response.error);
		    		exampleConsole.className = 'bg-danger text-danger';
		    	}
			},
			error: function(response) {
		    	$(exampleConsole).html('An error has occurred while loading data from the server. Please try again.');
		    	exampleConsole.className = 'bg-danger text-danger';
		    }
		});

		function getData() {
			return hot.getData();
		}

		function getDataIndex(index1, index2) {
			return hot.getData(index1, 0, index2, 0);
		}

		$('a[href="#mp-custom-fields"]').on('shown.bs.tab', function (e) {
            hot.render();
        });
	});
</script>
<script src="{{ asset('plugins/handsontable/handsontable.full.js', env('HTTPS', false)) }}"></script>
<link href="{{ asset('plugins/handsontable/handsontable.full.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">