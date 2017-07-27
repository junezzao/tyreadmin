@extends('layouts.master')

@section('title')
    @lang('admin/channels.page_title_channel_create')
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
                        <h3 class="box-title">@lang('admin/channels.box_header_channel_create')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body channels channel-page channel-create">
                        <!-- Nav tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_details')</a></li>
                                <li role="presentation"><a href="#merchants" aria-controls="merchants" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_merchants')</a></li>
                                <li role="presentation"><a href="#shippingRate" aria-controls="shippingRate" role="tab" data-toggle="tab">Shipping Rate</a></li>
                            </ul>
                            {!! Form::open(array('url' => route('admin.channels.store'), 'method' => 'POST', 'id' => 'create-channel-form')) !!}
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                        <div class="col-xs-12">
                                            <div class="col-xs-6">
                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="name">@lang('admin/channels.channel_form_label_name')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'name', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_name')] ) !!}
                                                        <div class="error">{{ $errors->first('name') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="channel_type_id">@lang('admin/channels.channel_form_label_channel_type')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::select('channel_type_id', $channel_types, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/channels.channel_form_placeholder_channel_type'))) !!}
                                                        <div class="error">{{ $errors->first('channel_type_id') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 required" for="address">@lang('admin/channels.channel_form_label_address')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::textarea( 'address', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_address')] ) !!}
                                                        <div class="error">{{ $errors->first('address') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="website_url">@lang('admin/channels.channel_form_label_website_url')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'website_url', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_website_url')] ) !!}
                                                        <div class="error">{{ $errors->first('website_url') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label" for="api_key">@lang('admin/channels.channel_form_label_api_key')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'api_key', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_api_key')] ) !!}
                                                        <div class="error">{{ $errors->first('api_key') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label" for="api_secret">@lang('admin/channels.channel_form_label_api_secret')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'api_secret', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_api_secret')] ) !!}
                                                        <div class="error">{{ $errors->first('api_secret') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label" for="api_password">@lang('admin/channels.channel_form_label_api_password')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'api_password', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_api_password')] ) !!}
                                                        <div class="error">{{ $errors->first('api_password') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="money_flow">@lang('admin/channels.channel_form_label_money_flow')</label>
                                                    <div class="col-xs-9">
                                                        <label class="nobold-label">
                                                           {!! Form::radio('money_flow', 'FMHW', 1) !!}&nbsp;&nbsp;FMHW&nbsp;&nbsp;&nbsp;&nbsp;
                                                           {!! Form::radio('money_flow', 'Merchant') !!}&nbsp;&nbsp;Merchant
                                                        </label>
                                                        <div class="error">{{ $errors->first('money_flow') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="sale_amount">@lang('admin/channels.channel_form_label_sale_amount_from')</label>
                                                    <div class="col-xs-9">
                                                        <label class="nobold-label">
                                                           {!! Form::radio('sale_amount', 1, 1) !!}&nbsp;&nbsp;Listing Price&nbsp;&nbsp;&nbsp;&nbsp;
                                                           {!! Form::radio('sale_amount', 0) !!}&nbsp;&nbsp;Sold Price
                                                        </label>
                                                        <div class="error">{{ $errors->first('sale_amount') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="picking_manifest">@lang('admin/channels.channel_form_label_picking_manifest')</label>
                                                    <div class="col-xs-9">
                                                        <label class="nobold-label">
                                                           {!! Form::radio('picking_manifest', 1, 1) !!}&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;
                                                           {!! Form::radio('picking_manifest', 0) !!}&nbsp;&nbsp;No
                                                        </label>
                                                        <div class="error">{{ $errors->first('picking_manifest') }}</div>
                                                    </div>
                                                </div>

                                                <fieldset class="custom-fields">
                                                </fieldset>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="support_email">@lang('admin/channels.channel_form_label_support_email')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'support_email', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_support_email')] ) !!}
                                                        <div class="error">{{ $errors->first('support_email') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="noreply_email">@lang('admin/channels.channel_form_label_noreply_email')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'noreply_email', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_noreply_email')] ) !!}
                                                        <div class="error">{{ $errors->first('noreply_email') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="finance_email">@lang('admin/channels.channel_form_label_finance_email')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'finance_email', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_finance_email')] ) !!}
                                                        <div class="error">{{ $errors->first('finance_email') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="marketing_email">@lang('admin/channels.channel_form_label_marketing_email')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::text( 'marketing_email', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_form_placeholder_marketing_email')] ) !!}
                                                        <div class="error">{{ $errors->first('marketing_email') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="currency">@lang('admin/channels.channel_form_label_currency')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::select('currency', $currencies, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/channels.channel_form_placeholder_currency'))) !!}
                                                        <div class="error">{{ $errors->first('currency') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="timezone">@lang('admin/channels.channel_form_label_timezone')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::select('timezone', $timezones, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/channels.channel_form_placeholder_timezone'))) !!}
                                                        <div class="error">{{ $errors->first('timezone') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="status">@lang('admin/channels.channel_form_label_status')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::select('status', $status, null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('admin/channels.channel_form_placeholder_status'))) !!}
                                                        <div class="error">{{ $errors->first('status') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label required" for="returns-chargable">@lang('admin/channels.channel_form_label_returns_chargable')</label>
                                                    <div class="col-xs-9">
                                                        <label class="nobold-label">
                                                            {!! Form::checkbox('returns_chargable', 1, 0) !!}
                                                            @lang('admin/channels.channel_form_label_returns_chargable_desc')
                                                        </label>
                                                        <div class="error">{{ $errors->first('returns_chargable') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback" id="issuing_company">
                                                    <label class="col-xs-3 control-label required" for="issuing_company">@lang('admin/channels.channel_form_label_issuing_company')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::select('issuing_company', $issuing_companies, null, array('class' => 'form-control select2', 'placeholder' => trans('admin/channels.channel_form_placeholder_issuing_company'))) !!}
                                                        <div class="error">{{ $errors->first('issuing_company') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-3 control-label" for="documents">@lang('admin/channels.channel_form_label_documents')</label>
                                                    <div class="col-xs-9">
                                                        {!! Form::select('docs_to_print[]', $docs_to_print_list, $docs_to_print, array('id' => 'docs-to-print-select', 'class' => 'form-control select2', 'multiple'=>'multiple')) !!}
                                                        <div class="error">{{ $errors->first('docs_to_print') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback" id="shipping_provider">
                                                    <label class="col-xs-3 control-label required" for="shipping_provider">@lang('admin/channels.channel_form_label_shipping_provider')</label>
                                                    <div class="col-xs-9">
                                                       {!! Form::select('shipping_provider', $shipping_provider_list, null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('admin/channels.channel_form_placeholder_shipping_porvider'))) !!}
                                                        <div class="error">{{ $errors->first('shipping_provider') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback" id="shipping_provider_explain">
                                                    <label class="col-xs-3 control-label" for="shipping_provider">@lang('admin/channels.channel_form_label_shipping_provider')</label>
                                                    <div class="col-xs-9">
                                                       <text>Will be auto retrieved after channel is created.</text>
                                                    </div>
                                                </div>
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
                                                <div class="col-xs-2">
                                                    <label class="nobold-label">
                                                        {!! Form::checkbox('filterChecked', 1, 1) !!}
                                                        @lang('admin/channels.channel_form_label_sort_checked')
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
                                    <div role="tabpanel" class="tab-pane clearfix" id="shippingRate">
                                        <div class="col-xs-12">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label class="col-xs-3 control-label" for="status">
                                                        @lang('admin/channels.channel_type_form_label_shipping_rate')
                                                    </label>
                                                    <div class="col-xs-3">
                                                        {!! Form::checkbox('shipping_default', 1, true, array('id' => 'shipping_default')) !!}
                                                    </div>
                                                    <div class="col-xs-6"></div>
                                                    <label class="col-xs-3 control-label" for="status">
                                                        @lang('admin/channels.channel_type_form_label_use_shipping_rate')
                                                    </label>
                                                    <div class="col-xs-3">
                                                        {!! Form::checkbox('use_shipping_rate', 1, true, array('id' => 'use_shipping_rate')) !!}
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
                                                    <tr id="shipping-rate-0">
                                                        <td class="col-xs-3" style="padding-top: 5px;">
                                                            {!! Form::text('region[0]', null, array('class' => 'form-control text', 'id' => 'region[0]', 'oninput' =>  'detect_input()', 'placeholder' => trans('admin/channels.channel_type_form_placeholder_region'))) !!}
                                                            <div class="error">{{ $errors->first('region') }}</div>
                                                        </td>
                                                        <td class="col-xs-3" style="padding-top: 5px;">
                                                            {!! Form::select('location[0]', $location, null, array('class' => 'form-control select2', 'style' => 'width:100%')) !!}
                                                            <div class="error">{{ $errors->first('location') }}</div>
                                                        </td>
                                                        <td class="col-xs-6" style="padding-top: 5px;">
                                                            <table>
                                                                <tr align="center">
                                                                    <th>
                                                                        RM&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! Form::number('base_amount[0]', 0, [ 'id' => 'base_amount', 'oninput' =>  'detect_input()', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;for the first&nbsp;&nbsp; 
                                                                    </th>
                                                                    <th>
                                                                        {!! Form::number('base_grams[0]', 0, [ 'id' => 'base_grams', 'oninput' =>  'detect_input()', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
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
                                                                        {!! Form::number('increment_amount[0]', 0, [ 'id' => 'increment_amount', 'oninput' =>  'detect_input()', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;for each&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! Form::number('increment_grams[0]', 0, [ 'id' => 'increment_grams', 'oninput' =>  'detect_input()', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;grams after  base weight
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </td>  
                                                        <td class="text-center col-xs-1" rowspan="2">
                                                            <button id="rate-delete" onclick = "delete_rate(0)" class="pull-right" tittle="Add&hellip;" type="button"><i class="fa fa-2x fa-times-circle" aria-hidden="true"></i>
                                                            </button>
                                                        </td>    
                                                    </tr>
                                                    <tr id="shipping-merchant-0"  class="box">
                                                        <td class="col-xs-3" style="padding-bottom: 5px;"><b>Merchant</b></td>
                                                        <td class="col-xs-6" colspan="3" style="padding-bottom: 5px;">
                                                            {!! Form::select('shipping_merchant[0][]', $shipping_merchants, null, array('id' => 'shipping_merchant', 'style' => 'width:90%', 'class' => 'select2', 'multiple'=>'multiple')) !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-xs-3"></td>
                                                        <td class="col-xs-3"></td>
                                                        <td class="col-xs-2"></td>
                                                        <td class="col-xs-1">
                                                            <button id="rate-add" class="pull-right" tittle="Add&hellip;" type="button">
                                                            <i class="fa fa-2x fa-plus-circle" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-xs-10 note">
                                                        <i><span class="glyphicon glyphicon-info-sign">&nbsp;</span>All price entered should be exclusive GST.</i>
                                                        <br/>
                                                        <i><span class="glyphicon glyphicon-info-sign">&nbsp;</span>Please uncheck the "Use default shipping rate" and remove all shipping fee rate settings if you do not wish to charge Merchant Shipping Fee for the channel.</i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group pull-right">
                                       <button type="button" id="btn_create_new_channel" class="btn btn-default">@lang('admin/channels.button_add_new_channel')</button>
                                    </div> <!-- / .form-actions -->
                                </div>
                            {!! Form::close() !!}
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
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<script type="text/javascript">
function setWebsiteUrlPlaceholder() {
    if ($("select[name='channel_type_id']").find("option:selected").text() == 'Shopify') {
        $('input[name=website_url]').attr('placeholder', '{{ trans("admin/channels.channel_form_placeholder_shopify_url") }}');
    }
    else {
        $('input[name=website_url]').attr('placeholder', '{{ trans("admin/channels.channel_form_placeholder_website_url") }}');
    }
}

    $(document).ready(function() {
        setWebsiteUrlPlaceholder();

        // for merchant pagination
        var channel_merchant = [];
        var merchants = [];
        @foreach($merchants as $merchant)
            merchants[{{$merchant->id}}] = {'id':{{$merchant->id}}, 'name': '{{$merchant->name}}'};
        @endforeach

        $('input[name="filterName"]').prop( "checked", true );
        $('input[name="filterChecked"]').prop( "checked", true );

        // to retain checked values
        $('body').on('complete.merchant', function(){
            $('input[name="merchant_id[]"]').change(function() {
                var value = parseInt($(this).val());
                if($(this).is(':checked')){
                    if($.inArray(value, selectedMerchants) == -1){
                        selectedMerchants.push(value);
                    }else{
                        // do nothing
                    }
                }else{
                    if($.inArray(value, selectedMerchants) != -1){
                        var index = selectedMerchants.indexOf(value);
                        selectedMerchants.splice(index, 1);
                    }else{
                        // do nothing
                    }
                }
            });
        });

        // Generate the merchant list
        drawMerchantList(merchants, channel_merchant);

        // get selected merchant list on load
        var selectedMerchants = $('input[name="merchant_id[]"]:checked').map(function() {
                return parseInt(this.value);
            }).get();

        // Set filters event
        $('input[name="filterMerchants"]').on('keyup', function(){
            var filterName = $('input[name="filterMerchants"]').val();
            drawMerchantList(merchants, selectedMerchants, filterName, $('input[name="filterName"]').is(':checked'),  $('input[name="filterChecked"]').is(':checked'));
        });

        $('input[name="filterName"],input[name="filterChecked"]').change(function() {
            var filterName = $('input[name="filterMerchants"]').val();
            drawMerchantList(merchants, selectedMerchants, filterName, $('input[name="filterName"]').is(':checked'),  $('input[name="filterChecked"]').is(':checked'));
        });
        $("#shipping_provider_explain").hide();

        $("select[name='channel_type_id']").change(function () {
            if($(this).find("option:selected").text()== 'Warehouse')
            {
                $label = $('label[for="issuing_company"]').removeClass( "required" );
                $("#issuing_company").hide();
            }else{
                $label = $('label[for="issuing_company"]').addClass( "required" );
                $("#issuing_company").show();
            }

            if ($(this).find("option:selected").text() == 'Lazada' || $(this).find("option:selected").text() == 'Zalora' || $(this).find("option:selected").text() == 'LazadaSC') {
                $("#shipping_provider").hide();
                $label = $('label[for="shipping_provider"]').removeClass( "required" );
                $("#shipping_provider_explain").show();
                if ($(this).find("option:selected").text() == 'Zalora') {
                    $("#docs-to-print-select option[value='3']").removeProp("disabled");
                }
            }
            else{
                $("#shipping_provider").show();
                $label = $('label[for="shipping_provider"]').addClass( "required" );
                $("#shipping_provider_explain").hide();
                if ($(this).find("option:selected").text() != 'Zalora') {
                     $("#docs-to-print-select option[value='3']").prop("disabled", "disabled");
                }
            }

            setWebsiteUrlPlaceholder();

            $('#docs-to-print-select').val('').trigger('change');
            $("#docs-to-print-select").select2();

            if($(this).val() != ''){
                waitingDialog.show('Getting channel type fields...', {dialogSize: 'sm'});
                $.ajax({
                    'url': "/admin/channels/"+$(this).val()+"/channel_type_fields",
                    'method': 'GET',
                    'dataType': 'json',
                    'success': function(response){
                        if(response.success == true){
                            $('.custom-fields').html(response.view);
                        }
                    },
                    complete: function(){
                        waitingDialog.hide();
                    }
                });
            }
            else{
                $('.custom-fields').html('');
            }
        });
        $("select[name='channel_type_id']").trigger('change');
        $('#btn_create_new_channel').on('click', function(e){
            $error_flag = false;
            e.preventDefault();
            $.each($('.custom-fields-div'), function(index, value){
                //console.log($(this).children('input[name="field_value[]"]').val());
                if($(this).children('input[name="field_required[]"]').val() == 1){
                    if($(this).children('input[name="field_default[]"]').val().trim() == '' && $(this).children('input[name="field_value[]"]').val().trim() == ''){
                        $error_flag = true;
                        $(this).children('.error').html('The ' + $(this).children('input[name="field_label[]"]').val() + ' field is required.');
                    }else{
                        $(this).children('.error').html('');
                    }
                }
            });

            $.each($('.required'), function(index, value){
                if($(this).next().children('input, textarea, select').val() == ''){
                    $error_flag = true;
                    $(this).next().children('.error').html('The ' + $(this).text() + ' field is required.');
                }else{
                    $(this).next().children('.error').html('');
                }
            });
            if($error_flag){
                return false;
            }else{
                drawMerchantList(merchants, selectedMerchants);
                $('#create-channel-form').submit();
            }
        });

        var rateRowIndex = 1;
        var locationOption = '{!! $locationOption !!}';
        var merchantOption = '{!! $merchantOption !!}';
        $('#rate-add').click(function(){
            var rateRow = '<tr id="shipping-rate-' + rateRowIndex + '">'
                        + '<td class="col-xs-3"><input class="form-control text" placeholder="@lang("admin/channels.channel_type_form_placeholder_region")" name="region[' + rateRowIndex + ']" type="text"><div class="error">{{ $errors->first("region") }}</div></td>'
                        + '<td class="col-xs-3"><select class="form-control select2" placeholder="@lang("admin/channels.channel_type_form_placeholder_region")" name="location[' + rateRowIndex + ']" style="width:100%">'+locationOption+'</select><div class="error">{{ $errors->first("location") }}</div></td>'
                        + '<td class="col-xs-6"><table><tr align="center"><th>RM&nbsp;&nbsp;</th><th><input id="base_amount" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="base_amount[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;for the first&nbsp;&nbsp;</th><th><input id="base_grams" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="base_grams[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;grams.</th></tr><tr align="center"><th>RM&nbsp;&nbsp;</th><th><input id="increment_amount" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="increment_amount[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;for each&nbsp;&nbsp;</th><th><input id="increment_grams" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="increment_grams[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;grams after  base weight</th></tr></table></td>'
                        + '<td class="text-center col-xs-1" rowspan="2"><button id="rate-delete" onclick = "delete_rate(' + rateRowIndex + ')" class="pull-right" tittle="Add&hellip;" type="button"><i class="fa fa-2x fa-times-circle" aria-hidden="true"></i></button></td>'
                    + '</tr>'
                     + '<tr id="shipping-merchant-' + rateRowIndex + '" class="box">'
                        + '<td class="col-xs-3" style="padding-bottom: 5px;"><b>Merchant</b></td>'
                        + '<td class="col-xs-6" colspan="3" style="padding-bottom: 5px;"><select id="shipping_merchant" style="width:90%" class="select2 " multiple="multiple" name="shipping_merchant[' + rateRowIndex + ']" tabindex="-1" aria-hidden="true">'+merchantOption+'</select></td>'
                    + '</tr>';

            $(this).closest('tr').before(rateRow);
            $('#shipping-rate-'+rateRowIndex+' .select2').val('').trigger('change');
            $('#shipping-rate-'+rateRowIndex+' .select2').select2({
                allowClear: true,
                placeholder: "@lang('admin/channels.channel_type_form_placeholder_location')",
                tags: true,
                closeOnSelect: true,
            });
            $('#shipping-merchant-'+rateRowIndex+' .select2').val('').trigger('change');
            $('#shipping-merchant-'+rateRowIndex+' .select2').select2({
                placeholder: "Select Merchant",
            });
            rateRowIndex++;
            detect_input();
        });

        $("#shipping-rate-0 .select2").val('').trigger('change');
        $("#shipping-rate-0 .select2").select2({
            allowClear: true,
            placeholder: "@lang('admin/channels.channel_type_form_placeholder_location')",
            tags: true,
            closeOnSelect: true,
        });

    });

    //detect the shipping rate input box to change the default 
    function detect_input() {
        $('#shipping_default').attr('checked', false); 
    }
    
    function delete_rate(num) {
        $('#shipping-rate-'+num+' .text').val('').trigger('change');
        $('#shipping-rate-'+num).addClass('hide');
        $('#shipping-merchant-'+num+' .text').val('').trigger('change');
        $('#shipping-merchant-'+num).addClass('hide');
        detect_input();
    }
</script>
@append