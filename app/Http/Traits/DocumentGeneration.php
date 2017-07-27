<?php namespace App\Http\Traits;

use DB;
use App\Models\Sales;
use App\Models\Channels;
use Carbon\Carbon;

trait DocumentGeneration
{
    public function generateTaxInvoice($order_id)
    {
        //$order = Sales::where('sale_id', '=', $order_id)->first();
        $order = DB::connection('mysql2')->table('sales')->where('sale_id', '=', $order_id)->first();
        $sales_items = DB::connection('mysql2')->table('channel_sku')
                        ->join('sku', 'channel_sku.sku_id', '=', 'sku.sku_id')
                        ->join('products', 'products.product_id', '=', 'channel_sku.product_id')
                        ->join('sales_items', 'channel_sku_id', '=', 'sales_items.product_id', 'right')
                        ->where('sale_id', '=', $order_id)
                        ->get();

        //$channel = Channels::where('channel_id', '=', $order->channel_id)->first();
        $channel = DB::connection('mysql2')->table('channels')->where('channel_id', '=', $order->channel_id)->first();
        
        $channel_type = '';
        $channel_types = config('globals.channel_type');
        for ($i = 0; $i <= count($channel_types)-1; $i++) {
            if ($channel->channel_type == $i) {
                $channel_type = $channel_types[$i];
            }
        }

        $member = DB::connection('mysql2')->table('members')->where('member_id', '=', $order->member_id)->first();
        $currency = (!empty($order->currency) ? $order->currency : $channel->channel_currency);
        $credits = DB::connection('mysql2')->table('store_credits_log')->where('sale_id', '=', $order_id)->get();
        if ($credits) {
            for ($i = 0; $i<= count($credits)-1; $i++) {
                for ($j = 0; $j<= count($sales_items)-1; $j++) {
                    if ($credits[$i]->sale_item_id == $sales_items[$j]->item_id) {
                        $sales_items[$j]->credits = (!empty($credits[$i]->amount) ? $credits[$i]->amount : '');
                    }
                }
            }
        }

        $promotions = array();
        $order_items = array();
        foreach ($sales_items as $item) {
            if ($item->product_type == 'PromotionCode') {
                $promo = DB::connection('mysql2')->table('promotion_code')->select('promo_code')->where('code_id', '=', $item->product_id)->first();
                $promotions[] = $promo->promo_code;
            }
            if ($item->product_type == 'ChannelSKU') {
                $order_items[] = $item;
                if ($item->item_original_quantity != $item->item_quantity && $item->item_original_quantity != 0) {
                    $returns = true;
                }
            }
        }

        $order->promotions = (!empty($promotions) ? implode(', ', $promotions) : 'None');
        $client = DB::connection('mysql2')->table('clients')->select('client_name')->where('client_id', '=', $order->client_id)->first();

        /**
        *
        * Temporarily store to local storage folder. 
        * Future to upload to S3 and direct user to view the tax invoice for printing with print dialog.
        *
        */

        $type = ($channel->channel_name == 'Fabspy' ? 'fabspy' : 'hw');
        //return view('orders.tax_invoice', compact('order', 'sales_items', 'channel_type', 'member', 'currency'));
        $today = Carbon::now()->setTimezone('UTC')->format('Ymd');
        //$fileDate = 111;
        $fileDate = Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at, 'UTC')->setTimezone('Asia/Kuala_Lumpur')->format('Ymd');

        $pdf = \PDF::loadView('orders.tax_invoice_polohaus', compact('order', 'order_items', 'channel_type', 'member', 'currency'))->save(storage_path('tax-invoice/tax-invoice-'.str_replace(' ', '-', strtolower($client->client_name)).'-'.str_replace(' ', '-', strtolower($channel->channel_name)).'-'.$order_id
            .'-'.$fileDate.'.pdf'));
    }

    public function generateSalesOrder($order_id)
    {
    }

    public function generateShippingLabel($order_id, $channel, $courier = null)
    {
    }
}
