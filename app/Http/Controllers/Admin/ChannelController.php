<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use App\Http\Requests;
use App\Http\Traits\GuzzleClient;
use Illuminate\Http\Request;
use Validator;
use Form;
use Log;
use DB;
use App\Http\Controllers\ChannelInventoryController;
use App\Http\Controllers\Admin\CategoriesController;


class ChannelController extends AdminController
{
    use GuzzleClient;

    protected $admin;
    protected $inventory;

    public function __construct()
    {
        $this->middleware('permission:edit.channel', ['only' => ['edit', 'update', 'retrySync', 'cancelSync']]);
        $this->middleware('permission:create.channel', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete.channel', ['only' => ['destroy']]);
        $this->middleware('permission:view.channel', ['only' => ['index', 'syncHistoryIndex']]);

        $this->admin = \Auth::user();
        $this->inventory = new ChannelInventoryController;
    }

    public function index($channel_id=null)
    {
        return view('admin.channels.channel-list', ['user'=>\Auth::user(), 'channel_id'=>$channel_id]);
    }

    public function create()
    {
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants')->getBody()->getContents());
        $sampleStatus = array(
                'Active'    =>  'Active',
                'Inactive'  =>  'Inactive',
            );
        $channel_type_list = array();
        $channel_types = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type')->getBody()->getContents());
        foreach($channel_types as $channel_type)
        {
            $channel_type_list[$channel_type->id] = $channel_type->name;
        }
        $issuing_company_list = array();
        $issuing_companies = json_decode($this->getGuzzleClient(array(), 'admin/issuing_companies')->getBody()->getContents());
        foreach($issuing_companies as $company)
        {
            $issuing_company_list[$company->id] = $company->prefix." - ".$company->name;
        }
        foreach ($merchants->merchants as $merchant) {
           $shipping_merchants[$merchant->id] = htmlspecialchars($merchant->name, ENT_QUOTES);
        }
        $combine_array = array_combine(config('globals.shipping_provider'), config('globals.shipping_provider'));
        $data['docs_to_print'] = [0,1];
        $data['status'] = $sampleStatus;
        $data['channel_types'] = $channel_type_list;
        $data['issuing_companies'] = $issuing_company_list;
        $data['timezones'] = $this->generate_timezone_list();
        $data['currencies'] = config('globals.currency_list');
        $data['docs_to_print_list'] = config('globals.docs_to_print');
        $data['merchants'] = $merchants->merchants;
        $data['shipping_merchants'] = $shipping_merchants;
        $data['shipping_provider_list'] = $combine_array;
        $data['location']['Region'] = config('globals.malaysia_region');
        $data['location']['State'] = config('globals.malaysia_state');
        $data['location']['Countries'] = config('countries');
        $data['location']['Other']['All'] = 'All';
        $data['location']['Other']['Other'] = 'Other';

        //setup of the js select option
        $data['locationOption'] = '';
        $data['locationOption'] .= '<optgroup label="Region">';
        foreach ($data['location']['Region'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';
        $data['locationOption'] .= '<optgroup label="State">';
        foreach ($data['location']['State'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';
        $data['locationOption'] .= '<optgroup label="Countries">';
        foreach ($data['location']['Countries'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';
        $data['locationOption'] .= '<optgroup label="Other">';
        foreach ($data['location']['Other'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';

        //shipping merchant
        $data['merchantOption'] = '';
        $data['merchantOption'] .= '<optgroup label="Merchant">';
        foreach ($data['shipping_merchants'] as $id => $name) {
            $data['merchantOption'] .= '<option value="'.$id.'">'.$name.'</option>';
        }
        $data['merchantOption'] .= '</optgroup>';


        return view('admin.channels.channel-create', $data);
    }

    public function show($id)
    {
        $data['user'] = \Auth::user();
        $merchants = json_decode($this->getGuzzleClient(array(), 'admin/merchants')->getBody()->getContents());
        $sampleStatus = array(
                'Active'    =>  'Active',
                'Inactive'  =>  'Inactive',
            );
        $data['status'] = $sampleStatus;

        $channel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());
        $extra_info = !empty($channel->channel_detail->extra_info) ? json_decode($channel->channel_detail->extra_info, true) : null;

        $custom_fields = $this->getChannelTypeFields($channel->channel_type_id, false, $extra_info, true);

        $channel_type_list = array();
        $channel_types = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type')->getBody()->getContents());
        foreach($channel_types as $channel_type)
        {
            if($channel_type->id == $channel->channel_type_id) $is_marketplace = ($channel_type->third_party==1)?true:false;
            $channel_type_list[$channel_type->id] = $channel_type->name;
        }
        $issuing_company_list = array();
        $issuing_companies = json_decode($this->getGuzzleClient(array(), 'admin/issuing_companies')->getBody()->getContents());
        foreach($issuing_companies as $company)
        {
            $issuing_company_list[$company->id] = $company->prefix." - ".$company->name;
        }
        //get the shipping merchant name using id
        foreach ($merchants->merchants as $merchant) {
            $fullMerchant[$merchant->id] = htmlspecialchars($merchant->name, ENT_QUOTES);
        }
        $getShippingRate = is_null($channel->channel_detail->shipping_rate) ? array() : json_decode($channel->channel_detail->shipping_rate, true);
        $shipping_merchants = '';
        foreach ($getShippingRate as $key => $value) {
            $value['shipping_merchant'] = (isset($value['shipping_merchant']) && is_array($value['shipping_merchant']))?$value['shipping_merchant']:array();
            $copyValueToKey = array_combine($value['shipping_merchant'], $value['shipping_merchant']);
            $shippingMerchants = array_intersect_key($fullMerchant, $copyValueToKey);
            foreach ($shippingMerchants as $name) {
                $shipping_merchants[$key] = '';
                $shipping_merchants[$key] .= '- ';
                $shipping_merchants[$key] .= $name;
                $shipping_merchants[$key] .= '<br/>';
            }
        }


        //$extra_info = json_decode($channel->channel_detail->raw_extra_info, true);

        if(is_null($channel->oauth_client)){
            $data['storefrontapi'] = false;
            $data['storefrontapi_id'] = '-';
            $data['storefrontapi_secret'] = '-';
        }else{
            $data['storefrontapi'] = true;
            $data['storefrontapi_id'] = $channel->oauth_client->id;
            $data['storefrontapi_secret'] = $channel->oauth_client->secret;
        }

        $data['id']                 = $channel->id;
        $data['name']               = $channel->name;
        $data['address']            = $channel->address;
        $data['website_url']        = $channel->website_url;
        $data['channel_type_id']    = $channel->channel_type_id;
        $data['currency']           = !empty($channel->currency) ? $channel->currency : '';
        $data['timezone']           = !empty($channel->timezone) ? $channel->timezone : '';
        $data['status']             = $channel->status;
        $data['issuing_company']    = $channel->issuing_company->id;
        $data['docs_to_print']      = explode(", ", $channel->docs_to_print);
        $data['shipping_provider']  = !empty($extra_info['shipping_provider'])?$extra_info['shipping_provider']:null;
        $data['cod']                = !empty($extra_info['shipping_provider_cod'])?$extra_info['shipping_provider_cod']:null;
        $data['hidden']             = $channel->hidden;
        $data['support_email']      = !empty($channel->channel_detail) ? $channel->channel_detail->support_email : '';
        $data['noreply_email']      = !empty($channel->channel_detail) ? $channel->channel_detail->noreply_email : '';
        $data['finance_email']      = !empty($channel->channel_detail) ? $channel->channel_detail->finance_email : '';
        $data['marketing_email']    = !empty($channel->channel_detail) ? $channel->channel_detail->marketing_email : '';
        $data['api_key']            = !empty($channel->channel_detail) ? $channel->channel_detail->api_key : '';
        $data['api_password']       = !empty($channel->channel_detail) ? $channel->channel_detail->api_password : '';
        $data['api_secret']         = !empty($channel->channel_detail) ? $channel->channel_detail->api_secret : '';
        $data['returns_chargable']  = isset($channel->channel_detail->returns_chargable) ? $channel->channel_detail->returns_chargable : 0;
        $data['money_flow']         = !empty($channel->channel_detail) ? $channel->channel_detail->money_flow : '';
        $data['sale_amount']         = !empty($channel->channel_detail) ? $channel->channel_detail->sale_amount : '';
        $data['picking_manifest']   = !empty($channel->channel_detail) ? $channel->channel_detail->picking_manifest : false;
        $data['channel_merchants']  = $channel->merchants;

        $data['statuses'] = $sampleStatus;
        $data['channel_types'] = $channel_type_list;
        $data['issuing_companies'] = $issuing_company_list;
        $data['timezones'] = $this->generate_timezone_list();
        $data['currencies'] = config('globals.currency_list');
        $data['merchants'] = $merchants->merchants;
        $data['custom_fields'] = $custom_fields;
        $data['is_marketplace'] = $is_marketplace;
        $data['docs_to_print_list'] = config('globals.docs_to_print');
        $data['shipping_default'] = ($channel->channel_detail->shipping_default)? 'Yes': 'No';
        $data['shipping_rate'] = json_decode($channel->channel_detail->shipping_rate, true);
        $data['shipping_merchants'] = $shipping_merchants;
        $data['use_shipping_rate'] = ($channel->channel_detail->use_shipping_rate)? 'Yes': 'No';

        if (strcasecmp($channel_type_list[$channel->channel_type_id], 'Shopify') == 0) {
            $data['webhooks'] = $channel->webhooks;
        }elseif (strcasecmp($channel_type_list[$channel->channel_type_id], 'Shopify POS') == 0) {
            $data['webhooks'] = $channel->webhooks;
        }

        if (strcasecmp($channel_type_list[$channel->channel_type_id], 'Lelong') == 0) {
            $data['edit'] = false;
            $data['store_categories'] = json_decode($this->getGuzzleClient(array(), 'channels/' . $id . '/get_store_categories')->getBody()->getContents())->store_categories;
        }
        //dd($channel->channel_detail);
        return view('admin.channels.channel-show', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, array(
            'name'                  => 'required|max:255',
            'channel_type_id'       => 'required|max:255',
            'address'               => 'required|max:255',
            'website_url'           => 'required|max:255',
            'currency'              => 'required',
            'timezone'              => 'required',
            'status'                => 'required',
            'money_flow'            => 'required',
            'sale_amount'           => 'required',
            'picking_manifest'      => 'required',
            'support_email'         => 'required|email|max:255',
            'noreply_email'         => 'required|email|max:255',
            'finance_email'         => 'required|email|max:255',
            'marketing_email'       => 'required|email|max:255',
            'docs_to_print'         => 'sometimes|array',
        ));

        //checking for shopify
        if ($request->input('channel_type_id') == 6 && preg_match("/(http\:\/\/)|(https\:\/\/)/", $request->input('website_url'))) {
            $errors = new \StdClass;
            $errors->website_url = 'The website url for Shopify cannot contain "http://" or "https://"';
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // To validate the custom fields
        $custom_values = $request->input('field_value');
        $custom_labels = $request->input('field_label');
        $required_fields = $request->input('field_required');
        $default_values = $request->input('field_default');
        $api_fields = $request->input('field_api');
        $outputs = array();
        $extraInfo = array();

        if(!is_null($custom_labels)){
            foreach($custom_labels as $index => $custom_label)
            {
                $outputs[$index]['label'] = $custom_label;
            }
        }
        if(!is_null($required_fields)){
            foreach($required_fields as $index => $required_field)
            {
                $outputs[$index]['required'] = $required_field;
            }
        }
        if(!is_null($custom_values)){
            foreach($custom_values as $index => $custom_value)
            {
                $outputs[$index]['value'] = $custom_value;
            }
        }
        if(!is_null($default_values)){
            foreach($default_values as $index => $default_value)
            {
                $outputs[$index]['default'] = $default_value;
            }
        }
        if(!is_null($api_fields)){
            foreach($api_fields as $index => $api_field)
            {
                $outputs[$index]['api'] = $api_field;
            }
        }

        $errors = array();
        $custom_field_error = false;
        foreach($outputs as $output)
        {
            // if the custom field is required, perform validation
            if($output['required'] == 1 && empty($output['default'])){
                $rules = array(
                        $output['label']    =>    'required',
                    );
                $validator = Validator::make(array($output['label'] => $output['value']), $rules, array());
                if ($validator->passes()) {
                    $extraInfo[$output['api']] = $output['value'];
                }else{
                    $custom_field_error = true;
                    $errors[] = $validator->errors()->first();
                }
            }else{
                $extraInfo[$output['api']] = $output['value'];
            }
        }
        $extraInfo['shipping_provider'] = $request->input('shipping_provider');
        if($custom_field_error){
            $error_msg = '';
            foreach($errors as $error)
                $error_msg .= $error . '<br/>';

            flash()->error($error_msg);
            return back()->withInput();
        }

        //set shipping rate to array
        $shippingRegion = $request->input('region', array());
        $shippingLocation = $request->input('location', array());
        $shippingBaseAmount = $request->input('base_amount', array());
        $shippingBaseGrams = $request->input('base_grams', array());
        $shippingIcrementAmount = $request->input('increment_amount', array());
        $shippingIcrementGrams = $request->input('increment_grams', array());
        $shippingRate = array();
        foreach ($shippingRegion as $key => $value) {
            if (!empty($value)) {
                $shippingRate[] = array(
                    'region' => $value, 
                    'location' => $shippingLocation[$key], 
                    'base_amount' => isset($shippingBaseAmount[$key])? $shippingBaseAmount[$key]*1.06 : null,
                    'base_grams' => isset($shippingBaseGrams[$key])? $shippingBaseGrams[$key] : null,
                    'increment_amount' => isset($shippingIcrementAmount[$key])? $shippingIcrementAmount[$key]*1.06 : null,
                    'increment_grams' => isset($shippingIcrementGrams[$key])? $shippingIcrementGrams[$key] : null,
                );
            }
        }

        $postData = array();
        $postData['channel'] = array(
                'name'              =>  $request->input('name'),
                'address'           =>  $request->input('address'),
                'website_url'       =>  $request->input('website_url'),
                'channel_type_id'   =>  $request->input('channel_type_id'),
                'currency'          =>  $request->input('currency'),
                'timezone'          =>  $request->input('timezone'),
                'status'            =>  $request->input('status'),
                'issuing_company'   =>  $request->input('issuing_company'),
                'docs_to_print'     =>  implode(", ", $request->input('docs_to_print', array())),
            );
        $postData['channel_detail'] = array(
                'support_email'     =>  $request->input('support_email'),
                'noreply_email'     =>  $request->input('noreply_email'),
                'finance_email'     =>  $request->input('finance_email'),
                'marketing_email'   =>  $request->input('marketing_email'),
                'api_key'           =>  $request->input('api_key'),
                'api_password'      =>  $request->input('api_password'),
                'api_secret'        =>  $request->input('api_secret'),
                'returns_chargable' =>  $request->input('returns_chargable', 0),
                'money_flow'        =>  $request->input('money_flow'),
                'sale_amount'       =>  $request->input('sale_amount'),
                'picking_manifest'  =>  $request->input('picking_manifest'),
                'extra_info'        =>  json_encode($extraInfo),
                'shipping_rate'     =>  json_encode($shippingRate),
                'shipping_default'  =>  is_null($request->input('shipping_default'))?0: $request->input('shipping_default'),
                'use_shipping_rate' =>  $request->input('use_shipping_rate', 0),
            );
        $postData['merchants'] = $request->input('merchant_id');

        $response = json_decode($this->postGuzzleClient($postData, 'channels/channel')->getBody()->getContents());

        $add_message = '';
        if ($response->channel_detail->channel->channel_type->name == 'Lazada' || $response->channel_detail->channel->channel_type->name == 'LazadaSC' || $response->channel_detail->channel->channel_type->name == 'Zalora') {
            if(!empty($response->channel_detail->api_key) || !empty($response->channel_detail->api_password) || !empty($response->channel_detail->api_secret)){
                $call_api = json_decode($this->getShippingProvider($response->id)->getContent());
                if ($call_api->success==false) {
                    $add_message = 'API setting error, cannot get shipping provider.';
                }

            }else{
                $add_message = 'Warning: API setting is incomplete, cannot get shipping provider.';
            }
        }

        if(empty($response->error)){
            $message = 'Channel ' . $request->input('name') .' has been successfully created. '.$add_message;

            flash()->success($message);
            return redirect()->route('admin.channels.index');
        }else{
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $data['user'] = \Auth::user();
        $merchants = $channel_types = json_decode($this->getGuzzleClient(array(), 'admin/merchants')->getBody()->getContents());
        $sampleStatus = array(
                'Active'    =>  'Active',
                'Inactive'  =>  'Inactive',
            );
        $data['status'] = $sampleStatus;

        $channel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());

        $extra_info = !empty($channel->channel_detail->raw_extra_info) ? json_decode($channel->channel_detail->raw_extra_info, true) : null;
        $custom_fields = $this->getChannelTypeFields($channel->channel_type_id, false, $extra_info);

        $channel_type_list = array();
        $channel_types = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type')->getBody()->getContents());

        foreach($channel_types as $channel_type)
        {
            if($channel_type->id == $channel->channel_type_id) $is_marketplace = ($channel_type->third_party==1)?true:false;
            $channel_type_list[$channel_type->id] = $channel_type->name;
        }
        $issuing_company_list = array();
        $issuing_companies = json_decode($this->getGuzzleClient(array(), 'admin/issuing_companies')->getBody()->getContents());
        foreach($issuing_companies as $company)
        {
            $issuing_company_list[$company->id] = $company->prefix." - ".$company->name;
        }

        $get_shipping_provider = json_decode($channel->channel_detail->raw_extra_info, true);
        $shipping_provider_detail = [
            'shipping_provider' => !empty($get_shipping_provider['shipping_provider'])?$get_shipping_provider['shipping_provider']:null, 
            'shipping_provider_cod' => !empty($get_shipping_provider['shipping_provider_cod'])?$get_shipping_provider['shipping_provider_cod']:null, 
            ];
        $combine_array = array_combine(config('globals.shipping_provider'), config('globals.shipping_provider'));

        //$extra_info = json_decode($channel->channel_detail->raw_extra_info, true);
        $shipping_provider_detail = [
            'shipping_provider' => !empty($extra_info['shipping_provider'])?$extra_info['shipping_provider']:null,
            'shipping_provider_cod' => !empty($extra_info['shipping_provider_cod'])?$extra_info['shipping_provider_cod']:null,
            ];
        $combine_array = array_combine(config('globals.shipping_provider'), config('globals.shipping_provider'));

        if(is_null($channel->oauth_client)){
            $data['storefrontapi'] = false;
            $data['storefrontapi_id'] = '-';
            $data['storefrontapi_secret'] = '-';
        }else{
            $data['storefrontapi'] = true;
            $data['storefrontapi_id'] = $channel->oauth_client->id;
            $data['storefrontapi_secret'] = $channel->oauth_client->secret;
        }
        $shipping_merchants = array();
        foreach ($channel->merchants as $merchant) {
           $shipping_merchants[$merchant->id] = htmlspecialchars($merchant->name, ENT_QUOTES);
        }

        $data['id']                 = $channel->id;
        $data['name']               = $channel->name;
        $data['address']            = $channel->address;
        $data['website_url']        = $channel->website_url;
        $data['channel_type_id']    = $channel->channel_type_id;
        $data['channel_type']       = $channel->channel_type->name;
        $data['currency']           = $channel->currency;
        $data['timezone']           = $channel->timezone;
        $data['status']             = $channel->status;
        $data['issuing_company']    = $channel->issuing_company;
        $data['docs_to_print']      = explode(", ", $channel->docs_to_print);
        $data['shipping_provider']  = $shipping_provider_detail['shipping_provider'];
        $data['cod']                = $shipping_provider_detail['shipping_provider_cod'];
        $data['hidden']             = $channel->hidden;

        $data['support_email']      = !empty($channel->channel_detail) ? $channel->channel_detail->support_email : '';
        $data['noreply_email']      = !empty($channel->channel_detail) ? $channel->channel_detail->noreply_email : '';
        $data['finance_email']      = !empty($channel->channel_detail) ? $channel->channel_detail->finance_email : '';
        $data['marketing_email']    = !empty($channel->channel_detail) ? $channel->channel_detail->marketing_email : '';
        $data['api_key']            = !empty($channel->channel_detail) ? $channel->channel_detail->api_key : '';
        $data['api_password']       = !empty($channel->channel_detail) ? $channel->channel_detail->api_password : '';
        $data['api_secret']         = !empty($channel->channel_detail) ? $channel->channel_detail->api_secret : '';
        $data['returns_chargable']  = isset($channel->channel_detail->returns_chargable) ? $channel->channel_detail->returns_chargable : 0;
        $data['money_flow_fmhw']    = ($channel->channel_detail->money_flow == 'FMHW') ? 1 : 0;
        $data['money_flow_merchant']= ($channel->channel_detail->money_flow == 'Merchant') ? 1 : 0;
        $data['sale_amount_listing']= (isset($channel->channel_detail->sale_amount) && $channel->channel_detail->sale_amount) ? 1 : 0;
        $data['sale_amount_sold']   = (isset($channel->channel_detail->sale_amount) && $channel->channel_detail->sale_amount) ? 0 : 1;
        $data['picking_manifest_yes']= ($channel->channel_detail->picking_manifest) ? 1 : 0;
        $data['picking_manifest_no']= ($channel->channel_detail->picking_manifest) ? 0 : 1;

        $data['channel_merchants']  = $channel->merchants;
        $data['shipping_merchants']  = $shipping_merchants;
        $data['shipping_default']  = $channel->channel_detail->shipping_default;
        $data['use_shipping_rate']  = $channel->channel_detail->use_shipping_rate;

        $data['statuses'] = $sampleStatus;
        $data['channel_types'] = $channel_type_list;
        $data['issuing_companies'] = $issuing_company_list;
        $data['timezones'] = $this->generate_timezone_list();
        $data['currencies'] = config('globals.currency_list');
        $data['docs_to_print_list'] = config('globals.docs_to_print');
        $data['shipping_provider_list'] = empty($shipping_provider_detail['shipping_provider_cod'])?$combine_array:$shipping_provider_detail['shipping_provider_cod'];
        $data['merchants'] = $merchants->merchants;
        $data['custom_fields'] = $custom_fields;
        $data['is_marketplace'] = $is_marketplace;
        $data['is_warehouse'] = (strcmp($channel_type_list[$channel->channel_type_id],'Warehouse')==0)?true:false;

        $categoryController = new CategoriesController;
        $data['categories'] = $this->processCFCategories(array_keys($categoryController->getCategories($channel->channel_type_id)), $channel->channel_type_id);

        if (strcasecmp($channel_type_list[$channel->channel_type_id], 'Shopify') == 0) {
            $data['webhooks'] = $channel->webhooks;
        }elseif (strcasecmp($channel_type_list[$channel->channel_type_id], 'Shopify POS') == 0) {
            $data['webhooks'] = $channel->webhooks;
        }

        if (strcasecmp($channel_type_list[$channel->channel_type_id], 'Lelong') == 0) {
            $data['edit'] = true;
            $response = json_decode($this->getGuzzleClient(array(), 'channels/' . $id . '/get_store_categories')->getBody()->getContents());

            $data['store_categories'] = $response->store_categories;
            $data['tags'] = $response->tags;
        }
        $data['shipping_rate']= json_decode($channel->channel_detail->shipping_rate, true);
        $data['location']['Region'] = config('globals.malaysia_region');
        $data['location']['State'] = config('globals.malaysia_state');
        $data['location']['Countries'] = config('countries');
        $data['location']['Other']['All'] = 'All';
        $data['location']['Other']['Other'] = 'Other';
        $data['shipping_rate'] = is_array($data['shipping_rate'])? $data['shipping_rate']: array();
        foreach ($data['shipping_rate'] as $dataFromDB) {
            $allDataFromConfig = array_merge(config('globals.malaysia_region'), config('globals.malaysia_state'), config('countries'));config('globals.malaysia_region');
            foreach ($allDataFromConfig as $dataFromConfig) {
                if ($dataFromDB['location'] != $dataFromConfig) {
                    $data['location']['Other'][$dataFromDB['location']] = $dataFromDB['location'];
                }
            }
        }

        //setup of the js select option
        $data['locationOption'] = '';
        $data['locationOption'] .= '<optgroup label="Region">';
        foreach ($data['location']['Region'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';
        $data['locationOption'] .= '<optgroup label="State">';
        foreach ($data['location']['State'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';
        $data['locationOption'] .= '<optgroup label="Countries">';
        foreach ($data['location']['Countries'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';
        $data['locationOption'] .= '<optgroup label="Other">';
        foreach ($data['location']['Other'] as $value) {
            $data['locationOption'] .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $data['locationOption'] .= '</optgroup>';

        //shipping merchant
        $data['merchantOption'] = '';
        $data['merchantOption'] .= '<optgroup label="Merchant">';
        foreach ($data['shipping_merchants'] as $id => $name) {
            $data['merchantOption'] .= '<option value="'.$id.'">'.$name.'</option>';
        }
        $data['merchantOption'] .= '</optgroup>';

        return view('admin.channels.channel-edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, array(
            'name'                  => 'required|max:255',
            'channel_type_id'       => 'required',
            'address'               => 'required|max:255',
            'website_url'           => 'required|max:255',
            'currency'              => 'required',
            'timezone'              => 'required',
            'status'                => 'required',
            'money_flow'            => 'required',
            'sale_amount'           => 'required',
            'picking_manifest'      => 'required',
            'support_email'         => 'required|email|max:255',
            'noreply_email'         => 'required|email|max:255',
            'finance_email'         => 'required|email|max:255',
            'marketing_email'       => 'required|email|max:255',
            'docs_to_print'         => 'sometimes|array',
        ));

        //checking for shopify
        if ($request->input('channel_type_id') == 6 && preg_match("/(http\:\/\/)|(https\:\/\/)/", $request->input('website_url'))) {
            $errors = new \StdClass;
            $errors->website_url = 'The website url for Shopify cannot contain "http://" or "https://"';
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // To validate the custom fields
        $custom_values = $request->input('field_value');
        $custom_labels = $request->input('field_label');
        $required_fields = $request->input('field_required');
        $default_values = $request->input('field_default');
        $api_fields = $request->input('field_api');
        $outputs = array();
        $extraInfo = array();
        if (!empty($custom_labels)) {
            foreach($custom_labels as $index => $custom_label)
            {
                $outputs[$index]['label'] = $custom_label;
            }
        }

        if (!empty($required_fields)) {
            foreach($required_fields as $index => $required_field)
            {
                $outputs[$index]['required'] = $required_field;
            }
        }

        if (!empty($custom_values)) {
            foreach($custom_values as $index => $custom_value)
            {
                $outputs[$index]['value'] = $custom_value;
            }
        }

        if (!empty($default_values)) {
            foreach($default_values as $index => $default_value)
            {
                $outputs[$index]['default'] = $default_value;
            }
        }

        if (!empty($api_fields)) {
            foreach($api_fields as $index => $api_field)
            {
                $outputs[$index]['api'] = $api_field;
            }
        }

        $errors = array();
        $custom_field_error = false;
        foreach($outputs as $output)
        {
            // if the custom field is required, perform validation
            if($output['required'] == 1 && empty($output['default'])){
                $rules = array(
                        $output['label']    =>    'required',
                    );
                $validator = Validator::make(array($output['label'] => $output['value']), $rules, array());
                if ($validator->passes()) {
                    $extraInfo[$output['api']] = $output['value'];
                }else{
                    $custom_field_error = true;
                    $errors[] = $validator->errors()->first();
                }
            }else{
                $extraInfo[$output['api']] = $output['value'];
            }
        }

        $extraInfo['shipping_provider'] = $request->input('shipping_provider');
        $extraInfo['shipping_provider_cod'] = $request->input('shipping_provider_cod');

        if($custom_field_error){
            $error_msg = '';
            foreach($errors as $error)
                $error_msg .= $error . '<br/>';

            flash()->error($error_msg);
            return back()->withInput();
        }

        //set shipping rate to array
        $shippingRegion = $request->input('region', array());
        $shippingLocation = $request->input('location', array());
        $shippingBaseAmount = $request->input('base_amount', array());
        $shippingBaseGrams = $request->input('base_grams', array());
        $shippingIcrementAmount = $request->input('increment_amount', array());
        $shippingIcrementGrams = $request->input('increment_grams', array());
        $shippingMerchant = $request->input('shipping_merchant', array());
        $shippingRate = array();
        foreach ($shippingRegion as $key => $value) {
            if (!empty($value)) {
                $shippingRate[] = array(
                    'region' => $value, 
                    'location' => $shippingLocation[$key], 
                    'base_amount' => isset($shippingBaseAmount[$key])? $shippingBaseAmount[$key]*1.06 : null,
                    'base_grams' => isset($shippingBaseGrams[$key])? $shippingBaseGrams[$key] : null,
                    'increment_amount' => isset($shippingIcrementAmount[$key])? $shippingIcrementAmount[$key]*1.06 : null,
                    'increment_grams' => isset($shippingIcrementGrams[$key])? $shippingIcrementGrams[$key] : null,
                    'shipping_merchant' => isset($shippingMerchant[$key])? (Array)$shippingMerchant[$key] : null,
                );
            }
        }
    
        $postData = array();
        $postData['channel'] = array(
                'name'              =>  $request->input('name'),
                'address'           =>  $request->input('address'),
                'website_url'       =>  $request->input('website_url'),
                'channel_type_id'   =>  $request->input('channel_type_id'),
                'currency'          =>  $request->input('currency'),
                'timezone'          =>  $request->input('timezone'),
                'status'            =>  $request->input('status'),
                'hidden'            =>  $request->input('hidden'),
                'docs_to_print'     =>  implode(", ", $request->input('docs_to_print')),
            );

        if($request->input('issuing_company')!==null) $postData['channel']['issuing_company']=$request->input('issuing_company');

        $postData['channel_detail'] = array(
                'support_email'     =>  $request->input('support_email'),
                'noreply_email'     =>  $request->input('noreply_email'),
                'finance_email'     =>  $request->input('finance_email'),
                'marketing_email'   =>  $request->input('marketing_email'),
                'api_key'           =>  $request->input('api_key'),
                'api_password'      =>  $request->input('api_password'),
                'api_secret'        =>  $request->input('api_secret'),
                'returns_chargable' =>  $request->input('returns_chargable', 0),
                'money_flow'        =>  $request->input('money_flow'),
                'sale_amount'       =>  $request->input('sale_amount'),
                'picking_manifest'  =>  $request->input('picking_manifest'),
                'extra_info'        =>  json_encode($extraInfo),
                'shipping_rate'     =>  json_encode($shippingRate),
                'shipping_default'  =>  is_null($request->input('shipping_default'))?0: $request->input('shipping_default'),
                'use_shipping_rate' =>  is_null($request->input('use_shipping_rate'))?0: $request->input('use_shipping_rate'),
            );
        
        $postData['merchants'] = $request->input('merchant_id');
        $postData['tags'] = $request->input('tags', '');

        $response = json_decode($this->putGuzzleClient($postData, 'channels/channel/'.$id)->getBody()->getContents());
        if($request->callApi=='true'){
            $route = 'admin.channels.show';
            if ($this->admin->is('channelmanager')){
                $route = 'byChannel.admin.channels.show';
            }
                $return = redirect()->route($route, $id);
        }
            elseif ($request->callApi=='false') {
                $add_message = '';
                if(!empty($response->channel_detail->api_key) || !empty($response->channel_detail->api_password) || !empty($response->channel_detail->api_secret)){
                    $call_api = json_decode($this->getShippingProvider($response->id)->getContent());
                    if ($call_api->success==false) {
                        $add_message = 'API setting error, cannot get shipping provider.';
                    }else{
                        $add_message = 'Shipping Provider has been updated successfully.';
                    }

                }else{
                    $add_message = 'Warning: API setting is incomplete, cannot get shipping provider.';
                }
                flash()->success($add_message);
                return redirect()->route('admin.channels.edit', $response->id);
            }

        if(empty($response->error)){
            $message = 'Channel ' . $request->input('name') .' has been successfully updated.';

            flash()->success($message);
            $route = 'admin.channels.show';
            if ($this->admin->is('channelmanager')){
                $route = 'byChannel.admin.channels.show';
            }
            return $return;

        }else{
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $channel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());
        $response = json_decode($this->deleteGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());

        if($response->response === true){
            $message = 'Channel ' . $channel->name .' has been successfully deleted.';
            flash()->success($message);
            return redirect()->route('admin.channels.index');
        }elseif(empty($response->error)){
            flash()->error('An error has occured: ' . $response->response);
            return back();
        }else{
            $message = 'Unable to delete ' . $channel->name .', please try again.';
            flash()->error($message);
            return redirect()->route('admin.channels.index');
        }
    }

    public function getChannelsTableData()
    {
        $channels = json_decode($this->getGuzzleClient(request()->all(), 'channels/channel')->getBody()->getContents());
        $channel_types = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type')->getBody()->getContents());
        foreach($channels as $index => $data)
        {
            $button = '';
            foreach($channel_types as $channel_type)
            {
                if($channel_type->id == $data->channel_type_id){
                    $channels[$index]->type = $channel_type->name;
                    break;
                }
            }
            if($data->status == 'Active')
                $statusBtnLabel = 'Deactivate';
            elseif($data->status == 'Inactive')
                $statusBtnLabel = 'Activate';
            //$channels[$index]->type = $channel_types[($data->channel_type_id-1)]->name;
            $channels[$index]->merchant_count = count($data->merchants);
            if($this->admin->can('edit.channel')){
                $route = route('admin.channels.edit', [$data->id]);
                if ($this->admin->is('channelmanager')){
                    $route = route('byChannel.admin.channels.edit', [$data->id]);
                }
                $button = Form::open(array('url'=> route('admin.channels.deactivate', [$data->id]), 'class' => 'form-inline', 'method' => 'GET')) .'<a href="'.$route.'">Edit</a> |<button type="submit" class="btn btn-link confirmation delete-'.$data->id.'">'.$statusBtnLabel.'</button>'.Form::close();
                $channels[$index]->actions = $button;
            }else{
                $channels[$index]->actions = '';
            }
            $route = route('admin.channels.show', $data->id);
            if ($this->admin->is('channelmanager')){
                $route = route('byChannel.admin.channels.show', $data->id);
            }
            $channels[$index]->name = '<a href="'.$route.'">'.$data->name.'</a>';
        }
        return json_encode(array("data" => $channels));
    }

    public function deactivate($id)
    {
        $channel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());
        $postData = array();
        $statusUpdate = '';
        if($channel->status == 'Active'){
            $statusUpdate = 'Inactive';
        }elseif($channel->status == 'Inactive'){
            $statusUpdate = 'Active';
        }
        $postData['channel'] = array('status'=>$statusUpdate);
        $postData['channel_detail'] = $channel->channel_detail;
        $response = json_decode($this->putGuzzleClient($postData, 'channels/channel/'.$id)->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Channel ' . $channel->name .'\'s status has been successfully updated.';
            flash()->success($message);
            return redirect()->route('admin.channels.index');
        }else{
            $message = 'Unable to update ' . $channel->name .', please try again.';
            flash()->error($message);
            return redirect()->route('admin.channels.index');
        }
    }

    public function getChannelTypeFields($id , $ajax = true, $values = null, $read_only = false)
    {
        $response = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$id)->getBody()->getContents());
        $data['read_only'] = $read_only;
        $data['fields'] = json_decode($response->fields);
        if(!is_null($values) && !is_null($data['fields'])){
            foreach($data['fields'] as $index => $field)
            {
                foreach($values as $key => $value)
                {
                    if($key == $field->api){
                        $field->value = $value;
                    }
                }
            }
        }
        if(!is_null($response->fields))
        {
            $view = \View::make('admin.channels.channel-type-fields-form', $data);
            $rendered_view = $view->render();
        }else{
            $rendered_view = '';
        }

        if($ajax){
            $output = array();
            $output['success'] = true;
            $output['view'] = $rendered_view;

            return json_encode($output);
        }else{

            return $rendered_view;
        }
    }

    public function getChannelsByMerchant(Request $request, $merchant_id){
        $merchantChannels = json_decode($this->getGuzzleClient($request->all(), 'channels/merchant/'.$merchant_id)->getBody()->getContents());

        return response()->json($merchantChannels);
    }

    public function getChannelsByMerchantAndType($merchant_id, $chnlTypeId){
        $merchantChannels = json_decode($this->getGuzzleClient(array(), 'channels/merchant/'.$merchant_id.'/channel_type/'.$chnlTypeId)->getBody()->getContents());

        return response()->json($merchantChannels);
    }

    public function processCFCategories($categories, $channel_type_id) {
        $delimiter = ($channel_type_id == 7) ?  ':' : '/';
        $data = array();

        foreach ($categories as $cat) {
            $levels = explode($delimiter, $cat);

            for ($i = 0; $i < count($levels); $i++) {
                $thisLevel = implode($delimiter, array_slice($levels, 0, $i + 1));

                if (!in_array($thisLevel, $data)) {
                    $data[] = $thisLevel;
                }
            }
        }

        sort($data);
        array_unshift($data, 'All');
        return $data;
    }

    public function getCF($channel_id)
    {
        $cf = json_decode($this->getGuzzleClient(array(), 'custom_fields/channel/'.$channel_id)->getBody()->getContents());

        return response()->json($cf);
    }

    // create or update custom field
    public function updateCF(Request $request, $channel_id) {
        $cf = json_decode($this->postGuzzleClient($request->all(), 'custom_fields/channel/'.$channel_id.'/updateCF')->getBody()->getContents());
        return response()->json($cf);
    }

    public function deleteCF($channel_id) {
        $input = \Input::all();

        $cf = json_decode($this->postGuzzleClient($input, 'custom_fields/channel/'.$channel_id.'/deleteCF')->getBody()->getContents());

        return response()->json($cf);
    }

    public function getOrder($channel_type_id, $order_code)
    {
        $response = json_decode($this->getGuzzleClient(array(), 'orders/getorder/'.$channel_type_id.'/'.$order_code)->getBody()->getContents(), true);
        if(isset($response['items'])){
		for($i = 0; $i < count($response['items']); $i++){
            		$itemDetails = $this->getGuzzleClient(['tp_ref_id' => $response['items'][$i]['channel_sku_ref_id']], 'admin/products/get_tp_item_details')->getBody()->getContents();
            		$response['items'][$i]['sku'] = $itemDetails;
        	}
	}
        return response()->json($response);
    }

    private function getSyncHistoryChannels($channelId) {
        $data = $this->admin->is('clientadmin|clientuser') ? array('merchant_id' => $this->admin->merchant_id) : array();
        $data['channel_id'] = $channelId;

        $response = json_decode($this->getGuzzleClient($data, 'channels/channel')->getBody()->getContents());
        $channels = array();
        foreach ($response as $channel) {
            if ($channel->channel_type->third_party == 1) {
                $channels[$channel->id] = $channel->name;
            }
        }

        if (empty($channels) || count($channels) == 0) {
            $channels[''] = 'No Channels Found';
        }

        reset($channels);
        $default = !empty(request()->get('channel', '')) ? request()->get('channel') : key($channels);

        return array('channels' => $channels, 'default_channel' => $default);
    }

    private function getSyncStatuses() {
        $syncStatuses = array(
            'SUCCESS'       => 'SUCCESS',
            'QUEUED'        => 'QUEUED',
            'SENT'          => 'SENT',
            'PROCESSING'    => 'PROCESSING',
            'RETRY'         => 'RETRY',
            'FAILED'        => 'FAILED',
            'ERROR'         => 'ERROR',
            'NEW'           => 'NEW',
            'CANCELLED'     => 'CANCELLED',
        );

        asort($syncStatuses);
        return $syncStatuses;
    }

    public function syncHistoryIndex() {
        $channelId = request()->route('channel_id', null);
        $data = $this->getSyncHistoryChannels($channelId);

        return view('admin.channels.sync_history', array(
            'channels'          => $data['channels'],
            'default_channel'   => $data['default_channel'],
            'statuses'          => $this->getSyncStatuses(),
            'archived'          => false,
            'admin'             => $this->admin,
            )
        );
    }

    public function syncArchiveIndex() {
        $channelId = request()->route('channel_id', null);
        $data = $this->getSyncHistoryChannels($channelId);

        return view('admin.channels.sync_history', array(
            'channels'          => $data['channels'],
            'default_channel'   => $data['default_channel'],
            'statuses'          => $this->getSyncStatuses(),
            'archived'          => true));
    }

    public function getSyncHistory(Request $request) {
        $data['start'] = $request->input('start');
        $data['length'] = $request->input('length');
        $data['draw'] = $request->input('draw');
        $data['order'] = $request->input('order');
        $data['columns'] = $request->input('columns');
        $data['search'] = $request->input('search_data');
        $data['archived'] = $request->input('archived', false);

        $data['channel_id'] = $request->input('channel');

        if($this->admin->is('clientadmin|clientuser')) {
             $data['merchant_id'] = $this->admin->merchant_id;
        }

        $syncHistory = json_decode($this->getGuzzleClient($data, 'channels/sync_history')->getBody()->getContents(), true);

        $data['recordsTotal'] = $data['recordsFiltered'] = $syncHistory['total'];
        $syncHistory = $syncHistory['sync_history'];
        // $showRemarkStatuses = array('FAILED', 'ERROR', 'CANCELLED');

        foreach ($syncHistory as $key => $sync) {
            if (!$data['archived']) {
                $actions = '';
                if ($this->admin->can('edit.channel')) {
                    if (strcasecmp($sync['status'], 'FAILED') == 0 || strcasecmp($sync['status'], 'ERROR') == 0) {
                        $actions .= '<button type="button" class="restock btn btn-link no-padding btn-retry" onclick="process(this)" data-url="' . route('admin.channels.sync_history.retry', $sync['id']) . '">' . trans('admin/channels.button_retry') . '</button>';
                        $actions .= ' | ';
                        $actions .= '<button type="button" class="restock btn btn-link no-padding btn-cancel" onclick="process(this)" data-url="' . route('admin.channels.sync_history.cancel', $sync['id']) . '">' . trans('admin/channels.button_cancel') . '</button>';
                        $syncHistory[$key]['checkbox'] = \Form::checkbox('sync', $sync['id']);
                    }
                    else if (strcasecmp($sync['status'], 'CANCELLED') == 0) {
                        $actions .= '<button type="button" class="restock btn btn-link no-padding btn-retry" onclick="process(this)" data-url="' . route('admin.channels.sync_history.retry', $sync['id']) . '">' . trans('admin/channels.button_retry') . '</button>';
                        $syncHistory[$key]['checkbox'] = \Form::checkbox('sync', $sync['id']);
                    }
                    else if (strcasecmp($sync['status'], 'RETRY') == 0) {
                        $actions .= '<button type="button" class="restock btn btn-link no-padding btn-cancel" onclick="process(this)" data-url="' . route('admin.channels.sync_history.cancel', $sync['id']) . '">' . trans('admin/channels.button_cancel') . '</button>';
                        $syncHistory[$key]['checkbox'] = \Form::checkbox('sync', $sync['id']);
                    }else{
                        $syncHistory[$key]['checkbox'] = \Form::checkbox('sync', $sync['id'], false, array('disabled'));
                    }
                }
                if($this->admin->can('edit.channelproduct')){
                    if ($this->admin->is('channelmanager')){
                        $route = 'byChannel.channels.inventory.edit';
                        $routeParams = [$data['channel_id'], $sync['product_id']];
                    }else{
                        $route = 'channels.inventory.edit';
                        $routeParams = [$sync['product_id']];
                    }
                    $syncHistory[$key]['product_id'] = '<a href="'.route($route, $routeParams).'?channel='.$data['channel_id'].'" target="_blank">'.$sync['product_id'].'</a>';
                }
                $syncHistory[$key]['actions'] = $actions;
            }

            // if (!in_array($sync['status'], $showRemarkStatuses)) {
            //     $syncHistory[$key]['remarks'] = '';
            // }
        }

        $data['data'] = $syncHistory;

        return json_encode($data);
    }

    public function retrySync($syncId) {
        $response = $this->postGuzzleClient(array(), 'channels/sync_history/retry/' . $syncId);

        if ($response->getStatusCode() != 200) {
            $return['success'] = false;
            $return['message'] = 'Failed to update sync status. Please try again later.';

            return $return;
        }

        $response = json_decode($response->getBody()->getContents(), true);

        $return['success'] = $response['success'];
        $return['message'] = $response['message'];
        $return['new_status'] = 'RETRY';
        $return['button'] = '<button type="button" class="restock btn btn-link no-padding btn-cancel" onclick="process(this)" data-url="' . route('admin.channels.sync_history.cancel', $syncId) . '">' . trans('admin/channels.button_cancel') . '</button>';

        return $return;
    }

    public function cancelSync($syncId) {
        $response = $this->postGuzzleClient(array(), 'channels/sync_history/cancel/' . $syncId);

        if ($response->getStatusCode() != 200) {
            $return['success'] = false;
            $return['message'] = 'Failed to update sync status. Please try again later.';

            return $return;
        }

        $response = json_decode($response->getBody()->getContents(), true);

        $return['success'] = $response['success'];
        $return['message'] = $response['message'];
        $return['new_status'] = 'CANCELLED';
        $return['button'] = '<button type="button" class="restock btn btn-link no-padding btn-retry" onclick="process(this)" data-url="' . route('admin.channels.sync_history.retry', $syncId) . '">' . trans('admin/channels.button_retry') . '</button>';

        return $return;
    }

    public function registerWebhooks($channelId) {
        $response = $this->postGuzzleClient(array(), 'webhook/register/' . $channelId);

        if ($response->getStatusCode() != 200) {
            $return['success'] = false;
            $return['message'] = 'Failed to register webhooks. Please try again later.';

            return $return;
        }

        $response = json_decode($response->getBody()->getContents(), true);

        $return['success'] = $response['success'];
        $return['message'] = json_encode($response['message']);

        return $return;
    }

    public function importStoreCategories($channelId) {
        $response = $this->postGuzzleClient(array(), 'thirdparty/import_store_categories/' . $channelId);

        if ($response->getStatusCode() != 200) {
            $return['success'] = false;
            $return['message'] = 'Failed to import store categories. Please try again later.';

            return $return;
        }

        $response = json_decode($response->getBody()->getContents(), true);

        $return['success'] = $response['success'];
        $return['message'] = json_encode($response['message']);

        return $return;
    }

    public function setStorefrontApi($id){
        $hapiResponse = json_decode($this->getGuzzleClient(array(), 'channels/channel/' . $id . '/get-storefrontapi')->getBody()->getContents());

        $response = array(
            'success'       => true,
            'storefront'    => $hapiResponse,
        );

        return response()->json($response);
    }

    public function bulkUpdateSyncs(Request $request)
    {
        $syncIds = $request->get('sync-ids');
        $action = $request->get('action');

        $hapiResponse = json_decode($this->postGuzzleClient($request->all(), 'channels/sync_history/bulk-update')->getBody()->getContents());

        if (isset($hapiResponse->error)) {
            $errorMsg = $hapiResponse->error;
            flash()->error($errorMsg);
        }else{
            $message = 'Successfully '.($action == 'retry' ? 'retried' : 'cancelled').' '.count(explode(',', $syncIds)).' sync(s).';
            flash()->success($message);
        }

        return back()->withInput();
    }

    public function getShippingProvider($id)
    {
        //$this->update($request, $id, true);
        $channel = json_decode($this->getGuzzleClient(array(), 'channels/channel/'.$id)->getBody()->getContents());

        $response = json_decode($this->postGuzzleClient($channel, 'channels/getShippingProvider')->getBody()->getContents());
        return response()->json($response);
    }

}
