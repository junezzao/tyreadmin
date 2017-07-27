
@extends('layouts.master')

@section('title')
	@lang('admin/fulfillment.page_title_view_order', ['order_id' => $order->id])
@stop

@section('header_scripts')
<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}">
<link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.css',env('HTTPS',false)) }}">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
@append

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/fulfillment.content_header_view_order')</h1>
      @include('partials.breadcrumb')
    </section>
    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/fulfillment.box_header_view_order', ['channel' => $channel->name, 'order' => $order->id])</h3>
	            	</div><!-- /.box-header -->
	            		@if(!empty($notes))
	              		<button type="button" class="scrollToBottom btn btn-default text-center"><span class="fa fa-exclamation-triangle"></span><br>Notes</button>
	              		@endif
	            	<div class="box-body">
	            		<div class="col-xs-12">
		            		<div class="row">
		            			<div class="col-xs-12 col-md-4 pull-right">
			              			<div class="col-lg7 col-xs-12 noPrint" id="options">
			              				<div id="options" class="pull-right">
											<div class="dropdown">
												<button type="button" id="btn_print_options" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print" aria-hidden="true"></i></button>
												<ul class="dropdown-menu dropdown-menu-right">
													@if ((strcasecmp($channel->channel_type->name, 'Lazada') == 0 || strcasecmp($channel->channel_type->name, 'LazadaSC') == 0 || strcasecmp($channel->channel_type->name, 'Zalora') == 0) && !empty($order->consignment_no))
														<li>
															<a href="#" class="print-option" data-url="{{ route('orders.print_document', ['document_type' => 'shipping_labels', 'order_id' => $order->id]) }}">
																@lang('admin/fulfillment.order_print_options_shipping_labels')</a>
														</li>
														<li>
															<a href="#" class="print-option" data-url="{{ route('orders.print_document', ['document_type' => 'invoice', 'order_id' => $order->id]) }}">
																@lang('admin/fulfillment.order_print_options_invoices')</a>
														</li>
													@endif
													<li>
														<a href="#" class="print-option" data-url="{{ route('orders.print_document', ['document_type' => 'tax_invoice', 'order_id' => $order->id]) }}">
															@lang('admin/fulfillment.order_print_options_tax_invoice')</a>
													</li>
													@if($order->hasReturns)
													<li><a href="#" class="print-option" data-url="{{ route('orders.print_document', ['document_type' => 'credit_note', 'order_id' => $order->id]) }}">@lang('admin/fulfillment.order_print_options_credit_note')</a></li>
													@endif
													<li><a href="{{ route('orders.print.order_sheet', ['order_id' => $order->id]) }}" target="_blank">@lang('admin/fulfillment.order_print_options_order_sheet')</a></li>
													<li><a href="{{ route('orders.print.return_slip', ['order_id' => $order->id]) }}" target="_blank">@lang('admin/fulfillment.order_print_options_return_slip')</a></li>
												</ul>
											</div>
								        </div>
									</div>
				              		<div id="barcode-container" class="pull-right">
										<div>
											{!! DNS1D::getBarcodeSVG($order->id, "C39") !!}
											<br>
											<p class="text-center">{{ $order->id }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
		            			<div class="col-md-8 col-xs-12">
		            				<h4>Order Details</h4>
		            				<div class="form-group">
			            				<div class="bold col-xs-12 col-md-3"><strong>Order No.: </strong></div>
			            				<div class="col-xs-12 col-md-2"> {{ $order->id }} </div>
			            			</div>
		            				<div class="form-group">
			            				<div class="bold col-xs-12 col-md-3"><strong>Third Party Order: </strong></div>
			            				<div class="col-xs-12 col-md-3"> {{ $order->tp_order_code }} <span class="label label-warning">{{ $channel->channel_type->name }}</span></div>
			            			</div>
		            				<div class="form-group">
			            				<div class="bold col-xs-12 col-md-3"><strong>Order Date: </strong></div>
			            				<div class="col-xs-12 col-md-3"> {{ $order->created_at }} </div>
			            			</div>
		            				<div class="form-group">
			            				<div class="bold col-xs-12 col-md-3"><strong>Status: </strong></div>
			            				<div class="col-xs-12 col-md-3">
			            					@if($order->status == 'Shipped' || $order->status == 'Completed' || !is_null($channel_id) || $order->status == 'New' || $order->cancelled_status == 1)
				            					{!! Form::select('status', $statusList, $order->status, ['class' => 'form-control', 'id' => 'status', 'disabled']) !!}
				            				@else
				            					{!! Form::select('status', $statusList, $order->status, ['class' => 'form-control select2-nosearch', 'id' => 'status']) !!}
				            				@endif
				            			</div>
			            			</div>
		            				<div class="form-group">
			            				<div class="bold col-xs-12 col-md-3"><strong>Paid Status: </strong></div>
		            					<div class="col-xs-12 col-md-3">
			            					@if($order->paid_status === true || !is_null($channel_id))
										        {!! Form::select('paid_status', $paidStatusList, $order->paid_status, ['class' => 'form-control', 'id' => 'paid_status', 'disabled']) !!}
										    @else
										        {!! Form::select('paid_status', $paidStatusList, $order->paid_status, ['class' => 'form-control select2-nosearch', 'id' => 'paid_status']) !!}
										   	@endif
									   </div>
		            				</div>
		            				<div class="form-group">
			            				<div class="bold col-xs-12 col-md-3"><strong>Order Cancelled ? </strong></div>
		            					<div class="col-xs-12 col-md-4">
			            					@if($order->cancelled_status == true)
			            						{!! Form::checkbox('cancelled_status', true, true, ['disabled'] ) !!}
			            					@else
			            						@if(!is_null($channel_id))
			            							{!! Form::checkbox('cancelled_status', true, false, ['disabled'] ) !!}
			            						@else
			            							{!! Form::checkbox('cancelled_status', true ) !!}
			            						@endif
			            					@endif
			            					<span id="cancelled-text" class="text-red">@if($order->cancelled_status == true) This order has been cancelled @endif </span>
									   </div>
		            				</div>
		            			</div>
		            			<div class="col-md-4 col-xs-12">
		            				<div id="consignment-container">
		            					<div class="form-group">
		            						{!! Form::label('shipping_provider', trans('admin/fulfillment.order_label_shipping_provider'), ['class'=>'control-label']) !!}
		            						{!! Form::text('shipping_provider', $order->shipping_provider, ['class' => 'form-control', 'readonly' =>'readonly']) !!}
		            					</div>
		            					<div class="form-group">
		            						{!! Form::label('consignment_no', trans('admin/fulfillment.order_label_consignment_no'), ['class'=>'control-label']) !!}
		            						<div class="row">
		            							<div class="col-md-10 col-xs-12">
		            								@if($order->consignment_no != '' || !is_null($channel_id))
		            									{!! Form::text('consignment_no', $order->consignment_no, ['class' => 'form-control', 'id' => 'consignment_no', 'placeholder' => trans('admin/fulfillment.order_placeholder_consignment_no'), 'readonly']) !!}
		            								@else
					            						{!! Form::text('consignment_no', $order->consignment_no, ['class' => 'form-control', 'id' => 'consignment_no', 'placeholder' => trans('admin/fulfillment.order_placeholder_consignment_no')]) !!}
					            					@endif
		            							</div>
		            							<div class="col-md-2"><button id="send-consignment-btn" class="btn btn-info" @if($order->consignment_no != '' || !is_null($channel_id)) style="visibility:hidden" @endif>Send</button></div>
		            						</div>
		            					</div>
		            					<div class="form-group" id="notif-date-container" @if($order->consignment_no == '') style="visibility:hidden" @endif>
		            						{!! Form::label('notification_date', trans('admin/fulfillment.order_label_notification_date'), ['class'=>'control-label']) !!}
		            						<div style="margin-left:15px" name="notif_date">{{ $order->shipping_notification_date }}</div>
		            					</div>
		            				</div>
		            			</div>
		           			</div>
		            		<div class="row">
		            			<div class="col-md-4 col-xs-12">
									<div class="box">
									    <div class="box-header">Shipping Details</div>
									    <div class="box-body">
									    	<p class="info-box-text"><span class="bold">Name:</span> <span class="block address-span">{{ $order->shipping_recipient }} </span></p>
									    	<p class="info-box-text"><span class="bold">Contact Number:</span> {{ $order->shipping_phone }}</p>
									    	<p class="info-box-text"><span class="bold">Address:</span><span class="block address-span"> {{ $order->shipping_street_1 }} <br/> {{ $order->shipping_street_2 }} <br/> {{ $order->shipping_city }} <br/> {{ $order->shipping_postcode}} {{ $order->shipping_state }}<br/>{{ $order->shipping_country }}</span></p>
									    </div>
									</div>
								</div>

								<div class="col-md-4 col-xs-12">
									<div class="box">
										<div class="box-header">Billing Details</div>
									  	<div class="box-body">
									    	<p class="info-box-text"><span class="bold">Name:</span> <span class="block address-span">{{ (!is_null($member) ? $member->member_name : '') }} </span></p>
									    	<p class="info-box-text"><span class="bold">Email:</span> {{ (!is_null($member) ? $member->member_email : '')}}</p>
									    	<p class="info-box-text"><span class="bold">Contact Number:</span> {{ (!is_null($member) ? $member->member_mobile : '') }}</p>
									    	<!-- Insert Address -->
									    </div>
									</div>
								</div>
								<div class="col-md-4 col-xs-12">
									<div class="box">
										<div class="box-header">Payment Details</div>
									  	<div class="box-body">
									  		<p class="info-box-text">
									  			<span class="bold">Payment Type:</span> {{ $order->payment_type }}
									  		</p>
											<p class="info-box-text">
									  			<span class="bold">Promotion Code:</span> {{ $order->promotions }}
									  		</p>
									  		<p class="info-box-text"><span class="bold">Amount Paid:</span>
									  		{{ $order->currency }} {{ number_format($order->total, 2) }}</p>
									  	</div>
									</div>
								</div>
		            		</div>
		            		<hr/>
		            		<div class="row">
		            			<div class="form-group col-xs-12 col-md-3">
		            				@if(is_null($channel_id))
			            				{!! Form::open(array('id' => 'find-item-form')) !!}
			            					{!! Form::text('hw_sku', null, ['class' => 'form-control', 'id' => 'find-item', 'placeholder' => trans('admin/fulfillment.order_placeholder_find_item')]) !!}
			            				{!! Form::close() !!}
		            				@endif
		            			</div>
		            			<div class="col-xs-12">
		            				<table class="table table-striped" id="order-details-table">
						                <tbody>
						                	<tr>
							                  <th style="width: 10px">#</th>
							                  <th></th>
							                  <th>SKU</th>
							                  <th>Location</th>
							                  <th>Product Name</th>
							                  <th class="text-center">Quantity</th>
							                  <th class="text-center">Listing Price<br/> <span class="small italic"><em>(inclusive GST)</em></span></th>
							                  <th class="text-center">Line Total<br/> <span class="small italic"><em>(inclusive GST)</em></span></th>
							                  <th class="text-center">Credits</th>
							                  <th class="text-center">Status</th>
							                  <th></th>
							                </tr>
							                @foreach($items as $item)
						                	<tr class="counter" id="order-item-id-{{ $item->item->id }}">
							                  <td style="width: 10px" class="item-id" data-id="{{ $item->item->id }}"></td>
							                  <td>@if(!empty($item->product->media[0]))<img src="{{ $item->product->media[0]->media->media_url.'_55x79' }}"/>@endif</td>
							                  <td>
							                  	<a href="{{route('products.inventory.edit', ['id'=>$item->product->id, 't' => 'channel'])}}" target="_blank">
							                  		{{ $item->item->ref->sku->hubwire_sku }}
							                  	</a>
							                  </td>
							                  <td>{{ $item->item->ref->channel_sku_coordinates }}</td>
							                  <td>{{ $item->product->name }}</td>
							                  <td class="text-center">
							                  	{{ $item->item->original_quantity }}
							                  	@if(!empty($item->returns))
								                  	@if($item->returns['Restocked'] > 0)
								                  		<br/><span class="small"><i>(Restocked {{ $item->returns['Restocked'] }})</i></span>
								                  	@endif
								                  	@if($item->returns['In Transit'] > 0)
								                  		<br/><span class="small"><i>(In Transit {{ $item->returns['In Transit'] }})</i></span>
								                  	@endif
								                  	@if($item->returns['Rejected'] > 0)
								                  		<br/><span class="small"><i>(Rejected {{ $item->returns['Rejected'] }})</i></span>
								                  	@endif
							                  	@endif
							                  </td>
							                  <td class="text-center">{{ $order->currency }} {{ ($item->item->sale_price > 0) ? $item->item->sale_price : $item->item->unit_price }}</td>
							                  <td class="text-center">{{ $order->currency }} {{ number_format($item->item->original_quantity * $item->item->sold_price + (($item->item->tax_inclusive) ? 0 : $item->item->tax), 2) }}</td>
							                  <td class="text-center"></td>
							                  <td class="text-center" id="item-status-{{ $item->item->id }}">
							                  @if(!$order->hasShipped && strtolower($item->item->status) == 'out of stock')
							                  	{!! Form::open(array('class' => 'item-status-form')) !!}
							                  		<input type="hidden" name="order-item-id" value="{{ $item->item->id }}">
							                  		<a href="#/" data-toggle="statusPopover">{{ $item->item->status }}</a>
							                  	{!! Form::close() !!}
							                  @else
						                 		{{ $item->item->status }}
							                  @endif
							                  </td>
							                  <td class="text-center">
							                  	@if(is_null($channel_id))
													@if(((!empty($item->returns) && ($item->returns['Restocked'] + $item->returns['In Transit'] + $item->returns['Rejected'] < $item->item->quantity)) || empty($item->returns)) && $item->item->status != 'Cancelled')
									                  	@if ( ($order->status != 'Completed' && $order->status != 'Shipped') || (($order->status == 'Completed' || $order->status == 'Shipped') && strtolower($item->item->status) == 'out of stock') )
									                  		<button type="button" id="btn_cancel_item" class="btn btn-default btn-table cancel-return-btn" data-toggle="popoverCancel">@lang('admin/fulfillment.button_cancel')</button>
									                  	@elseif($item->item->status != 'Out of Stock')
									                  		<button type="button" id="btn_return_item" class="btn btn-default btn-table cancel-return-btn" data-toggle="popover">@lang('admin/fulfillment.button_return')</button>
									                  	@endif
										            @endif
									            @endif
							                  </td>
							                </tr>
							                @endforeach
							                <tr class="plain-bg-row top-border">
							                	<td colspan="5"></td>
								                <td colspan="2"><span class="pull-right">Sub-total <span class="small italic"><em>(inclusive GST):</em></span></span></td>
								                <td class="text-center">{{ $order->currency }} {{ $order->subtotal }}</td>
								                <td></td>
								                <td></td>
								                <td></td>
							                </tr>
							                <tr class="plain-bg-row">
							                	<td colspan="5"></td>
								                <td colspan="2"><span class="pull-right">Shipping Fee <span class="small italic"><em>(inclusive GST):</em></span></span></td>
								                <td class="text-center">{{ $order->currency }} {{ $order->shipping_fee }}</td>
								                <td></td>
								                <td></td>
								                <td></td>
							                </tr>
							                <tr class="plain-bg-row">
							                	<td colspan="5"></td>
								                <td colspan="2"><span class="pull-right">Total <span class="small italic"><em>(excluding GST):</em></span></span></td>
								                <td class="text-center">{{ $order->currency }} {{ number_format($order->total / 1.06, 2) }}</td>
								                <td></td>
								                <td></td>
								                <td></td>
							                </tr>
							                <tr class="plain-bg-row">
							                	<td colspan="5"></td>
								                <td colspan="2"><span class="pull-right">GST (6%) :</span></td>
								                <td class="text-center">{{ $order->currency }} {{ number_format(($order->total / 1.06) * 0.06, 2) }}</td>
								                <td></td>
								                <td></td>
								                <td></td>
							                </tr>
							                <tr class="plain-bg-row">
							                	<td colspan="5"></td>
								                <td colspan="2"><span class="pull-right">Total <span class="small italic"><em>(inclusive GST):</em></span></span></td>
								                <td class="text-center">{{ $order->currency }} {{ $order->total }}</td>
								                <td></td>
								                <td></td>
								                <td></td>
							                </tr>
							                <tr class="plain-bg-row top-border">
							                	<td colspan="5"></td>
								                <td colspan="2"><span class="pull-right">Total GST charged at <br/> Standard Rate (6%) </span></td>
								                <td class="text-center">{{ $order->currency }} {{ number_format(($order->total / 1.06) * 0.06, 2) }}</td>
								                <td></td>
								                <td></td>
								                <td></td>
							                </tr>
						                </tbody>
		            				</table>
		            			</div>
		            		</div>
		            	</div>
		            	<hr/>
						<div class="col-xs-12 tab-container">
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs">
									<li class="active"><a data-toggle="tab" href="#tab-notes">@lang("admin/fulfillment.order_tabs_label_notes")</a></li>
									<li><a data-toggle="tab" href="#tab-timeline">@lang("admin/fulfillment.order_tabs_label_order_history")</a></li>
								</ul>
								<div class="tab-content">
									<div id="tab-notes" class="tab-pane active">
										<div class="row">
											{!! Form::open(array('url' => route('order.notes.create', [$order->id]), 'method' => 'POST', 'id' => 'create-order-note')) !!}
												<div class="form-group">
													<div class="col-xs-6">
														{!! Form::textarea( 'notes', null, ['class' => 'form-control', 'placeholder' => trans('admin/fulfillment.order_placeholder_add_note'), 'rows'=>'6'] ) !!}
														<div class="error">{{ $errors->first('notes') }}</div>
													</div>
													<div class="col-xs-6">
														<div class="col-xs-10">
															{!! Form::select('note_type', config('globals.order_notes_type'), null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('admin/fulfillment.order_placeholder_note_type'))) !!}
															<div class="error">{{ $errors->first('note_type') }}</div>
														</div>
														<div class="col-xs-2 pull-right">
															<button type="Submit" class="btn btn-success">
			                                                    @lang("admin/fulfillment.button_save")
			                                                </button>
														</div>
													</div>
												</div>
                        					{!! Form::close() !!}
										</div>
										<div class="row order-notes">
											@foreach($notes as $index => $note)
												<div class="col-xs-3 order-note">
													<div class="box box-solid @if($note->note_type=="Attention Required" && $note->note_status!="Done") box-warning @elseif($note->note_status == 'Done') box-success @else box-info @endif">
														<div class="box-body @if($note->note_type=="Attention Required" && $note->note_status!="Done") bg-warning @elseif($note->note_status == 'Done') bg-success @else bg-info @endif disabled">
															<p>
																{{$note->notes}}
																<div>
																	<small>
																		<i>
																			- {{$note->first_name}} on {{$note->created_at}}
																		</i>
																	</small>
																</div>
																@foreach($note->childrenNotes as $cNote)
																	<div class="child-note">
																		<p>
																			{{$cNote->notes}}
																			<div>
																				<small>
																					<i>
																						- {{$cNote->first_name}} on {{$cNote->created_at}}
																					</i>
																				</small>
																			</div>
																		</p>
																	</div>
																@endforeach
															</p>
															@if($note->note_type=="Attention Required" && $note->note_status!="Done")
																<p>
																	<a data-type="done" data-last-id="{{$note->lastID}}" data-parent-id="{{$note->id}}" data-toggle="modal" data-target="#commentModal" class="btn btn-success note-action">@lang("admin/fulfillment.button_done")</a>
																	<a data-type="comment" data-last-id="{{$note->lastID}}" data-parent-id="{{$note->id}}" data-toggle="modal" data-target="#commentModal" class="btn btn-default note-action">@lang("admin/fulfillment.button_comment")</a>
																</p>
															@endif
														</div>
													</div>
												</div>
												@if($index % 4 == 3)
													<div class="clearfix"></div>
												@endif
											@endforeach
										</div>
									</div>
									<div id="tab-timeline" class="tab-pane">
										<div class="timeline-container">
											@if(!empty($groupedHistory))
												<ul class="timeline">
													@foreach($groupedHistory as $date => $records)
														<li class="time-label">
															@if($date == 'Invalid date')
																<span class="bg-red">
																	{{$date}}
																</span>
															@else
																<span class="bg-purple">
																	{{$date}}
																</span>
															@endif
														</li>
														@foreach($records as $record)
															<li>
																<i class="fa {{$eventClasses[$record->event]}}"></i>
																@if($record->event == 'Status Updated')
																	<div class="timeline-item">
																		<span class="time">
																			<i class="fa fa-clock-o"></i>
																			<time class="timeago" datetime="{{$record->created_at}}">{{$record->created_at}}</time>
																			by {{$record->user_name}}
																		</span>
																		<h3 class="timeline-header">
																			{{$record->event}} : [{{$record->description}}]
																		</h3>
																	</div>
																@else
																	<div class="timeline-item collapsible collapsed">
																		<span class="time">
																			<i class="fa fa-clock-o"></i>
																			<time class="timeago" datetime="{{$record->created_at}}">{{$record->created_at}}</time>
																			by {{$record->user_name}}
																		</span>
																		<h3 class="timeline-header">
																			{{$record->event}}
																		</h3>
																		<div class="timeline-body">
																			{{$record->description}}
																		</div>
																		<div class="timeline-footer">
																			<i>By {{$record->user_name}}</i>
																			<i> on {{$record->created_at}}</i>
																		</div>
																	</div>
																@endif
															</li>
														@endforeach
													@endforeach
												</ul>
											@else
												<div>Timeline not available for this order.</div>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
	            	</div>
	            </div>
	        </div>
	    </div>
	</section>
	@include('orders.note-comment-modal')
