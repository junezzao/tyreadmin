@extends('layouts.master')

@section('title')
	@lang('admin/fulfillment.content_header_live_transactions')
@stop

@section('header_scripts')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/loading-modal-prompt.js') }}"></script>
<style>
.selected-box{
	-webkit-box-shadow: 0px 6px 11px 7px rgba(0,0,0,0.8);
	-moz-box-shadow: 0px 6px 11px 7px rgba(0,0,0,0.8);
	box-shadow: 0px 6px 11px 7px rgba(0,0,0,0.8);
}
</style>
@append

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/fulfillment.content_header_live_transactions')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	        <div class="errors"></div>
	          	<div class="box">
	          		
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/fulfillment.box_header_live_transactions')</h3>
	            	</div><!-- /.box-header -->       	
	            	<div class="box-body">
	            		{!! Form::open(array('class' => 'form-inline', 'id' => 'search', 'role'=> 'form','method'=>'GET')) !!}
	            			<div class="form-wrapper">
	            				<div class="form-group col-md-10" >
									{!! Form::text('search_order', null, array('id'=>'search_order','class'=>'form-control search-col', 'placeholder'=>trans('admin/fulfillment.order_placeholder_search_order'), 'data-col'=>16)) !!}
									<div class="search_error text-danger"></div>
								</div>
								<div class="form-group col-md-1 pull-right"  style="padding-right:0px;">
									<button type="button" id="show-all" class="btn btn-black pull-right" style="width:100%;">{{trans('admin/fulfillment.order_button_clear_filter')}}</button>
									<span class="text-danger"></span>
								</div>

						        <div class="form-group col-md-1 pull-right">
									<button type="submit" id="btnFilter" class="btn btn-black pull-right" style="width:100%;">{{trans('admin/fulfillment.order_button_filter')}}</button>
									<span class="text-danger"></span> 
								</div>		
	            			</div>
							<div class="form-wrapper">
										
						        <!-- TODO: combine with 3rd party Code -->
								<div class="form-group col-md-2" >
									{!! Form::text('third_party_code', null, array('id'=>'third_party_code','class'=>'form-control search-col', 'placeholder'=>trans('admin/fulfillment.order_placeholder_tp_code'), 'data-col'=>13)) !!}
								</div>		
						        <div class="form-group col-md-2">
						            {!! Form::text('date_range', null, array('id' => 'date_range', 'class'=>'form-control datepicker search-col', 'placeholder'=>trans('admin/fulfillment.order_placeholder_date_range'), 'data-col'=>5)) !!}
						            <div class="fromDate_error text-danger"></div>
						        </div>
						       	
						       	<!-- Member Name
						       	 <div class="form-group col-md-2" >
									{!! Form::text('customer_name', null, array('id'=>'customer_name','class'=>'form-control search-col', 'placeholder'=>'Customer Name', 'data-col'=>4)) !!}
								</div> -->
						   	
						        <div class="form-group col-md-2">
									{!! Form::select('merchant_id', $merchants, null, array('class'=>'form-control search-col merchant_id select2', 'placeholder' => trans('admin/fulfillment.order_placeholder_merchant'), 'data-col'=>12) ) !!}
									<div class="pay_with_error text-danger"></div>
								</div>

								@if(is_null($channel_id))
								<div class="form-group col-md-2">
									{!! Form::select('channel_id', $channels, null, array('class'=>'form-control search-col channel_id select2', 'placeholder' => trans('admin/fulfillment.order_placeholder_channel'), 'data-col'=>11) ) !!}
									<div class="pay_with_error text-danger"></div>
								</div>
								@endif

								<div class="form-group col-md-2">
									{!! Form::select('cancelled_status', $cancelled_status, 0, array('class'=>'form-control search-col select2-nosearch', 'placeholder'=>trans('admin/fulfillment.order_placeholder_cancelled_status'), 'data-col'=>15) ) !!}
									<div class="status_error text-danger"></div>
								</div>

										
							</div>

							<div class="form-wrapper">
								<div class="form-group col-md-2">
									{!! Form::select('status', $statuses, '', array('class'=>'form-control search-col select2-nosearch', 'placeholder'=>trans('admin/fulfillment.order_placeholder_status'), 'data-col'=>8) ) !!}
									<div class="status_error text-danger"></div>
								</div>
								<div class="form-group col-md-2">
									{!! Form::select('paid_status', $paid_status, '', array('class'=>'form-control search-col select2-nosearch', 'placeholder'=>trans('admin/fulfillment.order_placeholder_paid_status'), 'data-col'=>9) ) !!}
									<div class="status_error text-danger"></div>
								</div>
								<div class="form-group col-md-2">
									{!! Form::select('payment_type', $payments, '', array('class'=>'form-control search-col select2-nosearch', 'placeholder'=>trans('admin/fulfillment.order_placeholder_payment_type'), 'data-col'=>10) ) !!}
									<div class="status_error text-danger"></div>
								</div>
								<div class="form-group col-md-2">
									{!! Form::select('partially_fulfilled', $partially_fulfilled, '', array('class'=>'form-control search-col select2-nosearch', 'placeholder'=>trans('admin/fulfillment.order_placeholder_partially_fulfilled'), 'data-col'=>14) ) !!}
									<div class="status_error text-danger"></div>
								</div>

								<!-- <div class="btn-group form-group pull-right col-md-1" style="padding-right:0px;">
								  <button type="button" class="btn btn-black dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:100%;"> 
								    Export... <span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu">
								    <li><a href="#" id="post-laju">Pos Laju CSV</a></li>
								    <li><a href="#" id="export">Order result list</a></li>
								  </ul>
								</div> -->
							</div>

							<div class="form-wrapper">
								<div class="form-group col-md-1 status-display">
									<a href="#" class="badge-btn" data-status="new">
										{{trans('admin/fulfillment.order_badge_btn_new')}} <span class="badge info" id="count-new">0</span>
									</a>
								</div>

								<div class="form-group col-md-1 status-display">
									<a href="#" class="badge-btn" data-status="picking">
										{{trans('admin/fulfillment.order_badge_btn_picking')}} <span class="badge info" id="count-picking">0</span>
									</a>
								</div>

								<div class="form-group col-md-1 status-display">
									<a href="#" class="badge-btn" data-status="packing">
										{{trans('admin/fulfillment.order_badge_btn_packing')}} <span class="badge info" id="count-packing">0</span>
									</a>
								</div>

								<div class="form-group col-md-2 status-display">
									<a href="#" class="badge-btn" data-status="ReadyToShip">
										{{trans('admin/fulfillment.order_badge_btn_ready_to_ship')}} <span class="badge info" id="count-ready-to-ship">0</span>
									</a>
								</div>

								<div class="form-group col-md-2 status-display">
									<a href="#" class="badge-btn" id="search-partially-fulfilled">
										{{trans('admin/fulfillment.order_badge_btn_partially_fulfilled')}} <span class="badge info" id="count-partially-fulfilled">0</span>
									</a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3 level" data-level="1">
									<a href="#filterByLevel=1">
									<div class="small-box bg-light-yellow">
										<div class="inner">
					                        <h3> Level 1 <i class="fa fa-hand-o-right"></i> <span id="level-1">{{ 0 }}</span></h3>
					                        <p>@lang('orders.level_1_orders')</p>
					                    </div>
					                    <div class="icon">
					                        <i class="fa fa-bell-o"></i>
					                    </div>
					                </div>
									</a>
								</div>
								<div class="col-md-3 level" data-level="2">
									<a href="#filterByLevel=2">
									<div class="small-box bg-light-orange">
										<div class="inner">
					                        <h3>Level 2 <i class="fa fa-hand-o-right"></i> <span id="level-2">{{ 0 }}</span> </h3>
					                        <p>@lang('orders.level_2_orders')</p>
					                    </div>
					                    <div class="icon">
					                        <i class="fa fa-exclamation-triangle"></i>
					                    </div>
					                    
									</div>
									</a>
								</div>
								<div class="col-md-3 level" data-level="3">
									<a href="#filterByLevel=3">
									<div class="small-box bg-red">
										<div class="inner">
					                        <h3> Level 3 <i class="fa fa-hand-o-right"></i> <span id="level-3">{{ 0 }}</span></h3>
					                        <p>@lang('orders.level_3_orders')</p>
					                    </div>
					                    <div class="icon">
					                        <i class="fa fa-bolt"></i>
					                    </div>
					                </div>
					            	</a>
								</div>
								<div class="col-md-3 level" data-level="4">
									<a href="#filterByLevel=4">
									<div class="small-box bg-dark-red">
										<div class="inner">
					                        <h3>Level 4 <i class="fa fa-hand-o-right"></i> <span id="level-4">{{ 0 }}</span></h3>
					                        <p>@lang('orders.level_4_orders')</p>
					                    </div>
					                    <div class="icon">
					                        <i class="fa fa-bomb"></i>
					                    </div>
									</div>
									</a>
								</div>
							</div>
						{!! Form::close() !!}

						<div class="form-wrapper" style="margin-top:30px;">
							
							<div class="form-group col-md-4">
								<p>{{trans('admin/fulfillment.order_label_scan_order_id')}}
								<input id="scan-order-id" class="form-control" value="" placeholder="{{trans('admin/fulfillment.order_placeholder_scan_order_id')}}"></p>
							</div>
						</div>

						<form id="exportConsignment" action="" method="POST">
							<input type="hidden" name="selected-sales" value="">
						</form>


						<div class="col-xs-12">
							<table id="salesTable" width="100%" class="table table-striped" style="width:100%;">
					        <thead>
					            <tr>
					            	<th class="check">{!!Form::checkbox('select-all', '')!!}</th>
					            	<th>{{trans('admin/fulfillment.order_table_merchant')}}</th>      	
					                <th>{{trans('admin/fulfillment.order_table_channel')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_order_no')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_customer_name')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_created_at')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_total')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_no_items')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_status')}}</th>
					           		<th>{{trans('admin/fulfillment.order_table_paid_status')}}</th>
					                <th>{{trans('admin/fulfillment.order_table_payment_method')}}</th>

					                <!-- Columns purely for searching - not shown on UI -->
					                <th>Channel ID</th>
					                <th>Merchant ID</th>
					                <th>TP Code</th>
					                <th>Partially Fulfilled</th>
					                <th>Cancelled Status</th> 
					                <th>Dummy Search Col</th>
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('css/live_transactions_index.css',env('HTTPS',false)) }}">
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	
	$('#scan-order-id').focus();
	var selectedSales = [];
    var channels = {!! json_encode($channels) !!};
    var channel_id = '{{ $channel_id }}';

    var hash = window.top.location.hash.substring(1);
    if(hash!='') $("a[href='#"+hash+"']").find('.small-box').addClass('selected-box');

    $('.level').click(function(){
    	$('.selected-box').removeClass('selected-box');
    	$(this).find('.small-box').addClass('selected-box');
    });

    loadCounters();
    loadLevels();

    $(window).bind('hashchange',function(event){
		var hash = location.hash.replace('#','');
    	url = '{{ URL::to("orders/search") }}' + (channel_id.length > 0 ? '?channel_id='+channel_id+'&' : '?') + (hash.length > 0 ? hash : '');
    	salesTable.ajax.url(url).load();
	});

    var url = '{{ URL::to("orders/search") }}' + (channel_id.length > 0 ? '?channel_id='+channel_id+'&' : '?') + (hash.length > 0 ? hash : '');
    var salesTable = $('#salesTable').DataTable({
    	"sDom": '<"H"lpr>t<"F"i>',
    	"processing": false,
		"ajax": url,
    	"lengthMenu": [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
    	"serverSide": true,
    	"order": [[3, "desc"]],
    	"pageLength": 50,
    	"preDrawCallback": function() {
    		waitingDialog.show('Loading...', {dialogSize: 'sm'});
    	},
    	"drawCallback": function(settings){
    		var checkboxes = $("input[name='sale']");
	    	if (selectedSales.length > 0) {
	    		for (var i = 0; i < checkboxes.length; i++) {
	    			if (selectedSales.indexOf(checkboxes[i].value)!=-1) {
	    				$(checkboxes[i]).prop("checked", true);
	    			}
	    		}
	    	}
	    	checkSelectAll();
	    	waitingDialog.hide();
	    	$('#scan-order-id').focus();
	    },
		"columnDefs": [
	        {
	            // search columns
	            "targets": [1, 11, 12, 13, 14, 16],
	            "visible": false,
	        },
	        {
	        	// unsortable columns
	        	"targets": [0, 1, 2, 6],
	        	"sortable": false
	        },
	        {
	        	"targets": [1, 2, 12],
	        	"searchable": false
	        },
	        {
	        	"render": function(data, type, row) {
	        		return channels[data];
	        	},
	        	"targets": 2
	        },
        ],
    	"columns": [
    		{"data":"checkbox", "name":"checkbox", className:"check", "targets":0},
    		{"data":"merchant_name", "name":"merchant_name", "targets":1}, 
			{"data":"channel_name", "name":"channel_name", "targets":2}, 
			{"data":"id", "name":"id", "targets":3},
			{"data":"member_name", "name":"member_name", "targets":4},
			{"data":"created_at",  "name":"created_at", "targets": 5},
			{"data":"sale_total", "name":"sale_total", "targets":6},
			{"data":"item_quantity", "name":"item_quantity", "targets":7},
			{"data":"status", "name":"status", "targets":8},
			{"data":"paid_status", "name":"paid_status", "targets":9},
			{"data":"payment_type", "name":"payment_type", "targets":10},
			{"data":"channel_id", "name":"channel_id", "targets":11},
			{"data":"merchant_id", "name":"merchant_id", "targets":12},
			{"data":"tp_order_code", "name":"tp_order_code", "targets":13},
			{"data":"partially_fulfilled", "name":"partially_fulfilled", "targets":14},
			{"data":"cancelled_status", "name":"cancelled_status", "targets":15},
			{"data":null,"name":"search_order", "targets":16},
		],
		// populate search fields during init - the length of this array must be equal to the number of columns
		"searchCols": [
			null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    { "search": 0 },	// set to search for only non-cancelled orders on load
		]
    });

	setInterval( function () {
		salesTable.ajax.reload( null, false ); // user paging is not reset on reload
	}, 5*60000 );

    // Search
    $("#search").submit(function(event) {
    	$('.search-col').each(function(i,obj){
    		salesTable.columns($(obj).data('col')).search($(obj).val());
    	});
    	event.preventDefault();
    	salesTable.draw();
    	loadCounters();
    });

    // when input/select fields in the search form are changed
    $(".search-col").on('change', function() {
    	salesTable.columns($(this).data('col')).search($(this).val());
    });

    String.prototype.capitalize = function() {
	    return this.charAt(0).toUpperCase() + this.slice(1);
	}

	$(".badge-btn").click(function() {
		var val = $(this).data('status');
		
		$("input.search-col").val('');
		$("select.search-col").val('').trigger('change');
		
		if (val !== undefined) {
			$("select[name='status'] option:contains("+val.capitalize()+")").prop('selected', true).trigger("change");
			$("select[name='cancelled_status']").val(0).trigger("change");
			salesTable.draw();
		}
		// search partially fulfilled column
		else {
			$("select[name='partially_fulfilled']").val(1).trigger("change");
			$("select[name='cancelled_status']").val(0).trigger("change");
			salesTable.columns(8)
			.search('21|22|23|24', true, false)
			.columns(15).search(0, false, false).draw();
		}

		loadCounters();
	});

	// if user is a merchant, hide merchant column and dropdown
	if ("{{Auth::user()->is('clientuser|clientadmin')}}" == 1) {
    	salesTable.column( 1 ).visible(false);
    	$("select[name='merchant_id']").parent().hide();
    }

    $("#show-all").on('click', function() {
    	$("#search")[0].reset();
    	window.top.location.hash = hash = '';
    	$('.selected-box').removeClass('selected-box');
    	loadCounters();
    	salesTable.columns().search('').draw();
    });

    $('input[name="date_range"]').daterangepicker();

    // attach event to on merchant dropdown change
    $('.merchant_id').change(function(){
    	// clear channel dropdown
        $('.channel_id').find('option').remove();
        // get list of channels based on merchant
        if($(this).val() > 0){
            $.ajax({
                url: "/admin/channels/merchant/"+$(this).val(),
                data: {warehouse: false},
                type: 'GET',
                beforeSend: function(data) {
                	// display loading prompt
                	waitingDialog.show('Getting channel list...', {dialogSize: 'sm'});
                },
                success: function(data) {
                	if(data.channels==''){
                        // show placeholder
                        $('.channel_id').append('<option>N/A</option>');
                    }else{
                        // loop thru response and build new select options
                        $('.channel_id').append('<option value>{{trans("admin/fulfillment.order_placeholder_channel")}}</option>');	// placeholder
                        var channelOptions = '';
                        $.each(data.channels, function( index, channel ) {
                            channelOptions += '<option value="'+channel.id+'">';
                            channelOptions += channel.name;
                            channelOptions += '</option>';
                        });
                        $('.channel_id').append(channelOptions);
                    }
                },
                complete: function(){
                    waitingDialog.hide();
                }
            });
        }
        else {
        	if (channels.length==0) {
                // show placeholder
                $('.channel_id').append('<option>N/A</option>');
            }
            else {
            	// loop thru response and build new select options
                $('.channel_id').append('<option value>{{trans("admin/fulfillment.order_placeholder_channel")}}</option>');	// placeholder
                var channelOptions = '';
                $.each(channels, function( index, name ) {
                    channelOptions += '<option value="'+index+'">';
                    channelOptions += name;
                    channelOptions += '</option>';
                });
                $('.channel_id').append(channelOptions);
            }
        }
    });

    // End of Search

    // Scan order ID
    $(document).on("keyup", "#scan-order-id", function(e) {
    	if (e.keyCode == 13) {
    		var orderId = $("#scan-order-id").val();
		    	
	    	if (orderId!="") {
	    		$.ajax({
					type:"GET",
					data: {id: orderId},
					url: "{{route('orders.find')}}",
					beforeSend: function() {
						waitingDialog.show('Fetching order details...', {dialogSize: 'sm'});
					},
					success:function(response){
						if (response.found == true) {
							var orderWindow = window.open('{{ URL::to("orders/") }}' + '/' + orderId, '_blank');
							if (orderWindow) {
							    //Browser has allowed it to be opened
							    orderWindow.focus();
							} else {
							    //Browser has blocked it
							    alert('Unable to open order page, please allow popups for this website');
							}
						}	
						else {
							var message = '<div class="alert alert-error alert-dismissible" role="alert">' +
						                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' + 
						                            '<span aria-hidden="true">&times;</span>' +
						                        '</button>' + 
						                        'Order ID ('+orderId+') was not found in the system. Please verify that the correct order ID was entered.' +
						                    '</div>';
							$(".errors").html(message);
						}	
					},
					complete: function() {
						waitingDialog.hide();
					},
				});
	    	}
    	}
    });


	// Checkbox-related code
    $('#salesTable tbody').on( 'click', 'tr td.check input[name=sale]', function () {
    	var index = selectedSales.indexOf(this.value);
		if (index==-1) {
			selectedSales.push(this.value);
		}
		else {
			selectedSales.splice(index, 1);
		}
		checkSelectAll();
	});

    $("input[name='select-all']").on('click', function() {
    	var checkboxes = $("input[name='sale']");
    	if (this.checked) {   		
    		for (var i = 0; i < checkboxes.length; i++) {		    			
    			// tick checkbox
    			$(checkboxes[i]).prop("checked", true);
    			
    			// add sale to array of selected sales
    			if (selectedSales.indexOf(checkboxes[i].value)==-1)
    				selectedSales.push(checkboxes[i].value);
    		}		    	
    	}
    	else {
    		for (var i = 0; i < checkboxes.length; i++) {
    			// untick checkbox
    			$(checkboxes[i]).prop("checked", false);
    			var index = selectedSales.indexOf(checkboxes[i].value);
    			// add sale to array of selected sales
    			if (selectedSales.indexOf(checkboxes[i].value)!=-1)
    				selectedSales.splice(index, 1);
    		}
    	}
    });


    // Export post laju consignment
	$("#post-laju").on('click', function() {
		// if there aren't any selected sales, export all sales with 'Paid' status
    	/*$.ajax({
			url: "{{route('orders.index')}}",
			data: {sales: selectedSales},
			type: 'POST',
			dataType: 'json',
			success: function(data) { 
				
			}
		});*/
		//$("input[name='selected-sales']").val(selectedSales);
		//$("#exportConsignment").submit();
    	
	});

	/*$("#export").on('click', function() {
    	
	});*/
	
	$( "tbody" ).on({
		mouseenter: function() {
			$( this ).parent().addClass( "hover" );
		}, mouseleave: function() {
			$( this ).parent().removeClass( "hover" );
		}
	}, 'tr td:not(.check)');

	$('#salesTable tbody').on( 'click', 'tr td:not(.check)', function () {
		var cell = salesTable.cell(this).index().row;
		var data = salesTable.row(cell).data();
	    if (data!==undefined) {
	    	if(channel_id.length > 0) {
				window.open('orders/' + data.id, '_blank');
	    	} else {
	    		window.open('{{ URL::to("orders/") }}' + '/' + data.id, '_blank');
	    	}
	    }  
	});
	// load levels
	function loadLevels() {
		$.ajax({ 
			url: '{{route("orders.level")}}',
			dataType: 'json',
			data : {channel_id: (channel_id.length > 0 ? channel_id : 0)},
			type: 'GET',
			success: function(response){
				// console.log('check level!');
				$.each(response, function(index,value) {
					if (value!=null)
						$("#level-"+index).html(value);
					else
						$("#level-"+index).html('0');
				});
			},
			error: function(response) {
		    	console.log("An error has occurred.");
		    }
		});
		setTimeout(loadLevels, 5*60000); // 5 minutes interval
	}

    // load counters
    function loadCounters() {
    	$.ajax({ 
			url: '{{route("orders.count")}}',
			dataType: 'json',
			type: 'GET',
			data: {merchant_id: $("select[name='merchant_id']").val(), channel_id: (channel_id.length > 0 ? channel_id : $("select[name='channel_id']").val())},
			success: function(response){
				$.each(response, function(index,value) {
					if (value!=null)
						$("#count-"+index).html(value);
					else
						$("#count-"+index).html('0');
				});
			},
			error: function(response) {
		    	console.log("An error has occurred.");
		    }
		});
    }

    setInterval(function() {
    	loadCounters();
    }, 5*60000); // 5 minutes interval
    
    // determine whether to check the select all checkbox 
    function checkSelectAll() {
    	// count number of checked boxes on the page
    	var checkedCheckboxes = $("input[name='sale']:checked").length;
    	if (checkedCheckboxes==salesTable.page.len()) {
    		$("input[name='select-all']").prop("checked", true);
    	}
    	else {
    		$("input[name='select-all']").prop("checked", false);
    	}
    }
});
</script>
@append