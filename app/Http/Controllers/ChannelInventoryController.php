<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use App\Http\Controllers\InventoryController;
use Config;
use DB;
use Response;
use App\Services\MediaService as MediaService;
use Carbon\Carbon;
use Storage;
use Illuminate\Support\MessageBag;


class ChannelInventoryController extends InventoryController
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:view.channelproduct', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit.channelproduct', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create.channelproduct', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.channelproduct', ['only' => ['destroy']]);

        $this->admin = \Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_id=null)
    {
        $data = $this->getFiltersData($channel_id);

        $data['categories'] = array();
        $data['sync_statuses'] = array(
            'NEW'           => trans('product-management.channel_inventory_tooltip_new_product'),
            'SUCCESS'       => trans('product-management.channel_inventory_tooltip_synced'),
            'PROCESSING'    => trans('product-management.channel_inventory_tooltip_syncing'),
            'FAILED'        => trans('product-management.channel_inventory_tooltip_failed_sync')
        );

        if (empty($data['channels']) || count($data['channels']) == 0) {
            $data['channels'][''] = 'No Channels Found';
            $data['channel_by_types']['']['']= 'No Channels Found';
        }else{
            $channels = $data['channels'];
            $channel_by_types= array();
            $non_active = array();
            foreach ($data['channels'] as $channel) {
                if(strcmp($channel['status'],"Active")!=0){
                    $non_active[$channel['status']][] = $channel;
                }else{
                    $channel_by_types[$channel['type']][] = $channel;
                }
            }
            $data['channel_by_types'] = array_merge($channel_by_types,$non_active);
        }

        if(\Input::get('pid')){
            $data['selectedProds'] = $this->getProductsView(\Input::get('pid'), true, 'channels.inventory.product', \Input::get('cid'));
        }else{
            $data['selectedProds'] = '';
        }

        if(\Input::get('cid')){
            $data['cid'] = \Input::get('cid');
        }
        $response = json_decode($this->getGuzzleClient(array(), 'admin/categories')->getBody()->getContents());
        foreach ($response->categories as $cat) {
            $data['categories'][$cat->id] = $cat->full_name;
        }
        $data['channel_id'] = $channel_id;
        $data['admin'] = $this->admin;
        return view('channels.inventory.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->route('product_id');
        $channel_id = $request->get('channel');
        if (is_null($channel_id)) {
            flash()->error("An error has occured: Could not get product's channel.");
            return back();
        }

        $response = json_decode($this->getGuzzleClient(array('channel_id' => $channel_id), 'admin/inventories/' . $id)->getBody()->getContents());

        $data['product_name'] = $response->name;
        $data['merchant_name'] = !empty($response->merchant->name)?$response->merchant->name:'';
        $data['brand_name'] = !empty($response->brand->name)?$response->brand->name:'';
        $items = array();

        foreach ($response->sku_in_channel as $sku) {
            $options_string = '';

            foreach ($sku->sku_options as $option) {
                $options_string .= '<b>' . $option->option_name . '</b>: ' . $option->option_value . '<br>';
            }

            $options_string = rtrim($options_string, '<br>');

            $items[] = array(
               'sku_details'    => '<b>' . trans('product-management.inventory_label_hubwire_sku') . '</b> <br>' . $sku->sku->hubwire_sku . '<br><b>' . trans('product-management.inventory_label_supplier_sku') . '</b><br> ' . $sku->sku->sku_supplier_code,
               'options'        => $options_string,
               'quantity'       => $sku->channel_sku_quantity,
               'status'         => $sku->channel_sku_active,
               'live_price'     => sprintf('%.2f', $sku->channel_sku_live_price),
               'unit_price'     => sprintf('%.2f', $sku->channel_sku_price),
               'sale_price'     => sprintf('%.2f', $sku->channel_sku_promo_price),
               'sale_start_date'=> !empty($sku->promo_start_date)?date('Y-m-d', strtotime($sku->promo_start_date)):'',
               'sale_end_date'  => !empty($sku->promo_end_date)?date('Y-m-d', strtotime($sku->promo_end_date)):'',
               'coordinates'    => $sku->channel_sku_coordinates,
               'weight'         => $sku->sku->sku_weight . 'g',
               'channel_sku_id' => $sku->channel_sku_id
            );
        }

        $data['user'] = $this->admin;
        $data['items'] = $items;
        $data['product_id'] = $id;
        $data['statuses'] = array(1 => 'Active', 0 => 'Inactive');

        return view('channels.inventory.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $skusStatus         = $request->input('status');
        $skusUnitPrice      = $request->input('unit_price');
        $skusSalePrice      = $request->input('sale_price');
        $skusSaleStart      = $request->input('sale_start_date');
        $skusSaleEnd        = $request->input('sale_end_date');
        $skusCoordinate     = $request->input('coordinates');

        $channelSkus = array();
        foreach ($skusStatus as $channel_sku_id => $value) {
            $channelSkus[] = array(
                'channel_sku_id'            => $channel_sku_id,
                'channel_sku_active'        => $skusStatus[$channel_sku_id],
                'channel_sku_price'         => $skusUnitPrice[$channel_sku_id],
                'channel_sku_promo_price'   => $skusSalePrice[$channel_sku_id],
                'promo_start_date'          => $skusSaleStart[$channel_sku_id],
                'promo_end_date'            => $skusSaleEnd[$channel_sku_id],
                'channel_sku_coordinates'   => $skusCoordinate[$channel_sku_id]
            );
        }
        
        $response = json_decode($this->putGuzzleClient(array('sku_in_channel' => $channelSkus), 'admin/inventories/' . $id)->getBody()->getContents());
        
        if (empty($response->error)) {
            flash()->success('Details have been successfully updated.');
            return back();
        }
        else {
            $errorMsg = new MessageBag((array)$response->error);
            $response = array(
                'error'     => $errorMsg->all(),
                'success'   => false,
                );
            flash()->error("Something went wrong. Please check error message.");
            return back()->withInput()->withErrors($errorMsg->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function searchChannelInventory(Request $request) {
        if (empty($request->input('channel', ''))) {
            $data['start'] = $request->input('start');
            $data['length'] = $request->input('length');
            $data['draw'] = $request->input('draw');

            $data['recordsTotal'] = 0;
            $data['recordsFiltered'] = 0;
            $data['data'] = array();

            return Response::JSON($data);
        }

        $data = $this->search($request, 'channels.inventory.product');

        return $data;
    }


    // CATEGORIES

    // returns an array of categories
    public function getCategories($channel, $flag="default") {

        $data = [];
        $channel_type = $channel->channel_type->name;

        $data = (array) json_decode($this->getGuzzleClient(array('channel_type_name'=>$channel_type), 'channels/admin/categories/get')->getBody()->getContents());

        if (!empty($data)) {
            switch($flag) {
                case "TOP":
                    $data = $this->filterCategoriesTopLevel(array_keys($data), $channel->channel_type->id);
                    $data[0] = 'All';
                    break;
                case "BOTTOM":
                    $data = $this->filterCategoriesBottomLevel(array_keys($data), $channel->channel_type->id);
                    break;
                case "CATEGORY_NAMES":
                    $data = array_keys($data);
                    $data[0] = 'All';
                    break;
                default:
                    break;
            }
        }
        else {
            if ($flag=='TOP' || $flag=="CATEGORY_NAMES")
                $data[0] = 'All';
        }

        return $data;
    }

    // Returns only bottom level categories
    public function filterCategoriesBottomLevel($categories, $channel_type) {
        /*
            7 => 'Lelong',
            8 => 'Lazada',
            9 => 'Zalora',
            10 => '11Street',
            15 => 'RubberNeck',
            16 => 'BearInBag',
            17 => 'AmaxMall',
        */
        $separator = ($channel_type==7)? '/:/' : '/\//';
        $s = '/';
        $categoriesCount = array();
        $filtered = array();

        // 11street's categories are all bottom level
        if (!in_array($channel_type, array(10, 15, 17))) {
            foreach($categories as $cat) {
                //$cat = trim($cat);
                $categoriesCount[$cat] = 1;
                $subcats = preg_split($separator, $cat);
                if (count($subcats)==1)
                    $categoriesCount[$cat] += 1;

                $sCat = '';
                for ($i=0; $i<count($subcats)-1; $i++) {
                    $sCat = $sCat.$subcats[$i].$s;
                }
                if ($sCat!='') {
                    $sCat = substr($sCat, 0, -1);
                    if (array_key_exists($sCat, $categoriesCount))
                        $categoriesCount[$sCat] += 1;

                    else
                        $categoriesCount[$sCat] = 1;
                }

            }
            foreach($categories as $cat) {
                if ($categoriesCount[$cat] == 1)
                    $filtered[] = $cat;
            }
            //dd($filtered);
            return $filtered;
        }
        else
            return $categories;
    }

    // Returns only top level categories
    public function filterCategoriesTopLevel($categories, $channel_type) {
        /*
            7 => 'Lelong',
            8 => 'Lazada',
            9 => 'Zalora',
            10 => '11Street',
        */
        $separator = ($channel_type==7)? '/:/' : '/\//';
        $filtered = array();
        foreach($categories as $cat) {
            //$cat = ($channel_type==10)? substr($cat, 1) : $cat;
            $subcats = preg_split($separator, $cat);
            $subcat = trim($subcats[0]);

            if (!in_array($subcat, $filtered)) {
                $filtered[] = $subcat;
            }
        }
        //dd($filtered);
        return $filtered;
    }


    // CHANNEL INVENTORY CATEGORIES BULK UPDATE

    // shows the assign categories page
    public function bulkUpdateCategories() {

        $data = array();
        $data['product_ids'] = \Input::get('products');
        $data['channel_id'] = \Input::get('channel_id');
        $data['channel'] = $this->getChannel(\Input::get('channel_id'));

        if (array_key_exists($data['channel']->channel_type_id, config('globals.third_party_categories_applicable'))) {
            $data['categories'] = $this->getCategories($data['channel'], "BOTTOM");

            if (count($data['categories']) == 0) {
                flash()->error("No categories found.");
                return back();
            }

            $data['categoriesWithID'] = $this->getCategories($data['channel']);

            return view('channels.inventory.third_party_cat', $data);
        }
        else {
            flash()->error("Product category does not apply to the selected channel.");
            return back();
        }
    }

    public function bulkLoadCategories() {
        try {
            $ids = $_GET['product_ids'];
            $channelId = $_GET['channel_id'];

            $categories = array_flip($this->getCategories($this->getChannel($channelId)));
            $data['products'] = json_decode($this->getGuzzleClient(array('ids'=>$ids, 'channel_id'=>$channelId), 'admin/inventories/categories')->getBody()->getContents());

            // convert each category id to name
            for ($i=0; $i<count($data['products']); $i++) {
                if (!is_null($data['products'][$i]->cat_id)) {
                    $data['products'][$i]->cat_name = isset($categories[$data['products'][$i]->cat_id])? $categories[$data['products'][$i]->cat_id] : '';
                }
            }
            return response()->json(['success' => true, 'data' => $data]);
        }
        catch(Exception $e)
        {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function bulkSaveCategories() {
        try {
            $channelId = $_POST["channel_id"];
            $categories = $this->getCategories($this->getChannel($channelId));

            $changes = $_POST["data"];
            $data = array();
            $categoryExists = true;
            foreach ($changes as $change) {
                if (array_key_exists($change[3], $categories)) {
                    $change[4] = $categories[$change[3]];
                    $data[] = $change;
                }
                else {
                    $categoryExists = false;
                }
            }
            if ($categoryExists) {
                $response = json_decode($this->postGuzzleClient(array('changes'=>$data), 'admin/inventories/categories/save')->getBody()->getContents());

                if ($response->success)
                    return response()->json(['success' => $response->success, 'message' => 'Your changes were successfully saved.']);
            }

            else {
                return response()->json(['success' => false, 'error' => 'Some categories could not be assigned. Please verify that they exist.']);
            }

            return response()->json(['success' => false, 'error' => 'An error has occurred. Please try again.']);
        }
        catch(Exception $e)
        {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }


    // CHANNEL INVENTORY BULK UPDATE

    public function showBulkUpdate(Request $request) {
        $data = array();
        $data['product_ids'] = $request->input('products');
        $data['channel_id'] = $request->input('channel_id');
        $data['channel'] = $this->getChannel(\Input::get('channel_id'));
        $third_party_categories_applicable = array_keys(config('globals.third_party_categories_applicable'));
        $data['is_marketplace'] = in_array($data['channel']->channel_type_id, $third_party_categories_applicable) ?'true':'false';

        return view('channels.inventory.bulk_update', $data);
    }

    // Grabs a list of product ids and return products and associated skus & channel skus in a particular channel
    public function bulkLoad(Request $request) {
        try {
            $ids = $request->input('product_ids');
            $channelId = $request->input('channel_id');

            //dd($products);
            $response = json_decode($this->getGuzzleClient(array('ids'=>$ids, 'channel_id'=>$channelId), 'admin/inventories/bulk_update')->getBody()->getContents());
            $data['products'] = $response->products;


            $categories = array_flip($this->getCategories($this->getChannel($channelId)));
            // dd($categories);
            // convert each category id to name
            for ($i=0; $i<count($data['products']); $i++) {
                if (!is_null($data['products'][$i]->cat_id)) {
                    $data['products'][$i]->cat_name = isset($categories[$data['products'][$i]->cat_id])? $categories[$data['products'][$i]->cat_id] : '';
                    $data['products'][$i]->cat_id = $data['products'][$i]->cat_id;
                }
            }

            $data['custom_fields'] = $response->customFields;

            return response()->json(['success' => true, 'data' => $data]);
        }
        catch(Exception $e)
        {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => 'An error has occurred on the server: '.$e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function bulkSave(Request $request) {
        try {
            /*
             * fields that can be edited:
             * status       -> channel_sku_active -> channel sku
             * retail price -> channel_sku_price -> channel_sku
             * listing price   -> channel_sku_promo_price -> channel_sku
             * warehouse coordinates -> channel_sku_coordinates -> channel_sku
             * custom fields
             */
            $changes = $request->input("data");
            $response = json_decode($this->postGuzzleClient(array('changes'=>$changes), 'admin/inventories/bulk_update/save')->getBody()->getContents());

            return response()->json(['success' => true, 'message' => 'Your changes were successfully saved.']);
        }
        catch(Exception $e)
        {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => 'An error has occurred on the server: '.$e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function getChannel($channelId) {
        return json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$channelId)->getBody()->getContents());
    }

    public function syncProducts(Request $request) {
        $type = $request->route('type');

        $response = $this->postGuzzleClient($request->input(), 'admin/inventories/sync_products/' . $type);

        if ($response->getStatusCode() != 200) {
            flash()->error('Error creating syncs, please try again later');
            return back();
        }
        else {
            $response = json_decode($response->getBody()->getContents(), true);
            $message = $response['created'] . '/' . $response['total'] . ' sync(s) created.<br>';

            if (count($response['skipped_ids']) > 0) {
                $message .= (strcasecmp($type, 'create') == 0) ? 'The following sync(s) were skipped because they have been created previously, please use the following product id(s) to check the sync status: ' : 'Please make sure the following product(s) have been created in the marketplace: ';
                $message .= json_encode($response['skipped_ids']) . '<br>';
            }

            if (count($response['failed_data']) > 0) {
                $message .= ' Failed to create sync job for the following product(s). Errors:<br>';

                foreach ($response['failed_data'] as $data) {
                    $message .= 'Product ' . $data['product_id'] . ' >> ' . json_encode($data['error']);
                    $message .= ($data != end($response['failed_data'])) ? ', <br>' : '';
                }
            }

            flash()->info($message);
            if($this->admin->is('channelmanager')) {
                return redirect()->route('byChannel.admin.channels.sync_history.index', array('channel_id'=>$request->route('channel_id'), 'channel' => $request->input('channel')));
            } else {
                return redirect()->route('admin.channels.sync_history.index', array('channel' => $request->input('channel')));
            }
        }
    }

    public function generateProductListCsv($channelId)
    {
        $s3 = new MediaService();
        $channel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$channelId)->getBody()->getContents());
        $today = Carbon::now()->format('Ymd');
        $channelSkus = json_decode($this->getGuzzleClient(array(), 'admin/inventories/generate_channel_sku_list/'.$channelId)->getBody()->getContents());
        
        $header = array(
                    trans('product-management.channel_inventory_csv_label_sku_id'),
                    trans('product-management.channel_inventory_csv_channel_sku_id'),
                    trans('product-management.channel_inventory_csv_product_id'),
                    trans('product-management.channel_inventory_csv_hubwire_sku'),
                    trans('product-management.channel_inventory_csv_supplier_sku'),
                    trans('product-management.channel_inventory_csv_status'),
                    trans('product-management.channel_inventory_csv_product_name'),
                    trans('product-management.channel_inventory_csv_brand_prefix'),
                    trans('product-management.channel_inventory_csv_coordinates'),
                    trans('product-management.channel_inventory_csv_retail_price'),
                    trans('product-management.channel_inventory_csv_listing_price'),
                    trans('product-management.channel_inventory_csv_quantity'),
                );
    
        $items = array();
        $items[] = $header;

        foreach ($channelSkus as $csku) {
            $items[] = [$csku->sku->sku_id, 
                        $csku->channel_sku_id, 
                        $csku->product->id, 
                        $csku->sku->hubwire_sku, 
                        $csku->sku->sku_supplier_code, 
                        ($csku->channel_sku_active == 1 ? 'ACTIVE' : 'INACTIVE'), 
                        $csku->product->name, 
                        $csku->product->brand,
                        $csku->channel_sku_coordinates, 
                        $csku->channel_sku_price, 
                        ($csku->channel_sku_promo_price > 0 ? $csku->channel_sku_promo_price : $csku->channel_sku_price), 
                        $csku->channel_sku_quantity];
        }
        
        $fp = fopen(storage_path('/app/'.urlencode($channel->name).'_product_list_'.$today.'.csv'), 'w');
        foreach ($items as $fields) {
            fputcsv($fp, $fields);
        }
        $upload = $s3->uploadFileToS3(storage_path('/app/'.urlencode($channel->name).'_product_list_'.$today.'.csv'), 'channel_inventory/'.$channel->name.'_product_list_'.$today.'.csv');
        fclose($fp);

        if(is_array($upload) && array_key_exists("errors", $upload)) { 
            return false;
        }

        return $this->downloadFile($upload->url, $channel->name.'_product_list_'.$today, 'csv');
    }
}