@stop

@section('footer_scripts')
<script src="{{ asset('js/jquery.timeago.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<style type="text/css">
	.popover-buttons-div {
    	text-align: center;
    	margin-top: 10px;
	}
	.popoverCancel-buttons-div {
    	text-align: center;
    	margin-top: 10px;
	}
	.order-notes .double-space {
		display:block;
		margin-bottom: 20px;
	}
	.order-notes .child-note{
		margin-top: 20px;
	}
	.nav-tabs-custom > .nav-tabs > li.active > a, .nav-tabs-custom > .nav-tabs > li.active:hover > a{
		background-color: #ecf0f5;
	}
	.nav-tabs-custom > .tab-content{
		background-color: #ecf0f5;
	}
	.collapsible .timeline-header{
		cursor: pointer;
	}
	.collapsed .timeline-body, .collapsed .timeline-footer{
		display: none;
	}
	.order-note .box{
		word-wrap: break-word;
	}
	.scrollToBottom{ 
		background: #F9091B;
		font-size: 15px;
		font-weight: bold;
		color: white;
		position:fixed;
		top: 110px;
		right: 15px;
		z-index:999;
	}
	.scrollToBottom:hover{
		color: #444;
	}
	.scrollToBottom > .fa{ 
		font-size: 30px;
	}
