@extends('layouts.print')

@section('header_scripts')
<style>
.content {
    width: 210mm;
}
.box-body {
    page-break-inside: auto;
    clear:both;
}
</style>
@append

@section('title')
    @lang('admin/reports.page_title_tp_reports')
@stop

@section('content')
    <!-- Main content -->
    <!-- <section class="content">
        <div class=""> -->
            <div>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            <span class="to-print-header">{{ $tp_details->item->channel_type->name }} > {{ $tp_details->item->order->channel->name }} > {{ $tp_details->item->id }}</span>
                        </h3>
                        <div><span style="text-align:right">{{ strtoupper($tp_details->item->status) }}</span></div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="to-print " style='page-break-after: always !important;'>
                            <div style="float: left; width: 49%;">
                                <div>
                                    <h4><u>Order Details</u></h4>
                                    <p><b>Arc Order ID </b><span> : {{ $tp_details->item->order->id }}</span></p>
                                    <p><b>Third Party Order ID </b><span> : {{ $tp_details->item->tp_order_code }}</span></p>
                                    <p><b>Third Party Order Date </b><span> : {{ $tp_details->item->order->tp_order_date }}</span></p>
                                    <p><b>Arc Created Date </b><span> : {{ $tp_details->item->order->created_at }}</span></p>
                                    <p><b>Third Party Order Status </b><span> : {{ ucwords($tp_details->item->item_status) }}</span></p>
                                    <p><b>Arc Order Status </b><span> : {{ $tp_details->item->order->status }}</span></p>
                                    <p><b>Arc Shipped Date </b><span> : {{ $tp_details->item->order->shipped_date }}</span></p>
                                </div>
                                <div>
                                    <h4><u>Product Details</u></h4>
                                    <p><b>Third Party Item Reference ID </b><span> : {{ $tp_details->item->tp_item_id }}</span></p>
                                    @if(!empty($tp_details->item->order_item->id))
                                    <p><b>Arc Item ID </b><span> : {{ $tp_details->item->order_item->id }}</span></p>
                                    <p><b>Third Party SKU </b><span> : {{ $tp_details->item->hubwire_sku }}</span></p>
                                    <p><b>HubwireSKU </b><span> : {{ $tp_details->item->order_item->ref->sku->hubwire_sku }}</span></p>
                                    <p><b>Product Name </b><span> : {{ $tp_details->item->order_item->ref->product->name }}</span></p>
                                    <p><b>Currency </b><span> : {{ $tp_details->item->order->currency }}</span></p>
                                    <p><b>Retail Price <span class="small">(exclusive gst)</span> </b><span> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->unit_price / 1.06) : $tp_details->item->order_item->unit_price), 2, '.',',') }}</span></p>
                                    <p><b>Retail Price <span class="small">(inclusive gst)</span> </b><span> : {{ number_format($tp_details->item->order_item->unit_price, 2, '.',',') }}  (Arc) | {{ $tp_details->item->unit_price }} (Marketplace)</span></p>
                                    <p><b>Listing Price <span class="small">(exclusive gst)</span> </b><span> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->sale_price / 1.06) : $tp_details->item->order_item->sale_price), 2, '.',',') }}</span></p>
                                    <p><b>Listing Price <span class="small">(inclusive gst)</span> </b><span> : {{ number_format($tp_details->item->order_item->sale_price, 2, '.',',') }} (Arc) | {{ $tp_details->item->sale_price }} (Marketplace)</span></p>
                                    @if($tp_details->item->order_item->tax_inclusive  == 1)
                                    <p><b>Sold Price <span class="small">(exclusive gst)</span> </b><span> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->sale_price / 1.06) : $tp_details->item->order_item->sale_price), 2, '.',',') }}</span></p>
                                    <p><b>Sold Price <span class="small">(inclusive gst)</span> </b><span> : {{ number_format($tp_details->item->order_item->sold_price, 2, '.',',') }} (Arc) | {{ $tp_details->item->sold_price }} (Marketplace)</span></p>
                                    @else
                                    <p><b>Sold Price <span class="small">(exclusive gst)</span> </b><span> : {{ number_format($tp_details->item->order_item->sold_price, 2, '.',',') }}</span></p>
                                    <p><b>Sold Price <span class="small">(inclusive gst)</span> </b><span> : {{ number_format($tp_details->item->order_item->sold_price + $tp_details->item->order_item->tax, 2, '.',',') }} (Arc) | {{ $tp_details->item->sold_price }} (Marketplace)</span></p>
                                    @endif
                                    <p><b>Tax Rate </b><span> : {{ $tp_details->item->order_item->tax_rate*100 }} %</span></p>
                                    <p><b>Tax Amount </b><span> : {{ $tp_details->item->order_item->tax }}</span></p>
                                    <!--div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Shipping Fee </b><span> : item shipping fee</div></div-->
                                    @else
                                    <p><b>Arc Item ID </b><span> : N/A</span></p>
                                    <p><b>Third Party SKU </b><span> : N/A</span></p>
                                    <p><b>HubwireSKU </b><span> : N/A</span></p>
                                    <p><b>Product Name </b><span> : N/A</span></p>
                                    <p><b>Currency </b><span> : N/A</span></p>
                                    <p><b>Retail Price <span class="small">(exclusive gst)</span> </b><span> : N/A</span></p>
                                    <p><b>Retail Price <span class="small">(inclusive gst)</span> </b><span> : N/A (Arc) | {{ $tp_details->item->unit_price }} (TP Upload)</span></p>
                                    <p><b>Listing Price <span class="small">(exclusive gst)</span> </b><span> : N/A</span></p>
                                    <p><b>Listing Price <span class="small">(inclusive gst)</span> </b><span> : N/A (Arc) | {{ $tp_details->item->unit_price }} (TP Upload)</span></p>
                                    <p><b>Sold Price <span class="small">(exclusive gst)</span> </b><span> : N/A</span></p>
                                    <p><b>Sold Price <span class="small">(inclusive gst)</span> </b><span> : N/A (ARc) | {{ $tp_details->item->unit_price }} (TP Upload)</span></p>
                                    <p><b>Tax Rate </b><span> : N/A</span></p>
                                    <p><b>Tax Amount </b><span> : N/A</span></p>
                                    <!--div class="form-group"><div class="bold col-xs-12 col-md-4 no-padding">Shipping Fee </b><span> : item shipping fee</div></div-->
                                    @endif
                                </div>
                                <div>
                                    <h4><u>Hubwire Commission and Fees</u></h4>
                                    @if(!empty($tp_details->item->order_item->id))
                                    <p><b>Hubwire Fee </b><span> : {{ $tp_details->item->order_item->hw_fee }}</span></p>
                                    <p><b>Hubwire Commission </b><span> : {{ $tp_details->item->order_item->hw_commission }}</span></p>
                                    <p><b>Miscellaneous Fee </b><span> : {{ $tp_details->item->order_item->misc_fee }}</span></p>
                                    @else
                                    <p><b>Hubwire Fee </b><span> : N/A</span></p>
                                    <p><b>Hubwire Commission </b><span> : N/A</span></p>
                                    <p><b>Miscellaneous Fee </b><span> : N/A</span></p>
                                    @endif
                                </div>
                                <div>
                                    <p><b>Uploaded By </b><span> : {{ $tp_details->item->created_by->first_name }} {{ $tp_details->item->created_by->last_name }}</span></p>
                                    <p><b>Created On </b><span> : {{ $tp_details->item->created_at }}</span></p>
                                    <p><b>Last Updated By </b><span> : {{ $tp_details->item->last_attended_by->first_name }} {{ $tp_details->item->last_attended_by->last_name }}</span></p>
                                    <p><b>Last Updated On </b><span> : {{ $tp_details->item->updated_at }}</span></p>
                                </div>
                            </div>
                            <div style="float:left; width: 49%;">
                                <div>
                                    <h4><u>Third Party Payment Details</u></h4>
                                    <p><b>Third Party Payout Amount </b><span> : {{ $tp_details->item->net_payout }}</span></p>
                                    <p><b>Third Party Channel Fee </b><span> : {{ $tp_details->item->channel_fees }}</span></p>
                                    <p><b>Third Party Payout Status </b><span> : Paid</span></p>
                                    <p><b>Third Party Payout Date </b><span> : {{ $tp_details->item->payment_date }}</span></p>
                                </div>
                                <div>
                                    <h4><u>Hubwire to Merchant Payment Details</u></h4>
                                    @if(!empty($tp_details->item->order_item))
                                    <p><b>Merchant </b><span> : {{ $tp_details->item->order_item->ref->product->brands->merchant->name }}</span></p>
                                    <p><b>Brand </b><span> : {{ $tp_details->item->order_item->ref->product->brand_name }}</span></p>
                                    @else
                                    <p><b>Merchant </b><span> : N/A</span></p>
                                    <p><b>Brand </b><span> : N/A</span></p>
                                    @endif
                                    <p><b>Merchant Payout Amount </b><span> : {{ $tp_details->item->merchant_payout_amount }}</span></p>
                                    <p><b>Merchant Payout Status </b><span> : {{ $tp_details->item->merchant_payout_status }}</span></p>
                                    <p><b>Hubwire Payout Bank </b><span> : {{ $tp_details->item->hw_payout_bank }}</span></p>
                                    <p><b>Merchant Payout Date </b><span> : {{ $tp_details->item->merchant_payout_date }}</span></p>
                                    <p><b>Merchant Payout Payment Reference </b><span> : {{ $tp_details->item->merchant_payout_ref }}</span></p>
                                    <p><b>Merchant's Bank </b><span> : {{ $tp_details->item->merchant_bank }}</span></p>
                                    <p><b>Payment Method </b><span> : {{ $tp_details->item->merchant_payout_method }}</span></p>
                                    <p><b>Invoice No </b><span> : {{ $tp_details->item->merchant_invoice_no }}</span></p>
                                </div>
                            </div>
                        </div>
                        <div style='clear:both;overflow:hidden;page-break-after: always !important;'></div>
                        <div class="to-print">
                            <div>
                                <h4><u>Remarks</u></h4>
                                <table>
                                    @foreach($tp_details->remarks as $remark)
                                        <tr align="left" valign"top">
                                            <tbody>
                                            <td width="15%">{{ $remark->created_at }}</td>
                                            @if($remark->type == 'general')
                                                <td width="70%">{{ '[General] ' . $remark->remarks }}</td>
                                            @elseif($remark->type == 'error')
                                                @if($remark->resolve_status == 0)
                                                <td width="70%">{{ '[Error] ' . $remark->remarks }}</td>
                                                @elseif($remark->resolve_status == 1)
                                                <td width="70%">{{ '[Resolved] ' . $remark->remarks }}</td>
                                                @endif
                                            @endif
                                            <td width="15%">{{ $remark->user }}</td>
                                            </tbody>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <div style='clear:both;overflow:hidden;page-break-after: always !important;'></div>
                        <div>
                            <div>
                                <h4><u>Activity Log</u></h4>
                                <table>
                                    <tbody>
                                    @foreach($tp_details->logs as $log)
                                        <tr align="left" valign="top">
                                            <td width="15%">{{ $log->created_at }}</td>
                                            <td width="70%">Field {{ $log->field }} was updated from {{ $log->old_value }} to {{ $log->new_value }}</td>
                                            <td width="15%">{{ $log->user }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- </div>
    </section> -->
@stop


@section('footer_scripts')
<style>
p:last-child{
    margin-bottom: 0;
}
td{
    padding: 0px 5px;
}
.row{
    margin-left: 0;
    margin-right: 0;
}
.print-container{
    width: 100%;
}
</style>
<script>
    window.print();
</script>
@append