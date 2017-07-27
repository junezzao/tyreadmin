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
                    <div class="box-body">
                        <div class="col-xs-12">
                            <div class="col-xs-6">
                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="name">@lang('admin/channels.channel_form_label_name')</label>
                                    <div class="col-xs-9">
                                        {!! $name !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="channel_type_id">@lang('admin/channels.channel_form_label_channel_type')</label>
                                    <div class="col-xs-9">
                                        {!! $channel_types[$channel_type_id] !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="address">@lang('admin/channels.channel_form_label_address')</label>
                                    <div class="col-xs-9">
                                        {!! $address !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="website_url">@lang('admin/channels.channel_form_label_website_url')</label>
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

                            </div>
                            <div class="col-xs-6">
                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="support_email">@lang('admin/channels.channel_form_label_support_email')</label>
                                    <div class="col-xs-9">
                                        {!! $support_email !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="noreply_email">@lang('admin/channels.channel_form_label_noreply_email')</label>
                                    <div class="col-xs-9">
                                        {!! $noreply_email !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="finance_email">@lang('admin/channels.channel_form_label_finance_email')</label>
                                    <div class="col-xs-9">
                                        {!! $finance_email !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="marketing_email">@lang('admin/channels.channel_form_label_marketing_email')</label>
                                    <div class="col-xs-9">
                                        {!! $marketing_email !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="currency">@lang('admin/channels.channel_form_label_currency')</label>
                                    <div class="col-xs-9">
                                        {!! !empty($currency) ? $currencies[$currency] : '' !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="timezone">@lang('admin/channels.channel_form_label_timezone')</label>
                                    <div class="col-xs-9">
                                        {!! !empty($timezone) ? $timezones[$timezone] : '' !!}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-3 control-label required" for="status">@lang('admin/channels.channel_form_label_status')</label>
                                    <div class="col-xs-9">
                                        {!! $statuses[$status] !!}
                                    </div>
                                </div>

                                <fieldset class="custom-fields">
                                    {!! $custom_fields !!}
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<script src="{{ asset('js/jquery.twbsPagination.min.js',env('HTTPS',false)) }}"></script>
<style type="text/css">
    textarea {
        resize: none;
    }
    .nobold-label{
        font-weight: initial;
    }
</style>