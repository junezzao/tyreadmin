<?php
namespace Step\Functional;

use Step\Functional\UserSteps;
use DB;

class ReturnSteps extends UserSteps
{
    protected $data_ids;

    public function createTestData()
    {
        $I = $this;

        //create merchant
        $merchant = $I->haveRecord('merchants', array(
                'name'              => 'Test Merchant',
                'slug'              => 'testmerchant',
                'address'           => 'Test address',
                'contact'           => '',
                'email'             => '',
                'logo_url'          => '',
                'gst_reg_no'        => 12345,
                'self_invoicing'    => 0,
                'timezone'          => 'Asia/Kuala_Lumpur',
                'currency'          => 'MYR',
                'forex_rate'        => 1,
                'ae'                => 150,
                'status'            => 'Active'
            ));

        //create brand
        $brand = $I->haveRecord('brands', array(
                'brand_name'    => 'Test Brand',
                'product_brand' => 'TESTB',
                'client_id'     => $merchant
            ));

        //create product
        $product = $I->haveRecord('products', array(
                'product_name'      => 'Test Product',
                'product_desc'      => 'Test product',
                'client_id'         => $merchant,
                'default_media'     => 0,
                'product_brand'     => 'TEST',
                'product_desc_2'    => '',
                'third_party'       => '',
                'brand_id'          => $brand
            ));
        
        //create sku
        $sku = $I->haveRecord('sku', array(
                'sku_barcode'       => '',
                'product_id'        => $product,
                'client_id'         => $merchant,
                'sku_supplier_code' => '',
                'sku_weight'        => '',
                'hubwire_sku'       => 'TESTSKU000000'
            ));

        //create channel_type
        $channel_type = $I->haveRecord('channel_types', array(
                'name'      => 'Test Channel Type',
                'status'    => 'Active',
                'fields'    => ''
            ));

        //create channel
        $channel = $I->haveRecord('channels', array(
                'name'              => 'Test Channel',
                'address'           => '',
                'website_url'       => '',
                'channel_type_id'   => $channel_type,
                'merchant_id'       => $merchant,
                'currency'          => 'MYR',
                'timezone'          => 'Asia/Kuala_Lumpur',
                'status'            => 'Active',
                'hidden'            => 0
            ));

        //create channel_sku
        $channel_sku = $I->haveRecord('channel_sku', array(
                'channel_sku_quantity'      => 100,
                'channel_sku_price'         => '100.00',
                'channel_sku_promo_price'   => '0.00',
                'channel_sku_coordinates'   => '',
                'channel_id'                => $channel,
                'product_id'                => $product,
                'sku_id'                    => $sku,
                'client_id'                 => $merchant,
                'channel_sku_active'        => 1,
                'sync_status'               => 'SUCCESS'
            ));

        //create order
        $order = $I->haveRecord('orders', array(
                'subtotal'              => '100.00',
                'total'                 => '100.00',
                'shipping_fee'          => '0.00',
                'cart_discount'         => '0.00',
                'total_discount'        => '0.00',
                'total_tax'             => '0.00',
                'currency'              => 'MYR',
                'forex_rate'            => 1,
                'merchant_id'           => $merchant,
                'channel_id'            => $channel,
                'tp_source'             => '',
                'status'                => 0,
                'partially_fulfilled'   => 0,
                'cancelled_status'      => 0,
                'paid_status'           => 1,
                'payment_type'          => '',
                'shipping_recipient'    => '',
                'shipping_phone'        => '',
                'shipping_street_1'     => '',
                'reserved'              => 0,
                'member_id'             => 0
            ));

        //create order_item
        $order_item = $I->haveRecord('order_items', array(
                'order_id'                  => $order,
                'ref_id'                    => $channel_sku,
                'ref_type'                  => 'ChannelSKU',
                'unit_price'                => '100.00',
                'sale_price'                => '1000.00',
                'sold_price'                => '100.00',
                'tax_inclusive'             => 1,
                'tax_rate'                  => '0.00',
                'tax'                       => '0.00',
                'original_quantity'         => 1,
                'quantity'                  => 1,
                'discount'                  => '0.00',
                'tp_discount'               => '0.00',
                'weighted_cart_discount'    => '0.00'
            ));

        //create return
        $return = $I->haveRecord('return_log', array(
                'order_id'      => $order,
                'order_item_id' => $order_item,
                'status'        => 'In Transit'
            ));

        //save ids of created test records
        $this->data_ids = array(
                'merchants'     => $merchant,
                'brands'        => $brand,
                'products'      => $product,
                'sku'           => $sku,
                'channel_types' => $channel_type,
                'channels'      => $channel,
                'channel_sku'   => $channel_sku,
                'orders'        => $order,
                'order_items'   => $order_item,
                'return_log'    => $return
            );
    }

    public function getDataIds() {
        return $this->data_ids;
    }

    public function refreshReturnRecord() {
        $I = $this;

        //remove previous return
        DB::table('return_log')->where('id', '=', $this->data_ids['return_log'])->delete();

        //insert new return
        $return = $I->haveRecord('return_log', array(
                'order_id'      => $this->data_ids['orders'],
                'order_item_id' => $this->data_ids['order_items'],
                'status'        => 'In Transit'
            ));

        $this->data_ids['return_log'] = $return;
    }

    public function clearTestData($role)
    {
        foreach ($this->data_ids as $table => $id) {
            $col = 'id';

            switch ($table) {
                case 'brands':
                    $col = 'brand_id';
                    break;
                case 'products':
                    $col = 'product_id';
                    break;
                case 'sku':
                    $col = 'sku_id';
                    break;
                case 'channel_sku':
                    $col = 'channel_sku_id';
                    break;
                default:
                    $col = 'id';
                    break;
            }

            DB::table($table)->where($col, '=', $id)->delete();
        }
    }
}
