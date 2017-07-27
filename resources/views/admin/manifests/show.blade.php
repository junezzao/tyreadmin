@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('admin/fulfillment.page_title_manifest', ['manifest' => $manifest_id])
@stop

@section('content')
	<div class="errors"></div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('admin/fulfillment.content_header_manifest_list')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title to-print1">
                        	@lang('admin/fulfillment.box_title_manifest', ['manifest' => $manifest_id])
                        </h3>
                        <h3 class="box-title pull-right noPrint" id="manifest-status">{{$manifest_status}}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                       	<div>
							<div class="manifest-view form-wrapper">
								<h4 class="pull-left">{{trans('admin/fulfillment.manifest_label_item')}}: &nbsp;</h4> 
								<div class="manifest-view form-group col-md-4">
									{!! Form::text('hubwire_sku', null, array('id'=>'hubwire_sku','class'=>'form-control', 'placeholder'=>trans('admin/fulfillment.manifest_placeholder_barcode'))) !!}
								</div>
								@if($readyToComplete && $manifest_status!="Completed")
									<button type="button" class="btn btn-primary pull-right completed" style="margin-left: 10px;">@lang('admin/fulfillment.manifest_btn_completed')</button>
								@else
									<button type="button" class="btn btn-primary pull-right completed" style="display:none; margin-left: 10px;">@lang('admin/fulfillment.manifest_btn_completed')</button>
								@endif
								
								<button id="print" class="btn btn-black pull-right" style="margin-left: 10px;"><i class="fa fa-print" aria-hidden="true"></i></button>
								<button id="toggle-search" class="btn btn-black pull-right" style="margin-left: 10px;"><i class="fa fa-search" aria-hidden="true"></i></button>
								<a href="{{route('admin.fulfillment.manifests.exportPosLaju', $manifest_id)}}" class="btn btn-default pull-right" style="margin-left: 10px;">@lang('admin/fulfillment.manifest_btn_export_post_laju')</a>
								<a href="{{route('admin.fulfillment.manifests.printDocuments', $manifest_id)}}" class="btn btn-default pull-right" target="_blank">@lang('admin/fulfillment.manifest_btn_print_docs')</a>
							</div>
						</div>

						<!-- Nav tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('admin/fulfillment.tab_title_picking_items')</a></li>
                                <li role="presentation"><a href="#merchants" aria-controls="merchants" role="tab" data-toggle="tab">@lang('admin/fulfillment.tab_title_orders')</a></li>
                            </ul>

                             <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                    <div class="col-xs-12">
										<div class="to-print2" style="max-height:480px; overflow-y:scroll">
											<table id="itemsTable" width="100%" class="table table-striped" style="width:100%;">
										    <thead>
										        <tr>
										            <th>{{trans('admin/fulfillment.manifest_table_hubwire_sku')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_item_name')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_coordinates')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_order_no')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_item_id')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_tp_order_date')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_status')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_actions')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_sku_id')}}</th>
										        </tr>
										    </thead>
										    <tbody>
										    </tbody>
										    </table>
										</div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane clearfix" id="merchants">
                                    <div class="col-xs-12">
                                        <div style="max-height:480px; overflow-y:scroll">
											<table id="ordersTable" width="100%" class="table table-striped" style="width:100%;">
										    <thead>
										        <tr>
										            <th>{{trans('admin/fulfillment.manifest_table_order_no')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_channel_name')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_tp_order_date')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_status')}}</th>
										            <th>{{trans('admin/fulfillment.manifest_table_cancelled_status')}}</th>
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

						<div style="padding-top: 10px;">
							<div class="manifest-view form-wrapper">
								<div class="manifest-view form-group pull-right">
									@if($readyToComplete && $manifest_status!="Completed")
										<button type="button" class="btn btn-primary completed">@lang('admin/fulfillment.manifest_btn_completed')</button>
									@else
										<button type="button" class="btn btn-primary completed" style="display:none;">@lang('admin/fulfillment.manifest_btn_completed')</button>
									@endif
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="jsForPrintWindow" class="hide">
		$(document).ready(function(){
			$("#itemsTable_filter").addClass("noPrint");
			setTimeout( function() {
				window.print();
			}, 200);
		});
	</div>
	<div id="cssForPrintWindow" class="hide">
		.noPrint { 
			display:none!important; 
		}
		td {
			font-size:12px;
		}
		tr {
			font-size:14px;
		}
		table {
			line-height: 12px;
		}
	</div>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<script type="text/javascript">
    // type - warning, danger, success, info, etc
	function displayAlert(message, type) {
		$(".errors").html('<div class="alert alert-'+type+' alert-dismissible" role="alert">'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		  '<span aria-hidden="true" style="font-size:inherit;">&times;</span></button>'+message+
		'</div>');
	}

	$.fn.dataTable.ext.type.order['coordinates-pre'] = function ( val ) {
		val = val.replace(' ', '');
		if (val == "" || val == null || val ==  undefined) {
			return -1;
		}
		else {
			//replace(/[^\d-]/g, '')
	    	var sections = val.toUpperCase().split('/')[0].split('-');
			return ("" + sections[0] + sections[2] + sections[1]);
		}
	};

	$(document).ready(function() {
		var itemsTable = $('#itemsTable').DataTable({
	    	"sDom": '<"H"f>t<"F"ir>',
	    	"ajax": '{{route("admin.fulfillment.manifests.items", [$manifest_id])}}',
	    	"pageLength": -1,
			"order": [[2, "asc"], [8, "desc"], [3, "desc"]],
			"columnDefs": [
	            {
	                "targets": [ 8 ],
	                "visible": false
	            },
	            {
	            	"type": "coordinates",
	            	"targets": [ 2 ]
	            }
            ],
	    	"columns": [
				{"data":"hubwire_sku", "name":"hubwire_sku", "targets":0},
				{"data":"product_name", "name":"product_name", "targets":1},
				{"data":"coordinates", "name":"coordinates", "targets":2},
				{"data":"order_no", "name":"order_no", "targets":3},
				{"data":"item_id", "name":"item_id", "targets":4},
				{"data":"tp_order_date", "name":"tp_order_date", "targets":5},
				{"data":"status", "name":"status", "targets":6},
				{"data":"actions", "name":"actions", "targets":7},
				{"data":"sku_id", "name":"sku_id", "targets":8},
			]
	    });

	    var ordersTable = $('#ordersTable').DataTable({
	    	"sDom": '<"H"f>t<"F"ir>',
	    	"ajax": '{{route("admin.fulfillment.manifests.orders", [$manifest_id])}}',
	    	"pageLength": -1,
			"order": [[1, "desc"]],
	    	"columns": [
	    		{"data":"id", "name":"id", "targets":0, "width": "15%"},
				{"data":"name", "name":"name", "targets":1, "width": "35%"},
				{"data":"tp_order_date", "name":"tp_order_date", "targets":2, "width": "20%"},
				{"data":"status", "name":"status", "targets":3, "width": "15%"},				
				{"data":"cancelled_status", "name":"cancelled_status", "targets":4, "width": "15%"},
			]
	    });

		$(document).on("keyup","#hubwire_sku", function (e) {
			// if enter was pressed
			if (e.keyCode==13) {
				var sku = $("#hubwire_sku").val();
		    	var manifestId = "{{$manifest_id}}";
		    	if (sku!="") {
		    		$.ajax({
						type:"POST",
						data: {hubwire_sku: sku},
						url: "{{route('admin.fulfillment.manifests.item.pick', [$manifest_id])}}",
						beforeSend: function() {
		                    waitingDialog.show('Processing...', {dialogSize: 'sm'});
		                },
						success:function(response){
							if (response.success) {
								itemsTable.ajax.reload(function(){
									$('#itemsTable').find('tr').each(function() {
										var td = $(this).children('td:eq(4)');
										if(td.text() == response.orderItemId) {
											$(this).addClass('success');
											$('div.to-print2').animate({
										        scrollTop: $('div.to-print2').scrollTop() + $(this).position().top - 100
										    }, 500);
											return;
										}
									});
								});

								$("#hubwire_sku").val("");
								waitingDialog.hide();
								$("#hubwire_sku").focus();
							}
							else {
								displayAlert(response.message, 'danger');
								$('#itemsTable').find('tr').removeClass('success');
								$('html, body').animate({
							        scrollTop: 0
							    }, 500);
							    $("#hubwire_sku").val("");
							    waitingDialog.hide();
							    $("#hubwire_sku").focus();
							}
							if (response.readyToComplete) {
								$(".completed").show();
							}
						},
						
					});
		  	 	}	
			}
		});
		
	    $(document).on("click",".oos", function (e) {	
	    	var c = confirm('Are you sure you want to mark this SKU as out of stock?');
            if(c){
                var itemId = $(this).data('id');
				var manifestId = "{{$manifest_id}}";
	    		$.ajax({
					type:"POST",
					data: {id: itemId},
					url: "{{route('admin.fulfillment.manifests.item.outofstock', [$manifest_id])}}",
					beforeSend: function() {
	                    waitingDialog.show('Processing...', {dialogSize: 'sm'});
	                },
					success:function(response){
						if (response.success) {
							itemsTable.ajax.reload(function(){
								$('#itemsTable').find('tr').each(function() {
									$(this).removeClass('success');
									var td = $(this).children('td:eq(4)');
									if(td.text() == response.orderItemId) {
										$(this).addClass('success');
									}
								});
							});
							if(response.message !==undefined)
								displayAlert(response.message, 'danger');
						} else {
							if (response.message!==undefined) 
								displayAlert(response.message, 'danger');
							else 
								displayAlert("An error has occurred.", 'danger');
						}

						if (response.readyToComplete) {
							$(".completed").show();
						}

						$("#hubwire_sku").focus();
					},
					complete: function() {
						waitingDialog.hide();
						$("#hubwire_sku").focus();
					},
				});
            }
		});

		$(".completed").click(function() {			
	    	$.ajax({
				type:"POST",
				url: "{{route('admin.fulfillment.manifests.completed', [$manifest_id])}}",
				beforeSend: function() {
                    waitingDialog.show('Processing...', {dialogSize: 'sm'});
                },
				success:function(response){
					if (response.success) {
						window.location.href = "{{route('admin.fulfillment.manifests.index')}}";
					}
					else {
						if (response.message!==undefined) 
							displayAlert(response.message, 'danger');
						else 
							displayAlert("An error has occurred.", 'danger');
					}
				},
				complete: function() {
					waitingDialog.hide();
				},
			});	    	
	    });

	    if ("{{$manifest_status}}"=="Completed") {
	    	var column = itemsTable.column(7);
	    	column.visible( ! column.visible() );
	    }
				
		// Print table Css and Js
        var css = '<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">';
        //var tableCss = '<link href="/packages/datatables/datatables.min.css" rel="stylesheet" type="text/css">';
        var printCss = '<style>' + $("#cssForPrintWindow").text() +' <\/style>';
        var jquery = '<script src="{{ asset("plugins/jQuery/jQuery-2.1.4.min.js") }}"><\/script>';
        var datatables = '<script src="{{ asset("plugins/datatables/jquery.dataTables.min.js")}}"><\/script>';
        var js = "<script type='text/javascript'>" + $("#jsForPrintWindow").text() + "<\/script>";

	    $("#print").click(function() {	
	    	var column = itemsTable.column(7);
 
        	// Toggle the visibility
        	column.visible( ! column.visible() );	
			var header = '<h3>'+$('.to-print1').html()+'</h3>';
	    	var manifestTable = $('.to-print2').html();
			var printWindow = window.open();
			printWindow.document.write('<title>Print Manifest</title>');
			printWindow.document.write(css + printCss 
				+ jquery + datatables + js + header + manifestTable);
			printWindow.document.close();

			column.visible( ! column.visible() );
	    });


	    $("#hubwire_sku").focus();
	    $("#itemsTable_filter").hide();

	    $("#toggle-search").click(function() {
	    	if ($("#itemsTable_filter").is(":visible")) 
	    		$("#itemsTable_filter").hide();
	    	else 
	    		$("#itemsTable_filter").show();
	    });

	    $("#print-docs").click(function() {

	    });

	    $("#post-laju").click(function() {

	    });
	});
</script>
@append