@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
    @lang('admin/reports.page_title_tp_reports')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="errors"></div>
    <section class="content-header">
        <h1>@lang('admin/reports.content_header_finance')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            <span class="to-print-header">{{ $tp_details->item->channel_type->name }} > {{ isset($tp_details->item->order->channel->name)?$tp_details->item->order->channel->name: 'N/A' }} > {{ $tp_details->item->id }}</span>
                        </h3>
                        <div class="pull-right">
                            <span class="bold h4">{{ strtoupper($tp_details->item->status) }}</span>&nbsp;&nbsp;
                            @if($tp_details->item->status != 'Verified' && $tp_details->item->status != 'Completed'
                            && $tp_details->item->status != 'Not Found')
                            <button type="button" id="verify" class="btn btn-primary" style="margin-right:5px;"><span style="display: none;" class="pm-spinner"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></span>@lang('admin/reports.button_verify_tp_report_item')</button>
                            @endif
                            @if($tp_details->item->status != 'Completed')
                            <a href="{{ route('admin.tp_reports.edit', ['id' => $tp_details->item->id]) }}" class="btn btn-primary" style="margin-right:5px;">@lang('admin/reports.button_edit_tp_report_item')</a>
                            @endif
                            <a target="_blank" href="{{ route('admin.tp_reports.print', ['id' => $tp_details->item->id]) }}" id="print" class="btn btn-default" style="margin-left: 10px;">@lang('admin/reports.button_print_tp_report_item')</a>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="to-print col-md-12">
                            <div class="col-md-6 col-xs-12">
                                <div>
                                    <h4><u>Order Details</u></h4>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Order ID </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->id)?$tp_details->item->order->id:'N/A' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Order ID </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->tp_order_code }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Order Date </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->tp_order_date)?$tp_details->item->order->tp_order_date:'N/A' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Created Date </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->created_at)?$tp_details->item->order->created_at:'N/A' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Order Status </div><div class="col-xs-12 col-md-8"> : {{ ucwords($tp_details->item->item_status) }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Order Status </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->status)?$tp_details->item->order->status : 'N/A' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Shipped Date </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->shipped_date)?$tp_details->item->order->shipped_date:'N/A' }}</div></div>
                                </div>
                                <div>
                                    <h4><u>Product Details</u></h4>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Item Reference ID </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->tp_item_id }}</div></div>
                                    @if(!empty($tp_details->item->order_item->id))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Item ID </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->id }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Item Status </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->status }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party SKU </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->hubwire_sku }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">HubwireSKU </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->sku->hubwire_sku }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Product Name </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->product->name }}</div></div>
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Arc Item ID </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party SKU </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">HubwireSKU </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Product Name </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    @endif
                                    @if(!empty($tp_details->item->order->id)&&!empty($tp_details->item->order_item->id))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Currency </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order->currency }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Retail Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->unit_price / 1.06) : $tp_details->item->order_item->unit_price), 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Retail Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format($tp_details->item->order_item->unit_price, 2, '.',',') }}  (Arc) | {{ $tp_details->item->unit_price }} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Listing Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->sale_price / 1.06) : $tp_details->item->order_item->sale_price), 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Listing Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format($tp_details->item->order_item->sale_price, 2, '.',',') }} (Arc) | {{ $tp_details->item->sale_price }} (Marketplace)</div></div>
                                    @if($tp_details->item->order_item->tax_inclusive  == 1)
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Sold Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->sale_price / 1.06) : $tp_details->item->order_item->sale_price), 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Sold Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format($tp_details->item->order_item->sold_price, 2, '.',',') }} (Arc) | {{ $tp_details->item->sold_price }} (Marketplace)</div></div>
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Sold Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format($tp_details->item->order_item->sold_price, 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Sold Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format($tp_details->item->order_item->sold_price + $tp_details->item->order_item->tax, 2, '.',',') }} (Arc) | {{ $tp_details->item->sold_price }} (Marketplace)</div></div>
                                    @endif
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Tax Rate </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->tax_rate*100 }} %</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Tax Amount </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->tax }}</div></div>
                                    <!--div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Shipping Fee </div><div class="col-xs-12 col-md-8"> : item shipping fee</div></div-->
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Currency </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Retail Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Retail Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A (Arc) | {{ $tp_details->item->unit_price }} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Listing Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Listing Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A (Arc) | {{ $tp_details->item->sale_price }} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Sold Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Sold Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A (Arc) | {{ $tp_details->item->sold_price }} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Tax Rate </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Tax Amount </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <!--div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Shipping Fee </div><div class="col-xs-12 col-md-8"> : item shipping fee</div></div-->
                                    @endif
                                </div>
                                <div>
                                    <h4><u>Hubwire Commission and Fees</u></h4>
                                    @if(!empty($tp_details->item->order_item->id))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Hubwire Fee </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->hw_fee }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Hubwire Commission </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->hw_commission }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Miscellaneous Fee </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->misc_fee }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Minimum Guarantee </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->min_guarantee }}</div></div>                                
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Hubwire Fee </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Hubwire Commission </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Miscellaneous Fee </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Minimum Guarantee </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    @endif
                                </div>
                                <br/><br/><br/>
                                <div>
                                @if(!empty($tp_details->item->created_by) && !empty($tp_details->item->last_attended_by))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Uploaded By </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->created_by->first_name }} {{ $tp_details->item->created_by->last_name }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Created On </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->created_at }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Last Updated By </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->last_attended_by->first_name }} {{ $tp_details->item->last_attended_by->last_name }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Last Updated On </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->updated_at }}</div></div>
                                @elseif(!empty($tp_details->item->last_attended_by))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Created By </div><div class="col-xs-12 col-md-8"> : System </div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Created On </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->created_at }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Last Updated By </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->last_attended_by->first_name }} {{ $tp_details->item->last_attended_by->last_name }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Last Updated On </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->updated_at }}</div></div>
                                @elseif(empty($tp_details->item->last_attended_by) && empty($tp_details->item->last_attended_by))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Created By </div><div class="col-xs-12 col-md-8"> : System </div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Created On </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->created_at }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Last Updated By </div><div class="col-xs-12 col-md-8"> : N/A </div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Last Updated On </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->updated_at }}</div></div>
                                @endif
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <div>
                                    <h4><u>Third Party Payment Details</u></h4>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Payout Amount </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->net_payout_currency }} {{ $tp_details->item->net_payout }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Channel Fee </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->net_payout_currency }} {{ $tp_details->item->channel_fees }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Shipping Fee </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->net_payout_currency }} {{ $tp_details->item->channel_shipping_fees }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Payment Gateway Fee </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->net_payout_currency }} {{ $tp_details->item->channel_payment_gateway_fees }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Payout Status </div><div class="col-xs-12 col-md-8"> : {{ $paid_status }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Payout Date </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->payment_date}}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Third Party Payout Reference </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->tp_payout_ref}}</div></div>
                                </div>
                                <div>
                                    <h4><u>Hubwire to Merchant Payment Details</u></h4>
                                    @if(!empty($tp_details->item->order_item))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->product->brands->merchant->name }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Brand </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->product->brand_name }}</div></div>
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Brand </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    @endif
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant Payout Amount </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_payout_currency }} {{ $tp_details->item->merchant_payout_amount }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant Payout Status </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_payout_status }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Hubwire Payout Bank </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->hw_payout_bank }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant Payout Date </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_payout_date }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant Payout Payment Reference </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_payout_ref }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Merchant's Bank </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_bank }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Payment Method </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_payout_method }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Invoice No </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->merchant_invoice_no }}</div></div>
                                </div>
                            </div>
                        </div>
                        <div class="to-print col-md-12">
                            <div class="form-group">
                                <div class="table-responsive col-xs-12">
                                    <h4><u>Remarks</u></h4>
                                    <div >
                                        <div class="bold col-xs-12 col-md-2 no-padding">New Remark : </div>
                                        <div class="col-xs-12 col-md-10"><textarea name="remark" id="remark" class="form-control"></textarea></div>
                                    </div>
                                    <div>
                                        <div class=" col-xs-12 col-md-12" style="height: 45px; top: 5px; ">
                                        <button type="button" id="addRemark" class="pull-right btn btn-primary"><span style="display: none;" class="pm-spinner"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></span>Update Remark</button>
                                        </div>
                                    </div>
                                    <table class="table table-hover" style="margin-top: 5px;">
                                        <tbody>
                                            <tr>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Remarks</th>
                                                <th>Created By</th>
                                                <th>Actions</th>
                                            </tr>
                                            @foreach($tp_details->remarks as $remark)
                                                <tr>
                                                @if($remark->type == 'general')
                                                    <td><span class="remark-{{ $remark->id }}-status label label-primary">General</span></td>
                                                @elseif($remark->type == 'error')
                                                    @if($remark->resolve_status == 0)
                                                    <td><span class="remark-{{ $remark->id }}-status label label-danger">Error</span></td>
                                                    @elseif($remark->resolve_status == 1)
                                                    <td><span class="remark-{{ $remark->id }}-status label label-success">Resolved</span></td>
                                                    @endif
                                                @endif
                                                    <td>{{ $remark->created_at }}</td>
                                                    <td>{{ $remark->remarks }}</td>
                                                    <td>{{ $remark->user }}</td>
                                                    <td>
                                                        @if($remark->type == 'error' && $remark->resolve_status == 0)
                                                            <button data-remark-id="{{ $remark->id }}" type="button" class="resolve-remark btn btn-success"><span style="display: none;" class="pm-spinner"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></span> Resolve</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <h4><u>Activity Log</u></h4>
                                <div class="row">
                                    <div class="timeline-container col-xs-12">
                                        @if(!empty($groupedHistory))
                                            <ul class="timeline">
                                                @foreach($groupedHistory as $date => $logs)
                                                    <li class="time-label">
                                                        <span class="bg-purple">
                                                            {{$date}}
                                                        </span>
                                                    </li>
                                                    @foreach($logs as $log)
                                                        <li>
                                                            <i class="fa bg-blue fa-asterisk"></i>
                                                            <div class="timeline-item">
                                                                <span class="time">
                                                                    <i class="fa fa-clock-o"></i>
                                                                    <time class="timeago" datetime="{{$log->created_at}}">{{$log->created_at}}</time>
                                                                    by {{$log->user}}
                                                                </span>
                                                                <h3 class="timeline-header">
                                                                    Field <b>{{ $log->field }}</b> was updated from <b style="color:red;">{{ $log->old_value == '0000-00-00' || empty($log->old_value) ? 'N/A' : $log->old_value }}</b> to <b style="color:green;">{{ $log->new_value == '0000-00-00' || empty($log->new_value) ? 'N/A' : $log->new_value }}</b>
                                                                </h3>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        @else
                                            <div>Timeline not available for this report.</div>
                                        @endif
                                    </div>
                                </div>
                                @if($tp_details->item->status == 'Unverified' or $tp_details->item->status == 'Not Found')
                                <button type="button" id="btn_delete" class="btn btn-danger">
                                    @lang('admin/reports.button_delete_tp_report_item')
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    {!! Form::open(array('url' => route('admin.tp_reports.destroy', [$tp_details->item->id]), 'method' => 'DELETE', 'id' => 'delete-form')) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
    <div id="jsForPrintWindow" class="hide">
        $(document).ready(function(){
            setTimeout( function() {
                window.print();
            }, 200);
        });
    </div>
    <div id="cssForPrintWindow" class="hide">
        @page {size:portrait;
        body {
            font-size:12px !important;
            width: ;
        }
    </div>
@stop


@section('footer_scripts')
<style>
.timeline > li > .timeline-item > .timeline-header{
    border: 1px solid #f4f4f4;
}
</style>
<script src="{{ asset('js/jquery.timeago.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    $('#verify').click(function() {
        $('#verify').prop('disabled', true).find('.pm-spinner').show();
        var btn = $(this);
        $.ajax({
            url: "{{ route('admin.tp_reports.verify', ['id' => $tp_details->item->id]) }}",
            type: 'GET',
            success: function(response){
                // console.log(response);
                if(response.success){
                    $('#verify').hide();
                    window.location.href = "{{ route('admin.tp_reports.show', ['id' => $tp_details->item->id]) }}";
                }else{
                    window.location.href = "{{ route('admin.tp_reports.show', ['id' => $tp_details->item->id]) }}";
                }
            },
            complete: function(data){
                btn.find('.pm-spinner').hide();
            }
        });
    });

    $('#addRemark').click(function() {
        $('#addRemark').prop('disabled', true).find('.pm-spinner').show();
        var id = "{{ $tp_details->item->id }}";
        var remark = document.getElementById("remark").value;

        $.ajax({
            data: {remark: remark},
            url: '/admin/reports/third_party_reports/'+id+'/addRemark',
            type: 'POST',
            success: function(response){
                //console.log(response);
                if(response.success){
                    window.location.href = "{{ route('admin.tp_reports.show', ['id' => $tp_details->item->id]) }}";
                }else{
                    window.location.href = "{{ route('admin.tp_reports.show', ['id' => $tp_details->item->id]) }}";
                }
            },
        });
    });

    $('.resolve-remark').on('click', function(){
        // admin.tp_reports.resolve_remark
        $(this).prop('disabled', true).find('.pm-spinner').show();
        var btn = $(this);
        $.ajax({
            url: "/admin/reports/third_party_reports/remark/"+btn.data('remark-id')+"/resolve",
            type: 'POST',
            success: function(response){
                if(!response.error){
                    btn.hide().find('.pm-spinner').remove();
                    $('.remark-'+response.id+'-status').removeClass('label-danger').addClass('label-success').html('Resolved');
                }else{
                    btn.prop('disabled', false).find('.pm-spinner').remove();
                }
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                alert(errorThrown);
                btn.prop('disabled', false);
            }
        });
    });

    // Prompt delete confirmation
    $(document).on('click', '#btn_delete', function (e) {
        //e.preventDefault();
        var delete_channel = confirm('Are you sure you want to delete this third party report?');
        if(delete_channel){
            $('#delete-form').submit();
        }else{
            return false;
        }
    });
});
</script>
@append