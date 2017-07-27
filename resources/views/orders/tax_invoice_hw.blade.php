<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        body { font-family: "Open Sans", Arial, sans-serif; }
        table { font-size:12px; }
            th { color: white; -webkit-print-color-adjust: exact; }
        tr.breaker td { padding:15px 7px; }
        tr td.title { padding: 5px 7px; }
        tr.spanbreaker td { padding: 5px 7px; }
        .title { align:center; font-size:13px; font-weight:bold; }
        .odd { background-color: #ececec; }
        .even { background-color: #f8f8f8; }
        hr { margin: 0 auto; }
        .channel_type { border: 1px solid #999; border-radius: 3px; background-color: #f8f8f8; padding: 3px; font-size: 11px; }
        .small { font-size: 9px; }
    </style>
    <script type="text/javascript">
        function printpage() {
            window.print();
        }
    </script>
</head>
<!--body onload="printpage();"-->
<body>
    <div>
    <table border="0" cellspacing="1" cellpadding="0" width="100%">
        <tr>
            <td valign="top" colspan="5"><h1>TAX INVOICE HC{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format('ym') }}-{{ $order->sale_id }}</h1></td>
            <td valign="top" align="right" colspan="4" rowspan="2">
                @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d') >= '2016-05-01')
                    <?php $logo = 'fmhw-logo.png';
                          $name = 'FM Hubwire Sdn Bhd'; ?>
                @else 
                    <?php $logo = 'hubwire-logo.png';
                          $name = 'Hubwire Sdn Bhd'; ?>
                @endif
                <p>{!! Html::image("images/".$logo, "Logo", array('class'=>'img-responsive center-block')),env('HTTPS',false) !!}</p>
                <p class="small"><strong>{{ $name }}</strong><br/>Unit 17-7, Level 7, Block C1,<br/>Dataran Prima, Jalan PJU 1/41,<br/>47301 Petaling Jaya, Malaysia</p>
                <p class="small">GST Number: 
                    @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d') >= '2016-05-01')
                        {{ config('globals.gst_no_fmhw') }}
                    @else 
                       {{ config('globals.gst_no_hw') }}
                    @endif
                </p>
            </td>
        </tr>
        <tr>
            <td valign="top" colspan="5">
                <p><u>Order Details</u><br/>
                    <strong>Tax Invoice:</strong> HC{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format('ym') }}-{{ $order->sale_id }}
                    <p><strong>Third Party Order:</strong> {{ (!empty($order->order_code) ? $order->order_code : 'N/A') }} <span class="channel_type">{{ $channel_type }}</span></p>
                    <p><strong>Date:</strong> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format(config('globals.date_format_invoice')) }}</p>
                </p>
            </td>
        </tr>
        <tr class="breaker">
            <td colspan="9"></td>
        </tr>
        <tr>
            <td valign="top" colspan="3">
                <p>
                    <u>Shipping Address</u><br/>
                    <strong>Name:</strong> {{ ucwords($order->sale_recipient) }}
                    <p><strong>Contact Number:</strong> {{ $order->sale_phone }}</p>
                    <p><strong>Address:</strong> {{ $order->sale_address }}</p>
                </p>
            </td>
            <td valign="top" colspan="3">
                <p>
                    <u>Billing Address</u><br/>
                    <strong>Name:</strong> {{ (!empty($member->member_name) ? ucwords($member->member_name) : '') }}
                    <p><strong>Email:</strong> {{ (!empty($member->member_email) ? $member->member_email : '') }}</p>
                    <p><strong>Contact Number:</strong> {{ (!empty($member->member_mobile) ? $member->member_mobile : '') }}</p>
                </p>
            </td>
            <td valign="top" colspan="3">
                <p>
                    <u>Payment Details</u><br/>
                    <strong>Payment Type:</strong> {{ $order->payment_type }}
                    <p><strong>Promotion Code:</strong> {{ $order->promotions }}</p>
                    <p><strong>Amount Paid:</strong> {{ $currency }} {{ number_format($order->sale_total, 2) }}</p>
                </p>
            </td>
        </tr>
        <tr>
            <td valign="middle" class="title">No.</td>
            <td valign="middle" colspan="2" class="title">SKU</td>
            <td valign="middle" colspan="2" class="title">Product Name</td>
            <td valign="middle" class="title">Quantity</td>
            <td valign="middle" class="title">Retail Price</td>
            <td valign="middle" class="title">Line Total</td>
            <td valign="middle" class="title">Credits</td>
        </tr>
        <tr>
            <td colspan="9"><hr/></td>
        </tr>
        @for ($i = 0; $i <= count($order_items)-1; $i++)
            <tr class="{!! ($i%2 == 0 ? 'even' : 'odd') !!} breaker">
                <td valign="middle">{{ $i+1 }}</td>
                <td valign="middle" colspan="2">{{ $order_items[$i]->hubwire_sku }}</td>
                <td valign="middle" colspan="2">{{ $order_items[$i]->product_name }}</td>
                <td valign="middle" align="center">{{ $order_items[$i]->item_original_quantity }} 
                <td valign="middle" align="center">{{ $currency }} {{ number_format($order_items[$i]->item_price, 2) }}</td>
                <td valign="middle" align="center">{{ $currency }} {{ number_format($order_items[$i]->item_price * $order_items[$i]->item_original_quantity, 2) }}</td>
                <td valign="middle" align="center">{{ (!empty($order_items[$i]->credits) ? number_format($order_items[$i]->credits, 2) : '') }}</td>
            </tr>
        @endfor
        <tr>
            <td colspan="9"><hr/></td>
        </tr>
        <tr class="breaker">
            <td colspan="4"></td>
            <td valign="middle" colspan="3" align="right">Shipping Fee :</td>
            <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_shipping * 0.94, 2) }}</td>
        </tr>
        <tr class="breaker">
            <td colspan="4"></td>
            <td valign="middle" colspan="3" align="right">Total Excluding GST :</td>
            <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_total * 0.94, 2) }}</td>
        </tr>
        <tr class="spanbreaker">
            <td colspan="4"></td>
            <td valign="middle" colspan="3" align="right">GST (6%) :</td>
            <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_total * 0.06, 2) }}</td>
        </tr>
        <tr class="breaker">
            <td colspan="4"></td>
            <td valign="middle" colspan="3" align="right">Total Including GST :</td>
            <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="9"><hr/></td>
        </tr>
        <tr class="breaker">
            <td colspan="4"></td>
            <td valign="middle" colspan="3" align="right">Total GST charged at <br/> Standard Rate (6%) </td>
            <td valign="middle" colspan="2" align="left">: {{ $currency }} {{ number_format($order->sale_total * 0.06, 2) }}</td>
        </tr>
    </table>
    </div>
</body>
</html>