</style>

<script type="text/javascript">
var channelType = "{{ $order->channel->channel_type_id }}";
var orderId = "{{ $order->id }}";

function processCancelReturn(el) {
	var btnName = $(el).attr('name');
	var trigger = $(el).closest('td').find('[data-toggle="popover"],[data-toggle="popoverCancel"],[data-toggle="statusPopover"]').first();
	var returnReason = '';


	// get dropdown value

	if (btnName == 'btn_cancel') {
		trigger.popover('hide');
	}
	else if (btnName == 'btn_confirm') {
		var itemId = $(el).closest('tr').find('.item-id').attr('data-id');
		var url = orderId + '/item/' + itemId;

		if (trigger.attr('id') == 'btn_cancel_item') {
			url += '/cancel';
		}
		else {
			url += '/return';
			returnReason = $('#return_reason').val();
		}

		if (channelType == 1) {
			var storeCredit = $('input[name=store_credit]').val();

			url += '?store_credit=' + storeCredit;

			if(returnReason != ''){
				url += '&return_reason=' + returnReason;
			}

		}else if(returnReason != ''){
			url += '?return_reason=' + returnReason;
		}

		window.location = url;
	}
}

function timelineCollapse(timelineItem){
	var timelineCollapsible = timelineItem.children('.timeline-body, .timeline-footer');
	if (!timelineItem.hasClass("collapsed")) {
		//Hide the content
		timelineCollapsible.slideUp(500, function () {
			timelineItem.addClass("collapsed");
		});
	} else {
		//Show the content
		timelineCollapsible.slideDown(500, function () {
			timelineItem.removeClass("collapsed");
		});
	}
}

