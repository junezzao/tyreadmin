@extends('layouts.master')

@section('header_scripts')
<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
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
                            <span class="to-print-header">{{ $tp_details->item->channel_type->name }} > {{ isset($tp_details->item->order->channel->name)?$tp_details->item->order->channel->name:'' }} > {{ $tp_details->item->id }}</span>
                        </h3>
                        <div class="pull-right">
                            <span class="bold h4">{{ strtoupper($tp_details->item->status) }}</span>&nbsp;&nbsp;
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        {!! Form::open(['url' => route('admin.tp_reports.update', ['id' => $tp_details->item->id]), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'update-form']) !!}
                        {!! Form::hidden('id', $tp_details->item->id) !!}
                        <div class="to-print col-md-12">
                            <div class="col-md-6 col-xs-12">
                                <div>
                                    <h4><u>Order Details</u></h4>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Order ID </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->id)?$tp_details->item->order->id:'' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Order ID </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->tp_order_code }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Order Date </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->tp_order_date)?$tp_details->item->order->tp_order_date:'' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Created Date </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->created_at)?$tp_details->item->order->created_at:'' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Order Status </div><div class="col-xs-12 col-md-8">{!! Form::select('tp_item_status', $tpOrderStatus, ucwords($tp_details->item->item_status), ['class' => 'form-control']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Order Status </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->status)?$tp_details->item->order->status : '' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Shipped Date </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->shipped_date)?$tp_details->item->order->shipped_date:'' }}</div></div>
                                </div>
                                <div>
                                    <h4><u>Product Details</u></h4>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Item Reference ID </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->tp_item_id }}</div></div>
                                    @if(!empty($tp_details->item->order_item->id))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Item ID </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->id }} {!! Form::hidden('order_item_id', $tp_details->item->order_item->id) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Item Status </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->status }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party SKU </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->hubwire_sku }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">HubwireSKU </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->sku->hubwire_sku }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Product Name </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->product->name }}</div></div>
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Arc Item ID </div><div class="col-xs-12 col-md-8"> {!! Form::text('order_item_id', null, ['class' => 'form-control']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party SKU </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">HubwireSKU </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Product Name </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    @endif
                                    @if(!empty($tp_details->item->order->id)&&!empty($tp_details->item->order_item->id))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Currency </div><div class="col-xs-12 col-md-8"> : {{ isset($tp_details->item->order->currency)?$tp_details->item->order->currency:'' }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Retail Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->unit_price / 1.06) : $tp_details->item->order_item->unit_price), 2, '.',',') }} (Arc) | {{ $tp_details->item->unit_price }} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Retail Price <span class="small">(inclusive gst)</span> </div><div class="col-md-4 col-xs-12">{!! Form::text('arc_unit_price', number_format($tp_details->item->order_item->unit_price, 2, '.',','), ['class' => 'form-control']) !!} (Arc)</div><div class="col-md-4 col-xs-12"> {!! Form::text('tp_unit_price', $tp_details->item->unit_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Listing Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->sale_price / 1.06) : $tp_details->item->order_item->sale_price), 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Listing Price <span class="small">(inclusive gst)</span> </div><div class="col-md-4 col-xs-12">{!! Form::text('arc_sale_price', number_format($tp_details->item->order_item->sale_price, 2, '.',','), ['class' => 'form-control']) !!} (Arc)</div><div class="col-md-4 col-xs-12"> {!! Form::text('tp_sale_price', $tp_details->item->sale_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    @if($tp_details->item->order_item->tax_inclusive  == 1)
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Sold Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format( ($tp_details->item->order->channel->issuing_company->gst_reg == 1 ? ($tp_details->item->order_item->sold_price / 1.06) : $tp_details->item->order_item->sold_price), 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Sold Price <span class="small">(inclusive gst)</span> </div><div class="col-md-4 col-xs-12"> {!! Form::text('arc_sold_price', number_format($tp_details->item->order_item->sold_price, 2, '.',','), ['class' => 'form-control']) !!} (Arc) </div><div class="col-md-4 col-xs-12"> {!! Form::text('tp_sold_price', $tp_details->item->sold_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Sold Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : {{ number_format($tp_details->item->order_item->sold_price, 2, '.',',') }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Sold Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> {!! Form::text('arc_sold_price', number_format($tp_details->item->order_item->sold_price + $tp_details->item->order_item->tax, 2, '.',','), ['class' => 'form-control']) !!} (Arc) </div><div class="col-md-4 col-xs-12">{!! Form::text('tp_sold_price', $tp_details->item->sold_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    @endif
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Tax Rate </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->tax_rate*100 }} %</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Tax Amount </div><div class="col-xs-12 col-md-8"> {!! Form::text('tax', $tp_details->item->order_item->tax, ['class' => 'form-control']) !!}</div></div>
                                    <!--div class="form-group"><div class="bold col-xs-12 col-md-4">Shipping Fee </div><div class="col-xs-12 col-md-8"> : item shipping fee</div></div-->
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Currency </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Retail Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Retail Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A (Arc) | {!! Form::text('tp_unit_price', $tp_details->item->unit_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Listing Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Listing Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A (Arc) | {!! Form::text('tp_sale_price', $tp_details->item->sale_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Sold Price <span class="small">(exclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Sold Price <span class="small">(inclusive gst)</span> </div><div class="col-xs-12 col-md-8"> : N/A (Arc) | {!! Form::text('tp_sold_price', $tp_details->item->sold_price, ['class' => 'form-control']) !!} (Marketplace)</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Tax Rate </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Tax Amount </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <!--div class="form-group"><div class="bold col-xs-12 col-md-4">Shipping Fee </div><div class="col-xs-12 col-md-8"> : item shipping fee</div></div-->
                                    @endif
                                </div>
                                <div>
                                    <h4><u>Hubwire Commission and Fees</u></h4>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Hubwire Fee </div><div class="col-xs-12 col-md-8">{!! Form::text('hw_fee', (!empty($tp_details->item->order_item->hw_fee) ? $tp_details->item->order_item->hw_fee : '0.00'), ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('hw_fee') }}</div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Hubwire Commission </div><div class="col-xs-12 col-md-8">{!! Form::text('hw_commission', (!empty($tp_details->item->order_item->hw_commission) ? $tp_details->item->order_item->hw_commission : '0.00'), ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('hw_commission') }}</div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Miscellaneous Fee </div><div class="col-xs-12 col-md-8">{!! Form::text('misc_fee', (!empty($tp_details->item->order_item->misc_fee) ? $tp_details->item->order_item->misc_fee : '0.00'), ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('misc_fee') }}</div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Minimum Guarantee </div><div class="col-xs-12 col-md-8">{!! Form::text('min_guarantee', (!empty($tp_details->item->order_item->min_guarantee) ? $tp_details->item->order_item->min_guarantee : '0.00'), ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('min_guarantee') }}</div></div></div>
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
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Currency </div><div class="col-md-8 col-xs-12">{!! Form::select('net_payout_currency', $currencyList, $tp_details->item->net_payout_currency, ['class' => 'form-control']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Payout Amount </div><div class="col-md-8 col-xs-12"> {!! Form::text( 'net_payout', $tp_details->item->net_payout, ['class' => 'form-control'] ) !!} <div class="error">{{ $errors->first('net_payout') }}</div></div></div>

                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Channel Fee </div><div class="col-xs-12 col-md-8"> {!! Form::text('channel_fee', $tp_details->item->channel_fees, ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('channel_fee') }}</div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Shipping Fee </div><div class="col-xs-12 col-md-8"> {!! Form::text('channel_shipping_fees', $tp_details->item->channel_shipping_fees, ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('channel_shipping_fees') }}</div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Payment Gateway Fee </div><div class="col-xs-12 col-md-8"> {!! Form::text('channel_payment_gateway_fees', $tp_details->item->channel_payment_gateway_fees, ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('channel_payment_gateway_fees') }}</div></div></div>

                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Payout Status </div><div class="col-xs-12 col-md-8"> 
                                    @if($paid_status=="Paid")
                                     : Paid<input type="hidden" name="paid_status" value="1"></div></div>
                                    @else
                                    {!! Form::select('paid_status', $tpPaymentStatusList, $paid_status, ['class' => 'form-control']) !!}</div></div>
                                    @endif

                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Payout Date </div>
                                    <div class="col-xs-12 col-md-8"><div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>{!! Form::text('payment_date', $tp_details->item->payment_date, ['id' => 'payment_date','class' => 'form-control']) !!}
                                    </div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Third Party Payout Reference </div><div class="col-xs-12 col-md-8"> {!! Form::text('tp_payout_ref', $tp_details->item->tp_payout_ref, ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('tp_payout_ref') }}</div></div></div>
                                </div>
                                <div>
                                    <h4><u>Hubwire to Merchant Payment Details</u></h4>
                                    @if(!empty($tp_details->item->order_item))
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->product->brands->merchant->name }}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Brand </div><div class="col-xs-12 col-md-8"> : {{ $tp_details->item->order_item->ref->product->brand_name }}</div></div>
                                    @else
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Brand </div><div class="col-xs-12 col-md-8"> : N/A</div></div>
                                    @endif
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant Payout Amount </div><div class="col-md-4 col-xs-12">{!! Form::select('merchant_payout_currency', $currencyList, $tp_details->item->merchant_payout_currency, ['class' => 'form-control']) !!}</div><div class="col-md-4 col-xs-12"> {!! Form::text('merchant_payout_amount', $tp_details->item->merchant_payout_amount, ['class' => 'form-control']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant Payout Status </div><div class="col-xs-12 col-md-8"> {!! Form::select('merchant_payout_status', $paymentStatusList, $tp_details->item->merchant_payout_status, ['class' => 'form-control']) !!} <div class="error">{{ $errors->first('merchant_payout_status') }}</div></div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Hubwire Payout Bank </div><div class="col-xs-12 col-md-8"> {!! Form::select('hw_payout_bank', $bankList, $tp_details->item->hw_payout_bank, ['class' => 'form-control', 'placeholder' => 'Select Bank']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant Payout Date </div>
                                        <div class="col-xs-12 col-md-8"><div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>{!! Form::text('merchant_payout_date', $tp_details->item->merchant_payout_date, ['id' => 'merchant_payout_date', 'class' => 'form-control']) !!}
                                        </div></div>
                                    </div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant Payout Payment Reference </div><div class="col-xs-12 col-md-8">{!! Form::text('merchant_payout_reference', $tp_details->item->merchant_payout_ref, ['id' => 'merchant_payout_ref', 'class' => 'form-control']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Merchant's Bank </div><div class="col-xs-12 col-md-8"> {!! Form::select('merchant_bank', $bankList, $tp_details->item->merchant_bank, ['class' => 'form-control', 'placeholder' => 'Select Bank']) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Payment Method </div><div class="col-xs-12 col-md-8"> {!! Form::select('merchant_payment_method', $paymentMethodList, $tp_details->item->merchant_payout_method, ['class' => 'form-control', 'placeholder' => 'Select Paymnent Method'] ) !!}</div></div>
                                    <div class="form-group"><div class="bold col-xs-12 col-md-4">Invoice No </div><div class="col-xs-12 col-md-8">{!! Form::text('merchant_invoice_no', $tp_details->item->merchant_invoice_no, ['id' => 'merchant_invoice_no', 'class' => 'form-control']) !!}</div></div>
                                </div>
                            </div>
                        </div>
                        <div class="to-print col-xs-12">
                            <div class="col-xs-12">
                                <h4><u>Remarks</u></h4>
                                <div class="form-group">
                                    <div class="bold col-xs-12 col-md-2">New Remark : </div>
                                    <div class="col-xs-12 col-md-8">{!! Form::textarea('remark', null, ['class' => 'form-control']) !!}</div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="table-responsive col-xs-12">
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
                            @if($tp_details->item->status == 'Unverified' or $tp_details->item->status == 'Not Found')
                            <button type="button" id="btn_delete" class="btn btn-danger">
                                @lang('admin/reports.button_delete_tp_report_item')
                            </button>
                            @endif
                            {!! Form::submit('Update', ['class' => 'pull-right btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                        {!! Form::close() !!}
                            {!! Form::open(array('url' => route('admin.tp_reports.destroy', [$tp_details->item->id]), 'method' => 'DELETE', 'id' => 'delete-form')) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop



@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    $('#merchant_payout_date, #payment_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    $("#print").click(function() {
        var css = '<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">';
        var printCss = '<style>' + $("#cssForPrintWindow").text() +' <\/style>';
        var header = '<h3>'+$('.to-print-header').html()+'</h3>';
        var content = $('.to-print').html();
        var printWindow = window.open();
        printWindow.document.write('<title>Print Title</title>');
        printWindow.document.write(css + printCss + header + content);
        printWindow.document.close();
    });

    $('input[type=submit]').click(function() {
        $(this).prop('disabled', true);
        $('#update-form').submit();
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