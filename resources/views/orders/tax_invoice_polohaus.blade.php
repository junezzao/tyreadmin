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
        .ita { font-style: italic; }
        .page-break{page-break-after: always;}
    </style>
    <script type="text/javascript">
        function printpage() {
            window.print();
        }
    </script>
</head>
<!--body onload="printpage();"-->
<body>
    <div class="page-break">
        <table border="0" cellspacing="1" cellpadding="0" width="100%">
            <tr>
                <td valign="top" colspan="5"><h1>TAX INVOICE {{ $order->tax_invoice_no }}</h1></td>
                <td valign="top" align="right" colspan="4" rowspan="2">
                    <p><img src="{{ asset('images/logos/polohaus-logo.png',env('HTTPS',false)) }}" class="img-responsive center-block" /></p>
                    <p class="small"><strong>BRG Polo Haus Sdn Bhd</strong><br/>Lot 1897B, Jalan KPB 9,<br/>Kawasan Perindustrian Kg. Baru Balakong,<br/>Seri Kembangan,<br/>43300 Selangor,<br/>Malaysia.
                    <br/>Tel: 603-896 44 997
                    <br/>Fax No: 603-896 44 991
                    <br/>Email: os@polohaus.com
                    <br/>GST Number: 001434722304</p>
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="5">
                    <p><u>Order Details</u><br/>
                        <p><strong>Tax Invoice:</strong> {{ $order->tax_invoice_no }}</p>
                        <p><strong>Hubwire Order:</strong> #{{ $order->sale_id }}</p>
                        <p><strong>Third Party Order:</strong> {{ (!empty($order->order_code) ? $order->order_code : 'N/A') }} <span class="channel_type">{{ $channel_type }}</span></p>
                        <p><strong>Date:</strong> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format(Config::get('globals.carbonFormat.date_format_invoice')) }}</p>
                    </p>
                </td>
            </tr>
            <tr class="breaker">
                <td colspan="9"></td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <p>
                        <u>Shipping Details</u><br/>
                        <strong>Name:</strong> {{ ucwords($order->sale_recipient) }}
                        <p><strong>Contact Number:</strong> {{ $order->sale_phone }}</p>
                        <p><strong>Address:</strong> {{ $order->sale_address }}</p>
                    </p>
                </td>
                <td valign="top" colspan="3">
                    <p>
                        <u>Billing Details</u><br/>
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
                <td valign="middle" class="title">Retail Price<br/><span class="small ita">(Inclusive GST)</span></td>
                <td valign="middle" class="title">Line Total<br/><span class="small ita">(Inclusive GST)</span></td>
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
                    <td valign="middle" align="center"></td>
                </tr>
            @endfor
            <tr>
                <td colspan="9"><hr/></td>
            </tr>
            <tr class="breaker">
                <td colspan="4"></td>
                <td valign="middle" colspan="3" align="right">Shipping Fee (Inclusive GST) :</td>
                <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_shipping, 2) }}</td>
            </tr>
            <tr class="breaker">
                <td colspan="4"></td>
                <td valign="middle" colspan="3" align="right">Total Excluding GST :</td>
                <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_total / 1.06, 2) }}</td>
            </tr>
            <tr class="spanbreaker">
                <td colspan="4"></td>
                <td valign="middle" colspan="3" align="right">GST (6%) :</td>
                <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format(($order->sale_total / 1.06) * 0.06, 2) }}</td>
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
                <td valign="middle" colspan="2" align="left">: {{ $currency }} {{ number_format(($order->sale_total / 1.06) * 0.06, 2) }}</td>
            </tr>
        </table>
        <div id="returns-container" style="width:200px;float:left;">
            <table>
                <tr>
                    <td>Return Reason</td>
                    <td>
                        <ol>
                            <li>Defect</li>
                            <li>Differenct from website</li>
                            <li>Size is too large</li>
                            <li>Size is too small</li>
                            <li>Disliked</li>
                            <li>Wrong Item</li>
                            <li>Not convinced by the quality</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>
        <div id="returns-container" style="width:150px;float:right;">
            <table>
                <tr>
                    <td rowspan="2">Return Reason</td>
                    <td colspan="2">Exchange</td>
                </tr>
                <tr>
                    <td>Qty</td><td>Size/Colour</td>
                </tr>
                <tr>
                    <td><input type="text" size="10"/></td>
                    <td><input type="text" size="5"/></td>
                    <td><input type="text" size="20"/></td>
                </tr>
            </table>
        </div>
    </div>
    <div>
        <table border="0" cellspacing="1" cellpadding="0" width="100%">
            <tr>
                <td valign="top" colspan="5"><h1>TAX INVOICE {{ $order->tax_invoice_no }}</h1></td>
                <td valign="top" align="right" colspan="4" rowspan="2">
                    <p><img src="{{ asset('images/logos/polohaus-logo.png',env('HTTPS',false)) }}" class="img-responsive center-block" /></p>
                    <p class="small"><strong>BRG Polo Haus Sdn Bhd</strong><br/>Lot 1897B, Jalan KPB 9,<br/>Kawasan Perindustrian Kg. Baru Balakong,<br/>Seri Kembangan,<br/>43300 Selangor,<br/>Malaysia.
                    <br/>Tel: 603-896 44 997
                    <br/>Fax No: 603-896 44 991
                    <br/>Email: os@polohaus.com
                    <br/>GST Number: 001434722304</p>
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="5">
                    <p><u>Order Details</u><br/>
                        <p><strong>Tax Invoice:</strong> {{ $order->tax_invoice_no }}</p>
                        <p><strong>Hubwire Order:</strong> #{{ $order->sale_id }}</p>
                        <p><strong>Third Party Order:</strong> {{ (!empty($order->order_code) ? $order->order_code : 'N/A') }} <span class="channel_type">{{ $channel_type }}</span></p>
                        <p><strong>Date:</strong> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format(Config::get('globals.carbonFormat.date_format_invoice')) }}</p>
                    </p>
                </td>
            </tr>
            <tr class="breaker">
                <td colspan="9"></td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <p>
                        <u>Shipping Details</u><br/>
                        <strong>Name:</strong> {{ ucwords($order->sale_recipient) }}
                        <p><strong>Contact Number:</strong> {{ $order->sale_phone }}</p>
                        <p><strong>Address:</strong> {{ $order->sale_address }}</p>
                    </p>
                </td>
                <td valign="top" colspan="3">
                    <p>
                        <u>Billing Details</u><br/>
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
                <td valign="middle" class="title">Retail Price<br/><span class="small ita">(Inclusive GST)</span></td>
                <td valign="middle" class="title">Line Total<br/><span class="small ita">(Inclusive GST)</span></td>
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
                    <td valign="middle" align="center"></td>
                </tr>
            @endfor
            <tr>
                <td colspan="9"><hr/></td>
            </tr>
            <tr class="breaker">
                <td colspan="4"></td>
                <td valign="middle" colspan="3" align="right">Shipping Fee (Inclusive GST) :</td>
                <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_shipping, 2) }}</td>
            </tr>
            <tr class="breaker">
                <td colspan="4"></td>
                <td valign="middle" colspan="3" align="right">Total Excluding GST :</td>
                <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format($order->sale_total / 1.06, 2) }}</td>
            </tr>
            <tr class="spanbreaker">
                <td colspan="4"></td>
                <td valign="middle" colspan="3" align="right">GST (6%) :</td>
                <td valign="middle" colspan="2" align="left"> {{ $currency }} {{ number_format(($order->sale_total / 1.06) * 0.06, 2) }}</td>
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
                <td valign="middle" colspan="2" align="left">: {{ $currency }} {{ number_format(($order->sale_total / 1.06) * 0.06, 2) }}</td>
            </tr>
        </table>
        <div id="returns-container" style="width:200px;float:left;">
            <table>
                <tr>
                    <td>Return Reason</td>
                    <td>
                        <ol>
                            <li>Defect</li>
                            <li>Differenct from website</li>
                            <li>Size is too large</li>
                            <li>Size is too small</li>
                            <li>Disliked</li>
                            <li>Wrong Item</li>
                            <li>Not convinced by the quality</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>
        <div id="returns-container" style="width:150px;float:right;">
            <table>
                <tr>
                    <td rowspan="2">Return Reason</td>
                    <td colspan="2">Exchange</td>
                </tr>
                <tr>
                    <td>Qty</td><td>Size/Colour</td>
                </tr>
                <tr>
                    <td><input type="text" size="10"/></td>
                    <td><input type="text" size="5"/></td>
                    <td><input type="text" size="20"/></td>
                </tr>
            </table>
        </div>
    </div>
    <script type="text/javascript">print();</script>
</body>
</html>