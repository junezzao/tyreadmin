@extends('layouts.master')

@section('title')
    @lang('admin/channels.page_title_channel_view')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/channels.content_header_channels')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">@lang('admin/channels.box_header_channel_view')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body channels channel-page channel-show">
                        <!-- Nav tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_details')</a></li>
                                <li role="presentation"><a href="#merchants" aria-controls="merchants" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_merchants')</a></li>
                                @if($channel_types[$channel_type_id] == 'Shopify' or $channel_types[$channel_type_id] == 'Shopify POS')
                                    <li role="presentation"><a href="#webhooks" aria-controls="webhooks" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_webhooks')</a></li>
                                @endif
                                @if($channel_types[$channel_type_id] == 'Lelong')
                                    <li role="presentation"><a href="#store_categories" aria-controls="store_categories" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_store_categories')</a></li>
                                @endif
                                <li role="presentation"><a href="#storefront" aria-controls="storefront" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_storefront_api')</a></li>
                                <li role="presentation"><a href="#shippingRate" aria-controls="shippingRate" role="tab" data-toggle="tab">Shipping Rate</a></li>
                            </ul>

                             <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                    <div class="col-xs-12">
                                        <div class="col-xs-6">
                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="name">@lang('admin/channels.channel_form_label_name')</label>
                                                <div class="col-xs-9">
                                                    {!! $name !!}
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="channel_type_id">@lang('admin/channels.channel_form_label_channel_type')</label>
                                                <div class="col-xs-9">
                                                    {!! $channel_types[$channel_type_id] !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="address">@lang('admin/channels.channel_form_label_address')</label>
                                                <div class="col-xs-9">
                                                    {!! $address !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="website_url">@lang('admin/channels.channel_form_label_website_url')</label>
                                                <div class="col-xs-9">
                                                    {!! $website_url !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="api_key">@lang('admin/channels.channel_form_label_api_key')</label>
                                                <div class="col-xs-9">
                                                    {!! $api_key !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="api_secret">@lang('admin/channels.channel_form_label_api_secret')</label>
                                                <div class="col-xs-9">
                                                    {!! $api_secret !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="api_password">@lang('admin/channels.channel_form_label_api_password')</label>
                                                <div class="col-xs-9">
                                                    {!! $api_password !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="api_password">@lang('admin/channels.channel_form_label_hidden')</label>
                                                <div class="col-xs-9">
                                                    <label class="nobold-label">
                                                        {!! (($hidden) ? 'Yes' : 'No') !!}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="money_flow">@lang('admin/channels.channel_form_label_money_flow')</label>
                                                <div class="col-xs-9">
                                                    <label class="nobold-label">
                                                        {!! $money_flow !!}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="sale_amount">@lang('admin/channels.channel_form_label_sale_amount_from')</label>
                                                <div class="col-xs-9">
                                                    <label class="nobold-label">
                                                        {!! (($sale_amount) ? 'Listing Price' : 'Sold Price') !!}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="picking_manifest">@lang('admin/channels.channel_form_label_picking_manifest')</label>
                                                <div class="col-xs-9">
                                                    <label class="nobold-label">
                                                        {!! (($picking_manifest) ? 'Yes' : 'No') !!}
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="support_email">@lang('admin/channels.channel_form_label_support_email')</label>
                                                <div class="col-xs-9">
                                                    {!! $support_email !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="noreply_email">@lang('admin/channels.channel_form_label_noreply_email')</label>
                                                <div class="col-xs-9">
                                                    {!! $noreply_email !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="finance_email">@lang('admin/channels.channel_form_label_finance_email')</label>
                                                <div class="col-xs-9">
                                                    {!! $finance_email !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="marketing_email">@lang('admin/channels.channel_form_label_marketing_email')</label>
                                                <div class="col-xs-9">
                                                    {!! $marketing_email !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="currency">@lang('admin/channels.channel_form_label_currency')</label>
                                                <div class="col-xs-9">
                                                    {!! !empty($currency) ? $currencies[$currency] : '' !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="timezone">@lang('admin/channels.channel_form_label_timezone')</label>
                                                <div class="col-xs-9">
                                                    {!! !empty($timezone) ? $timezones[$timezone] : '' !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="status">@lang('admin/channels.channel_form_label_status')</label>
                                                <div class="col-xs-9">
                                                    {!! $statuses[$status] !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="returns_chargable">@lang('admin/channels.channel_form_label_returns_chargable')</label>
                                                <div class="col-xs-9">
                                                    <label class="nobold-label">
                                                        {!! (($returns_chargable) ? 'Yes' : 'No') !!}
                                                    </label>
                                                </div>
                                            </div>

                                            @if (!empty($issuing_company) && !empty($issuing_companies))
                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="issuing_company">@lang('admin/channels.channel_form_label_issuing_company')</label>
                                                <div class="col-xs-9">
                                                    {!! $issuing_companies[$issuing_company] !!}
                                                </div>
                                            </div>
                                            @endif


                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="documents">@lang('admin/channels.channel_form_label_documents')</label>
                                                <div class="col-xs-9">
                                                    {!! Form::select('docs_to_print[]', $docs_to_print_list, !empty($docs_to_print)?$docs_to_print:null, array('class' => 'form-control select2', 'multiple'=>'multiple', 'disabled'=>'disabled')) !!}
                                                    <div class="error">{{ $errors->first('docs_to_print') }}</div>
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label class="col-xs-3 control-label" for="documents">@lang('admin/channels.channel_form_label_shipping_provider')</label>
                                                <div class="col-xs-9">
                                                     @if(empty($cod))
                                                        {!! $shipping_provider !!}
                                                    @else
                                                    <div>
                                                        <text>{!! $shipping_provider !!}<br>{{ $cod }}&nbsp;&nbsp;&nbsp;<span class="badge info" style="background-color:orange; color:black;">COD</span></text>
                                                    </div>
                                                    @endif
                                                    <div class="error">{{ $errors->first('shipping_provider') }}</div>
                                                </div>
                                            </div>

                                            <fieldset class="custom-fields">
                                                {!! $custom_fields !!}
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane clearfix" id="merchants">
                                    <div class="col-xs-12">
                                        <div class="form-group has-feedback">
                                            <div class="col-xs-6">
                                                <h4 class="merchant-header">@lang('admin/channels.box_header_channel_merchant')</h4>
                                            </div>
                                            <div class="col-xs-6">
                                                <span class="pull-right">
                                                    <ul id="js-pagination"></ul>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <div class="col-xs-6">
                                                <label class="col-xs-3 control-label" for="filterMerchants">@lang('admin/channels.channel_form_label_filter_name')</label>
                                                <div class="col-xs-8">
                                                    {!! Form::text( 'filterMerchants', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_filter_name')] ) !!}
                                                </div>
                                            </div>
                                            <div class="col-xs-2">
                                                <label class="nobold-label">
                                                    {!! Form::checkbox('filterName', 1, 1) !!}
                                                    @lang('admin/channels.channel_form_label_sort_name')
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <div class="col-xs-6">
                                                <div class="col-xs-7">
                                                    <span class="merchant-label">@lang('admin/channels.channel_form_label_merchant')</span>
                                                </div>
                                                <div class="col-xs-5">
                                                    <span class="merchant-label">@lang('admin/channels.channel_form_label_activate')</span>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="col-xs-7">
                                                    <span class="merchant-label">@lang('admin/channels.channel_form_label_merchant')</span>
                                                </div>
                                                <div class="col-xs-5">
                                                    <span class="merchant-label">@lang('admin/channels.channel_form_label_activate')</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="merchants-list">
                                        </div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane clearfix" id="webhooks">
                                    @if($channel_types[$channel_type_id] == 'Shopify' or $channel_types[$channel_type_id] == 'Shopify POS')
                                        @include('admin.channels.webhooks')
                                    @endif
                                </div>

                                <div role="tabpanel" class="tab-pane clearfix" id="store_categories">
                                    @if($channel_types[$channel_type_id] == 'Lelong')
                                        @include('admin.channels.store_categories')
                                    @endif
                                </div>

                                <div role="tabpanel" class="tab-pane clearfix" id="storefront">
                                    <div class="col-xs-12">
                                        @if(!$storefrontapi)
                                            <div style="text-align: center; padding: 30px; background-color: #f4f4f5">
                                                <h4>
                                                    @lang('admin/channels.string_no_storefront_api')
                                                </h4>
                                                @lang('admin/channels.string_setup_storefront_api')
                                                <br>
                                                @if($user->is('channelmanager'))
                                                    {!!
                                                        link_to_route('byChannel.admin.channels.edit', trans('admin/channels.button_storefront_api'),
                                                        [
                                                            $id,
                                                            'tab' => 'storefront'
                                                        ],
                                                        [
                                                            'class' => 'btn bg-purple margin',
                                                            'role'  => 'button',
                                                        ])
                                                    !!}
                                                @else
                                                    {!!
                                                        link_to_route('admin.channels.edit', trans('admin/channels.button_storefront_api'),
                                                        [
                                                            $id,
                                                            'tab' => 'storefront'
                                                        ],
                                                        [
                                                            'class' => 'btn bg-purple margin',
                                                            'role'  => 'button',
                                                        ])
                                                    !!}
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-xs-6">
                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label" for="name">@lang('admin/channels.channel_form_label_client_id')</label>
                                                    <div class="col-xs-9">
                                                        {{ $storefrontapi_id }}
                                                    </div>
                                                </div>
                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label" for="name">@lang('admin/channels.channel_form_label_client_secret')</label>
                                                    <div class="col-xs-9">
                                                        {{ $storefrontapi_secret }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane clearfix" id="shippingRate">
                                    <div class="col-xs-12">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label class="col-xs-3 control-label" for="status">
                                                    @lang('admin/channels.channel_type_form_label_shipping_rate')
                                                </label>
                                                <div class="col-xs-3">
                                                    {!!  $shipping_default !!}
                                                </div>
                                                <div class="col-xs-6"></div>
                                                <label class="col-xs-3 control-label" for="status">
                                                    @lang('admin/channels.channel_type_form_label_use_shipping_rate')
                                                </label>
                                                <div class="col-xs-3">
                                                    {!!  $use_shipping_rate !!}
                                                </div>
                                            
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-xs-4 control-label" for="status">@lang('admin/channels.channel_type_form_label_shipping_fee')</label>
                                                <table id="custom_fields_table">
                                                    <tr>
                                                        <td align="center"><u><b>&nbsp;&nbsp;Region&nbsp;&nbsp;</b></u></td>
                                                        <td align="center"><u><b>&nbsp;&nbsp;Location&nbsp;&nbsp;</b></u></td>
                                                        <td align="center"><u><b>&nbsp;&nbsp;Shipping Fee&nbsp;&nbsp;</b></u></td>
                                                    </tr>
                                                    @if(!empty($shipping_rate) && count($shipping_rate) > 0)
                                                    <?php $num = 0; ?>
                                                    @foreach($shipping_rate as $key => $shippingRate)
                                                    <tr id="shipping-rate-{{ $num }}">
                                                        <td class="col-xs-3" style="padding-top: 5px;">
                                                            {!! $shippingRate['region'] !!}
                                                            <div class="error">{{ $errors->first('region') }}</div>
                                                        </td>
                                                        <td class="col-xs-3" style="padding-top: 5px;">
                                                            {!! $shippingRate['location']!!}
                                                            <div class="error">{{ $errors->first('location') }}</div>
                                                        </td>
                                                        <td class="col-xs-6" style="padding-top: 5px;">
                                                            <table>
                                                                <tr align="center">
                                                                    <th>
                                                                        RM&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! number_format($shippingRate['base_amount']/1.06, 2) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;for the first&nbsp;&nbsp; 
                                                                    </th>
                                                                    <th>
                                                                        {!! $shippingRate['base_grams'] !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;grams.
                                                                    </th>
                                                                </tr>
                                                                <tr align="center">
                                                                    <th>
                                                                        RM&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! number_format($shippingRate['increment_amount']/1.06, 2) !!}
                                                                    </th>
                                                                    <th align="center">
                                                                        &nbsp;&nbsp;for each&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! $shippingRate['increment_grams'] !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;grams after base weight
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </td>  
                                                        <td class="text-center col-xs-1"></td>    
                                                    </tr>
                                                    <tr id="shipping-merchant-0"  class="box">
                                                        <td class="col-xs-3" style="padding-bottom: 5px;"><b>Merchant</b></td>
                                                        <td class="col-xs-6" colspan="6" style="padding-bottom: 5px;">
                                                            {!! isset($shipping_merchants[$key])?$shipping_merchants[$key]:null !!}
                                                        </td>
                                                    </tr>
                                                    <?php $num++ ?>
                                                    @endforeach
                                                    @else
                                                    <tr id="shipping-rate-0">
                                                        <td class="col-xs-3" style="padding-top: 5px;">
                                                            {!! Form::text('region[0]', null, array('class' => 'form-control', 'id' => 'region[0]', 'oninput' =>  'detect_input()','disabled', 'placeholder' => trans('admin/channels.channel_type_form_placeholder_region'))) !!}
                                                            <div class="error">{{ $errors->first('region') }}</div>
                                                        </td>
                                                        <td class="col-xs-3" style="padding-top: 5px;">
                                                            {!! Form::text('location[0]', null, array('class' => 'form-control select2','disabled', 'style' => 'width:100%')) !!}
                                                            <div class="error">{{ $errors->first('location') }}</div>
                                                        </td>
                                                        <td class="col-xs-6" style="padding-top: 5px;">
                                                            <table>
                                                                <tr align="center">
                                                                    <th>
                                                                        RM&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! 0 !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;for the first&nbsp;&nbsp; 
                                                                    </th>
                                                                    <th>
                                                                        {!! 0 !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;grams.
                                                                    </th>
                                                                </tr>
                                                                <tr align="center">
                                                                    <th>
                                                                        RM&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! 0 !!}
                                                                    </th>
                                                                    <th align="center">
                                                                        &nbsp;&nbsp;for each&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! 0 !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;grams after  base weight
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </td> 
                                                        <td class="text-center col-xs-1"></td>    
                                                    </tr>
                                                    <tr id="shipping-merchant-0"  class="box">
                                                        <td class="col-xs-3" style="padding-bottom: 5px;"><b>Merchant</b></td>
                                                        <td class="col-xs-6" colspan="6" style="padding-bottom: 5px;">
                                                            {!! $shipping_merchants !!}
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-xs-10 note">
                                                    <i><span class="glyphicon glyphicon-info-sign">&nbsp;</span>All price entered should be exclusive GST.</i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group pull-right">
                                @if($user->can('edit.channel'))
                                    @if($user->is('channelmanager'))
                                        <a href="{{route('byChannel.admin.channels.edit', $id)}}">
                                    @else
                                        <a href="{{route('admin.channels.edit', $id)}}">
                                    @endif
                                            <button type="button" class="btn btn-default">@lang('admin/channels.button_edit_channel')</button>
                                        </a>
                                @endif
                            </div> <!-- / .form-actions -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<script src="{{ asset('js/jquery.twbsPagination.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/channel-merchant-table.js', env('HTTPS', false)) }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        // for merchant pagination
        var channel_merchant = [@foreach($channel_merchants as $channel_merchant){{$channel_merchant->id}},@endforeach];
        var merchants = [];
        @foreach($merchants as $merchant)
           @foreach($channel_merchants as $channel_merchant)
                @if($merchant->id == $channel_merchant->id)
                    merchants[{{$merchant->id}}] = {'id':{{$merchant->id}}, 'name': '{{$merchant->name}}'};
                @endif
            @endforeach
        @endforeach

        $('input[name="filterName"]').prop( "checked", true );
        $('input[name="filterChecked"]').prop( "checked", true );

        // Generate the merchant list
        drawMerchantList(merchants, channel_merchant, '', true, true, 10, true);

        // Set filters event
        $('input[name="filterMerchants"]').on('keyup', function(){
            var filterName = $('input[name="filterMerchants"]').val();
            drawMerchantList(merchants, channel_merchant, filterName, $('input[name="filterName"]').is(':checked'), true, 10, true);
        });
        $('input[name="filterName"]').change(function() {
            var filterName = $('input[name="filterMerchants"]').val();
            drawMerchantList(merchants, channel_merchant, filterName, $('input[name="filterName"]').is(':checked'),  $('input[name="filterChecked"]').is(':checked'), 10, true);
        });
    });
</script>
@append