function displayAjaxError(msg){
    var msg = msg || 'An error has occured, please try again later.';
    var errorDiv = '<div class="dialog-remove-on-hide">'+msg+'</div>';
    $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
    $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
    $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
    $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
}

function displayAjaxSuccess(){
    $('#loading-prompt-dialog .modal-content .modal-header h3').html('Success!');
    $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-success');
    $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
    setTimeout(function () {waitingDialog.hide();}, 500);
}

function resetTrClass(){
	$('#order-details-table tbody tr').removeClass('success').find('td.success:first-child').addClass('sorting_1').removeClass('success');
	$('#order-details-table tbody tr:nth-child(even)').addClass('even');
	$('#order-details-table tbody tr:nth-child(odd)').addClass('odd');
}

jQuery(document).ready(function($){
	// for order history time ago
	$("time.timeago").timeago();
	$('.timeline-item').on('click', function(){
		var element = $(this);
		timelineCollapse(element);
	});
	var popoverContent = getPopoverContent();
	var popoverCancelContent = getPopoverCancelContent();
	var itemStatusPopoverContent = getItemStatusPopoverContent();

	$('[data-toggle="popover"]').popover({
	  trigger: 'click',
	  title: 'Return Confirmation',
	  placement: 'top',
	  html: 'true',
	  content: popoverContent
	});
	$('[data-toggle="popoverCancel"]').popover({
	  trigger: 'click',
	  title: 'Confirmation',
	  placement: 'top',
	  html: 'true',
	  content: popoverCancelContent
	});

	$('[data-toggle="statusPopover"]').popover({
	  trigger: 'click',
	  placement: 'top',
	  html: 'true',
	  content: itemStatusPopoverContent,
	});

	$('.cancel-return-btn').on('click', function (e) {
	    $('.cancel-return-btn').not(this).popover('hide');
	});

	$('body').on('click', function (e) {
	    $('[data-toggle=popover],[data-toggle=popoverCancel],[data-toggle=statusPopover]').each(function () {
	        // hide any open popovers when the anywhere else in the body is clicked
	        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
	            $(this).popover('hide');
	        }
	    });
	});

	// Order notes add a comment modal form submit
	$('#create-order-note-modal').submit(function(event){
		$('#add-comment-error').html('');
		if($.trim($('#add-comment-text-area').val()) == ''){
			$('#add-comment-error').html('Please enter a comment.');

			return false;
		}

	});

	// Update order item submit function
	$('.item-status-form').submit(function(event){
		event.preventDefault();
		$('[data-toggle=statusPopover]').popover('hide');
		var orderItemId = $(this).find('input[name=order-item-id]').val();
		//console.log(orderItemId);

		var formData = {
            'order-item-id'		: orderItemId,
        };
        waitingDialog.show('Updating item status...', {dialogSize: 'sm'});
		// perform ajax
		$.ajax({
			method: 'POST',
			url: '{{route('order.item.updateStatus', [$order->id])}}',
			data: formData,
			success: function(response) {
				if(!response.success) {
					var errorMsg = '';
                    $.each(response.error, function(index, item){
                        errorMsg += '<li>'+item+'</li>';
                    });
                    var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                    displayAjaxError(error);
				}else{
					$('#item-status-'+response.response.id).html(response.response.status);
					displayAjaxSuccess();
				}
			},
		});
	});

	$('#find-item').focus();

	$('#find-item-form').submit(function(event){
		var hwSku = $.trim($("#find-item").val());
		event.preventDefault();
		if($.trim(hwSku) != ''){
			waitingDialog.show('Updating order item...',
				{
					dialogSize: 'sm',
					onHide: function() {
						$('#find-item').focus();
					}
				}
			);

			$.ajax({
				method: 'POST',
				url: '{{route('order.item.pack', [$order->id])}}',
				data: $('input[name=hw_sku]').serialize(),
				success: function(response) {
					console.log(response);
					if(!response.success) {
						var errorMsg = '';
	                    $.each(response.error, function(index, item){
	                        errorMsg += '<li>'+item+'</li>';
	                    });
	                    var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
	                    displayAjaxError(error);
					} else {
						waitingDialog.hide();
						$('#item-status-'+response.response.order_item_id).html(response.response.status);
						resetTrClass();
						$('#order-item-id-'+response.response.order_item_id).removeClass('odd').removeClass('even').addClass('success');
						$('html, body').animate({
					        scrollTop: $('#item-status-'+response.response.order_item_id).offset().top - 100
					    }, 1000);
					}
				},
				complete: function(){
	                $('#find-item').val('');
					$('#find-item').focus();
	            }
			});
		}else{
			alert('Please enter a Hubwire SKU.');
		}
	});

	$('#status').change(function(){
		var currentStatus = "{{ $order->status }}";
		var newStatus = $('#status option:selected').val()

		if(currentStatus == $('#status option:selected').text()){
			alert("New order status cannot be the same as the current order status.");
		}else{
			var data = JSON.stringify({'status' : newStatus});
			waitingDialog.show('Updating order status...', {dialogSize: 'sm'});
			$.ajax({
				method: 'POST',
				url: '/orders/update-status',
				data: { channelId: "{{ $channel->channel_type->id }}", orderId: "{{ $order->id }}", data: data },
				success: function(response){
					if(response.success == true){
						//repopulate with new statuses
						$("#status").empty();
						$.each(response.newStatusList, function(k, v) {
						    $("#status").append($("<option></option>").attr("value", k).text(v));
						});
						$("#status").find('option:eq(0)').prop('selected', true);
						displayAjaxSuccess();
					}else{
						var errorMsg = '';
	                    $.each(response.error, function(index, item){
	                        errorMsg += '<li>'+item+'</li>';
	                    });
	                    var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
	                    displayAjaxError(error);
	                    // set back to 1st status
	                    $("#status").val($("#status option:first").val());
					}
					if($('#status').val() == 31){
						$('#status').prop('disabled', 'disabled');
					}
				}
			});
		}
	});

	$('#paid_status').change(function(){
		// update when unpaid is being used
		var data = JSON.stringify({'paid_status' : $('#paid_status option:selected').val() });
		waitingDialog.show('Updating order status...', {dialogSize: 'sm'});
		$.ajax({
			method: 'POST',
			url: '/orders/update-status',
			data: { channelId: "{{ $channel->channel_type->id }}", orderId: "{{ $order->id }}", data: data },
			success: function(response){
				if(response.success == true){
					if($('#paid_status option:selected').val() == 1)
					$('#paid_status').prop('disabled', 'disabled');
					displayAjaxSuccess();
				}else{
					var errorMsg = '';
                    $.each(response.error, function(index, item){
                        errorMsg += '<li>'+item+'</li>';
                    });
                    var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                    displayAjaxError(error);
                    
				}

			}
		});
	});

	$('#send-consignment-btn').click(function() {
		var channel_type = '{{ $channel->channel_type->name }}';
		if($('input[name=consignment_no]').val().length <= 0) {
			if(channel_type != 'Lazada' && channel_type != 'LazadaSC' && channel_type != 'Zalora') {
				alert('Please input a Consignment No.');
				return;
			}
		}
		waitingDialog.show('Sending consignment number...', {dialogSize: 'sm'});
		$.ajax({
			method: 'POST',
			url: '{{ $order->id }}/send_consignment_number',
			data: $('input[name=consignment_no]').serialize(),
			success: function(response) {
				console.log(response);
				if(!response.success) {
					alert('FAILED --> ' + response.message);
				} else {
					$('input[name=consignment_no]').val(response.tracking_no).attr('readonly', 'readonly');
					$('div[name=notif_date]').text(response.notification_date);
					$('#send-consignment-btn').css('visibility', 'hidden');
					$('div#notif-date-container').css('visibility', 'visible');
				}
			},
			complete: function(){
            	waitingDialog.hide();
            }
		});
	});

	$('input[name=cancelled_status]').change(function(){
		if($('input[name=cancelled_status]').is(":checked")){
			if(confirm('Are you sure you want to cancel the order?') === true){
				waitingDialog.show('Cancelling order...', {dialogSize: 'sm'});
                // ajax call to cancel the order
                var data = JSON.stringify({'cancelled_status' : true});
                $.ajax({
					method: 'POST',
					url: '/orders/cancel-order/{{ $order->id }}',
					success: function(response){
						waitingDialog.hide();
						if(!response.error){
							$(this).attr("disabled", true);
                			$('#cancelled-text').text('This order has been cancelled');
						}else{
							$('input[name=cancelled_status]').prop("checked", false);
							alert(response.error);
						}
					}
				});
            }
            else{
                $(this).removeAttr("checked");
            }
		}
	});

	$('.note-action').on('click', function(){
		var noteType = $(this).data('type');
		var noteId = $(this).data('last-id');
		var noteParentId = $(this).data('parent-id');
		$('#input-note-type').val(noteType);
		$('#input-note-id').val(noteId);
		$('#input-note-parent-id').val(noteParentId);
	});

	$(".print-option").on("click", function() {
		waitingDialog.show('Retrieving data...', {dialogSize: 'sm'});
		var url = $(this).attr('data-url');

		$.ajax({
			url: url,
			method: 'POST',
			success: function( result ) {
				if (result.success) {
					window.open(result.media_url, '_blank');
				}
	        	else {
	        		alert(result.message);
	        	}
	        	waitingDialog.hide();
	    	},
	    	error: function( jqXHR, textStatus, errorThrown ) {
	    		alert(errorThrown);
	    		waitingDialog.hide();
	    	}
		});
	});

	function getPopoverContent() {
		if (channelType == 1) {
			return ('<div>Reason: </div>{!! Form::select("return_reason", config("globals.return_reason"), $order->status, ["class" => "form-control", "id" => "return_reason"]) !!}'
					+ '<input class="form-control" placeholder="@lang("admin/fulfillment.order_placeholder_store_credit")" name="store_credit" type="number" autofocus>'
					+ '<div class="popover-buttons-div"><button type="button" name="btn_confirm" class="btn btn-default btn-table form-inline" style="margin-right:5px;" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_confirm")</button>'
					+ '<button type="button" name="btn_cancel" class="btn btn-default btn-table form-inline" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_cancel")</button></div>');
		}
		else {
			return ('<div>Reason: </div>{!! Form::select("return_reason", config("globals.return_reason"), $order->status, ["class" => "form-control", "id" => "return_reason"]) !!}'
					+'<div class="popover-buttons-div"><button type="button" name="btn_confirm" class="btn btn-default btn-table form-inline" style="margin-right:5px;" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_yes")</button>'
					+ '<button type="button" name="btn_cancel" class="btn btn-default btn-table form-inline" onclick="processCancelReturn(this)" autofocus>@lang("admin/fulfillment.button_no")</button></div>');
		}
	}

	function getPopoverCancelContent() {
		if (channelType == 1) {
			return ('<input class="form-control" placeholder="@lang("admin/fulfillment.order_placeholder_store_credit")" name="store_credit" type="number" autofocus>'
					+ '<div class="popover-buttons-div" style="width:150px"><button type="button" name="btn_confirm" class="btn btn-default btn-table form-inline" style="margin-right:5px;" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_confirm")</button>'
					+ '<button type="button" name="btn_cancel" class="btn btn-default btn-table form-inline" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_cancel")</button></div>');
		}
		else {
			return ('<div class="popover-buttons-div" style="width:150px"><button type="button" name="btn_confirm" class="btn btn-default btn-table form-inline" style="margin-right:5px;" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_yes")</button>'
					+ '<button type="button" name="btn_cancel" class="btn btn-default btn-table form-inline" onclick="processCancelReturn(this)" autofocus>@lang("admin/fulfillment.button_no")</button></div>');
		}
	}

	function getItemStatusPopoverContent(){
		return '\
		<div>\
			Change item status to Picked?\
			<div class="popover-buttons-div">\
				<button type="submit" name="btn_update_item" class="btn btn-default btn-table form-inline" style="margin-right:5px;">@lang("admin/fulfillment.button_confirm")</button>\
				<button type="button" name="btn_cancel" class="btn btn-default btn-table form-inline" onclick="processCancelReturn(this)">@lang("admin/fulfillment.button_cancel")</button>\
			</div>\
		</div>\
		';
	}

	//Check to see if the window is bottom if not then display button
	$(window).scroll(function(){
		var pageHeight = $(document).height();
		if ($(this).scrollTop() < pageHeight*0.4) {
			$('.scrollToBottom').fadeIn();
		} else {
			$('.scrollToBottom').fadeOut();
		}
	});
	
	//Click event to scroll to bottom
	$('.scrollToBottom').click(function(){
		var pageHeight = $(document).height();
		$('html, body').animate({scrollTop : pageHeight},1000);
		return false;
	});
});
</script>
@append