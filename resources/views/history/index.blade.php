@extends('layouts.master')

@section('title')
@lang('titles.tyre_history')
@stop

@section('content')
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	
	            	<div class="box-body tyre-history">
	            		
	            		<div class="col-xs-12">
		            		<div class="col-xs-3" style="padding-left:0px;">
					            <div class="form-group">
					            	<label for="input-search" class="sr-only">Search Tree:</label>
					            	<input type="input" class="form-control" id="input-search" placeholder="Type keyword and enter to search..." value="">
					            </div>
					        </div>

					        <div class="col-xs-9" style="padding-left:0px;">
					            <button type="button" class="btn btn-default" id="btn-search">Search</button>
					            <button type="button" class="btn btn-default" id="btn-clear-search">Clear Search</button>
					            <button type="button" class="btn btn-success" id="btn-expand">Expand All</button>
					            <button type="button" class="btn btn-success" id="btn-collapse">Collapse All</button>
					            <button type="button" class="btn btn-warning" id="btn-expand-selected">Expand Selected</button>
					            <button type="button" class="btn btn-warning" id="btn-collapse-selected">Collapse Selected</button>
					        </div>
					    </div>

					    <div class="col-xs-12 view">
		            		<div class="title">View by Truck Position</div>
		            		<div id="truck-position"></div>
		            	</div>

		            	<div class="col-xs-12 view">
		            		<div class="title">View by Truck Serviced Date and Job Sheet</div>
		            		<div id="truck-service"></div>
		            	</div>

		            	<div class="col-xs-12 view">
							<div class="title">View by Tyre Brand</div>
							<div id="tyre-brand-nt"></div>
							<div id="tyre-brand-nt-sub-con"></div>
							<div id="tyre-brand-stk"></div>
							<div id="tyre-brand-stk-sub-con"></div>
							<div id="tyre-brand-coc"></div>
							<div id="tyre-brand-used"></div>
							<div id="tyre-brand-other"></div>
						</div>
	            	</div>
	        	</div>
	    	</div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<link href="{{ asset('css/bootstrap-treeview.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/bootstrap-treeview.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('div.level').on('click', function(e) {
		e.stopPropagation();

		$(this).children('div.level').slideToggle(500);
		$(this).children('div.item').children('a').children('.symbol').toggleClass('expand');
	});

	$('div.title').on('click', function(e) {
		e.stopPropagation();

		$(this).siblings('div.level').slideToggle(500);
	});

	$.ajax({
	  	type: "GET",  
	  	url: '{{ route('history.load') }}',
	  	dataType: "json",       
	  	success: function(data){
	  		$('#truck-position').treeview({
				data: JSON.parse(data['truckPosition']),
				levels: 1,
				multiSelect: true
			});

			$('#truck-service').treeview({
				data: JSON.parse(data['truckService']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-nt').treeview({
				data: JSON.parse(data['tyreBrand']['NT']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-nt-sub-con').treeview({
				data: JSON.parse(data['tyreBrand']['NT_SUB_CON']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-stk').treeview({
				data: JSON.parse(data['tyreBrand']['STK']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-stk-sub-con').treeview({
				data: JSON.parse(data['tyreBrand']['STK_SUB_CON']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-coc').treeview({
				data: JSON.parse(data['tyreBrand']['COC']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-used').treeview({
				data: JSON.parse(data['tyreBrand']['USED']),
				levels: 1,
				multiSelect: true
			});

			$('#tyre-brand-other').treeview({
				data: JSON.parse(data['tyreBrand']['OTHER']),
				levels: 1,
				multiSelect: true
			});
		}  
	});

	var search = function(e) {
		var pattern = $('#input-search').val();
		var options = {
			ignoreCase: true,
			exactMatch: false,
			revealResults: true
		};

		$('#truck-position').treeview('search', [ pattern, options]);
		$('#truck-service').treeview('search', [ pattern, options]);
		$('#tyre-brand-nt').treeview('search', [ pattern, options]);
		$('#tyre-brand-nt-sub-con').treeview('search', [ pattern, options]);
		$('#tyre-brand-stk').treeview('search', [ pattern, options]);
		$('#tyre-brand-stk-sub-con').treeview('search', [ pattern, options]);
		$('#tyre-brand-coc').treeview('search', [ pattern, options]);
		$('#tyre-brand-used').treeview('search', [ pattern, options]);
		$('#tyre-brand-other').treeview('search', [ pattern, options]);
	};

	var expand = function(e) {
		$('#truck-position').treeview('expandAll', { silent: true });
		$('#truck-service').treeview('expandAll', { silent: true });
		$('#tyre-brand-nt').treeview('expandAll', { silent: true });
		$('#tyre-brand-nt-sub-con').treeview('expandAll', { silent: true });
		$('#tyre-brand-stk').treeview('expandAll', { silent: true });
		$('#tyre-brand-stk-sub-con').treeview('expandAll', { silent: true });
		$('#tyre-brand-coc').treeview('expandAll', { silent: true });
		$('#tyre-brand-used').treeview('expandAll', { silent: true });
		$('#tyre-brand-other').treeview('expandAll', { silent: true });
	};

	var collapse = function(e) {
		$('#truck-position').treeview('collapseAll', { silent: true });
		$('#truck-service').treeview('collapseAll', { silent: true });
		$('#tyre-brand-nt').treeview('collapseAll', { silent: true });
		$('#tyre-brand-nt-sub-con').treeview('collapseAll', { silent: true });
		$('#tyre-brand-stk').treeview('collapseAll', { silent: true });
		$('#tyre-brand-stk-sub-con').treeview('collapseAll', { silent: true });
		$('#tyre-brand-coc').treeview('collapseAll', { silent: true });
		$('#tyre-brand-used').treeview('collapseAll', { silent: true });
		$('#tyre-brand-other').treeview('collapseAll', { silent: true });
	};

	$('#btn-search').on('click', search);
	$('#input-search').keypress(function(e) {
	    if(e.which == 13) {
	        $('#btn-search').trigger('click');
	    }
	});

	$('#btn-clear-search').on('click', function(e) {
		$('#truck-position').treeview('clearSearch');
		$('#truck-service').treeview('clearSearch');
		$('#tyre-brand-nt').treeview('clearSearch');
		$('#tyre-brand-nt-sub-con').treeview('clearSearch');
		$('#tyre-brand-stk').treeview('clearSearch');
		$('#tyre-brand-stk-sub-con').treeview('clearSearch');
		$('#tyre-brand-coc').treeview('clearSearch');
		$('#tyre-brand-used').treeview('clearSearch');
		$('#tyre-brand-other').treeview('clearSearch');
		$('#input-search').val('');
	});

	$('#btn-expand').on('click', expand);
	$('#btn-collapse').on('click', collapse);
	
	$('#btn-expand-selected').on('click', function(e) {
		var selectedNodes = $('#truck-position').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#truck-position').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#truck-service').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#truck-service').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-nt').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-nt').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-nt-sub-con').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-nt-sub-con').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-stk').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-stk').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-stk-sub-con').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-stk-sub-con').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-coc').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-coc').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-used').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-used').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});

		var selectedNodes = $('#tyre-brand-other').treeview('getSelected');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-other').treeview('expandNode', [ selectedNode.nodeId, { levels: 10, silent: true } ]);
		});
	});

	$('#btn-collapse-selected').on('click', function(e) {
		var selectedNodes = $('#truck-position').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#truck-position').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#truck-service').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#truck-service').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-nt').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-nt').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-nt-sub-con').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-nt-sub-con').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-stk').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-stk').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-stk-sub-con').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-stk-sub-con').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-coc').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-coc').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-used').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-used').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});

		var selectedNodes = $('#tyre-brand-other').treeview('getChecked');
		selectedNodes.forEach(function(selectedNode){
		    $('#tyre-brand-other').treeview('collapseNode', [ selectedNode.nodeId, { silent: true, ignoreChildren: false } ]);
		});
	});

	$('.check-icon').on('click', function(e) {
		console.log('checked');
	});
});
</script>
@append