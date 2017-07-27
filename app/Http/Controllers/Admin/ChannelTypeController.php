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

class ChannelTypeController extends AdminController
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:edit.channeltype', ['only' => ['edit', 'update', 'deactivate']]);
        $this->middleware('permission:delete.channeltype', ['only' => ['destroy']]);
        $this->middleware('permission:view.channeltype', ['only' => ['index']]);
        $this->middleware('permission:create.channeltype', ['only' => ['create', 'store']]);

        $this->admin = \Auth::user();
    }

    public function index()
    {
        $data = array();
        $data['user'] = $this->admin;

        return view('admin.channels.channel-type-list', $data);
    }

    public function create()
    {
        $sampleStatus = array(
                'Active'    =>  'Active',
                'Inactive'  =>  'Inactive',
            );
        $data['status'] = $sampleStatus;
        $data['types'] = config('globals.channel_type_type');
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

        return view('admin.channels.channel-type-create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, array(
            'name'                  => 'required|max:255',
            'status'                => 'required',
            'type'                  => 'required',
        ));

        // To build the custom fields
        $custom_field_name_arr = $request->input('custom_field_name', array());
        $custom_field_api_arr = $request->input('custom_field_api', array());
        $custom_field_desc_arr = $request->input('custom_field_desc', array());
        $custom_field_default_arr = $request->input('custom_field_default', array());
        $custom_field_required_arr = $request->input('custom_field_required', array());
        $output = array();
        foreach($custom_field_name_arr as $key => $custom_field)
        {
            if (!empty($custom_field)) {
                $output[] = array(
                    'id'            => $key,
                    'label'         => $custom_field,
                    'api'           => !empty($custom_field_api_arr[$key]) ? $custom_field_api_arr[$key] : "",
                    'description'   => !empty($custom_field_desc_arr[$key]) ? $custom_field_desc_arr[$key] : "",
                    'default'       => !empty($custom_field_default_arr[$key]) ? $custom_field_default_arr[$key] : "",
                    'required'      => !empty($custom_field_required_arr[$key]) ? "1" : "0"
                );
            }
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


        // Prepare POST data
        $postData = array(
            'name'          => $request->input('name'),
            'status'        => $request->input('status'),
            'fields'        => json_encode($output),
            'manual_order'  => $request->input('manual_order'),
            'shipping_rate' => json_encode($shippingRate),
            'type'          => $request->input('type'),
        );
        $response = json_decode($this->postGuzzleClient($postData, 'channels/admin/channel_type')->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Channel type ' . $request->input('name') .' has been successfully created.';

            flash()->success($message);
            return redirect()->route('admin.channel-type.index');
        }else{
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $sampleStatus = array(
                'Active'    =>  'Active',
                'Inactive'  =>  'Inactive',
            );
        $data['status'] = $sampleStatus;
        $response = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$id)->getBody()->getContents());
        $response->fields = json_decode($response->fields, true);
        $response->shipping_rate = json_decode($response->shipping_rate, true);
        $data['channel_type'] = $response;
        $data['channel_type']->shipping_rate = is_array($data['channel_type']->shipping_rate)? $data['channel_type']->shipping_rate : array();

        $cfIds = (!empty($response->fields) && count($response->fields) > 0) ? array_pluck($response->fields, 'id'): array();
        sort($cfIds);
        $data['max_id'] = (count($cfIds) > 0) ? $cfIds[count($cfIds) - 1] : 1;

        $data['manual_order'] = (json_decode($response->manual_order) == 1 ? true : false);
        $data['user'] = $this->admin;

        $data['types'] = config('globals.channel_type_type');
        $data['location']['Region'] = config('globals.malaysia_region');
        $data['location']['State'] = config('globals.malaysia_state');
        $data['location']['Countries'] = config('countries');
        $data['location']['Other']['All'] = 'All';
        $data['location']['Other']['Other'] = 'Other';
        foreach ($data['channel_type']->shipping_rate as $dataFromDB) {
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
        return view('admin.channels.channel-type-edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, array(
            'name'                  => 'required|max:255',
            'status'                => 'required',
        ));

        // To build the custom fields
        $custom_field_name_arr = $request->input('custom_field_name', array());
        $custom_field_api_arr = $request->input('custom_field_api', array());
        $custom_field_desc_arr = $request->input('custom_field_desc', array());
        $custom_field_default_arr = $request->input('custom_field_default', array());
        $custom_field_required_arr = $request->input('custom_field_required', array());
        $output = array();
        foreach($custom_field_name_arr as $key => $custom_field)
        {
            if (!empty($custom_field)) {
                $output[] = array(
                    'id'            => $key,
                    'label'         => $custom_field,
                    'api'           => !empty($custom_field_api_arr[$key]) ? $custom_field_api_arr[$key] : "",
                    'description'   => !empty($custom_field_desc_arr[$key]) ? $custom_field_desc_arr[$key] : "",
                    'default'       => !empty($custom_field_default_arr[$key]) ? $custom_field_default_arr[$key] : "",
                    'required'      => !empty($custom_field_required_arr[$key]) ? "1" : "0"
                );
            }
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
                    'location' => isset($shippingLocation[$key])? $shippingLocation[$key] : null, 
                    'base_amount' => isset($shippingBaseAmount[$key])? $shippingBaseAmount[$key]*1.06 : null,
                    'base_grams' => isset($shippingBaseGrams[$key])? $shippingBaseGrams[$key] : null,
                    'increment_amount' => isset($shippingIcrementAmount[$key])? $shippingIcrementAmount[$key]*1.06 : null,
                    'increment_grams' => isset($shippingIcrementGrams[$key])? $shippingIcrementGrams[$key] : null,
                );
            }
        }

        // Prepare POST data
        $postData = array(
            'name'          => $request->input('name'),
            'status'        => $request->input('status'),
            'fields'        => json_encode($output),
            'manual_order'  => $request->input('manual_order'),
            'shipping_rate' => json_encode($shippingRate),
            'type'          => $request->input('type')
        );
        $response = json_decode($this->putGuzzleClient($postData, 'channels/admin/channel_type/'.$id)->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Channel type ' . $request->input('name') .' has been successfully updated.';

            flash()->success($message);
            return redirect()->route('admin.channel-type.index');
        }else{
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $channel_type = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$id)->getBody()->getContents());
        $response = json_decode($this->deleteGuzzleClient(array(), 'channels/admin/channel_type/'.$id)->getBody()->getContents());
        //dd($response);
        if($response->response === true){
            $message = 'Channel type ' . $channel_type->name .' has been successfully deleted.';
            flash()->success($message);

            return redirect()->route('admin.channel-type.index');
        }elseif(empty($response->error)){
            flash()->error('An error has occured: ' . $response->response);

            return back();
        }else{
            $message = 'Unable to delete ' . $channel_type->name .', please try again.';
            flash()->error($message);

            return redirect()->route('admin.channel-type.index');
        }
    }

    public function getChannelTypesTableData()
    {
        $channel_types = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type')->getBody()->getContents());
        foreach($channel_types as $index => $data)
        {
            $button = '';
            $channel_types[$index]->channel_count = '<a href="'.route('admin.channels.index').'?channel='.urlencode($data->name).'">' . count($data->channels) . '</a>';
            if($data->status == 'Active')
                $statusBtnLabel = 'Deactivate';
            elseif($data->status == 'Inactive')
                $statusBtnLabel = 'Activate';

            $buttons = array();
            if ($this->admin->can('edit.channeltype'))
            {
                $buttons[] = '<a href="'.route('admin.channel-type.edit', [$data->id]).'">Edit</a>';
                $buttons[] = '<a href="'.route('admin.channel-type.deactivate', [$data->id]).'">'.$statusBtnLabel.'</a>';
            }
            if ($this->admin->can('manage.category')
                 && array_key_exists($data->id, config('globals.third_party_categories_applicable'))) // if this channel type has category update API
            {
                $buttons[] = '<a href="'.route('admin.channel-type.categories.edit', [$data->id]).'">Manage Categories</a>';
            }

            $channel_types[$index]->actions = implode(' |', $buttons);
        }

        return json_encode(array("data" => $channel_types));
    }

    public function deactivate($id)
    {
        $channel_type = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$id)->getBody()->getContents());        $statusUpdate = '';
        if($channel_type->status == 'Active'){
            $statusUpdate = 'Inactive';
        }elseif($channel_type->status == 'Inactive'){
            $statusUpdate = 'Active';
        }
        $response = json_decode($this->postGuzzleClient(array('status' => $statusUpdate), 'channels/admin/channel_type/' . $id . '/update_status')->getBody()->getContents());

        if(empty($response->error)){
            $message = 'Channel type ' . $channel_type->name .'\'s status has been successfully updated.';
            flash()->success($message);
        }else{
            $message = 'Unable to update ' . $channel_type->name .', please try again.';
            flash()->error($message);
        }

        return redirect()->route('admin.channel-type.index');
    }

}
