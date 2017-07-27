@extends('layouts.master')

@section('title')
    @lang('admin/channels.page_title_channel_type_create')
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
                        <h3 class="box-title">@lang('admin/channels.box_header_channel_type_create')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body channels channel-page channel-create">
                        <!-- Nav tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('admin/channels.tab_title_details')</a></li>
                                <li role="presentation"><a href="#shippingRate" aria-controls="shippingRate" role="tab" data-toggle="tab">Shipping Rate</a></li>
                            </ul>
                            {!! Form::open(array('url' => route('admin.channel-type.store'), 'method' => 'POST')) !!}
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active clearfix" id="details">
                                        <div class="col-xs-12">
                                            <div class="col-xs-7">
                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-4 control-label required" for="name">@lang('admin/channels.channel_type_form_label_name')</label>
                                                    <div class="col-xs-8">
                                                        {!! Form::text( 'name', null, ['class' => 'form-control', 'placeholder' => trans('admin/channels.channel_type_form_placeholder_name')] ) !!}
                                                        <div class="error">{{ $errors->first('name') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-4 control-label required" for="status">@lang('admin/channels.channel_form_label_status')</label>
                                                    <div class="col-xs-8">
                                                        {!! Form::select('status', $status, null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('admin/channels.channel_form_placeholder_status'))) !!}
                                                        <div class="error">{{ $errors->first('status') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-4 control-label required" for="status">@lang('admin/channels.channel_type_form_label_type')</label>
                                                    <div class="col-xs-8">
                                                        {!! Form::select('type', $types, null, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('admin/channels.channel_type_form_placeholder_type'))) !!}
                                                        <div class="error">{{ $errors->first('type') }}</div>
                                                    </div>
                                                </div>

                                                <div class="form-group has-feedback">
                                                    <label class="col-xs-4 control-label" for="status">@lang('admin/channels.channel_type_form_label_manual_order')</label>
                                                    <div class="col-xs-8">
                                                        {!! Form::hidden('manual_order', false) !!}
                                                        {!! Form::checkbox('manual_order', true, true) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12">
                                            <div class="col-xs-12">
                                                <fieldset id="custom-fields">
                                                    <div class="col-xs-12">
                                                        <label class="control-label" for="custom_field[]"><u>@lang('admin/channels.channel_type_form_label_fields')</u></label>
                                                    </div>
                                                    <div>
                                                        <table id="custom_fields_table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="col-xs-3">@lang('admin/channels.channel_type_table_header_field_name')</th>
                                                                    <th class="col-xs-2">@lang('admin/channels.channel_type_table_header_api_field')</th>
                                                                    <th class="col-xs-3">@lang('admin/channels.channel_type_table_header_desc')</th>
                                                                    <th class="col-xs-2">@lang('admin/channels.channel_type_table_header_default_value')</th>
                                                                    <th class="col-xs-1 text-center">@lang('admin/channels.channel_type_table_header_required')</th>
                                                                    <th class="col-xs-1"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="col-xs-3">{!! Form::text("custom_field_name[1]", "", ["class" => "form-control", "placeholder" => trans("admin/channels.channel_type_form_placeholder_field")]) !!}</td>
                                                                    <td class="col-xs-2">{!! Form::text("custom_field_api[1]", "", ["class" => "form-control", "placeholder" => trans("admin/channels.channel_type_form_placeholder_api_field")]) !!}</td>
                                                                    <td class="col-xs-3">{!! Form::textarea("custom_field_desc[1]", "", ["class" => "form-control slim-textarea", "placeholder" => trans("admin/channels.channel_type_form_placeholder_desc")]) !!}</td>
                                                                    <td class="col-xs-2">{!! Form::text("custom_field_default[1]", "", ["class" => "form-control", "placeholder" => trans("admin/channels.channel_type_form_placeholder_default_value")]) !!}</td>
                                                                    <td class="text-center col-xs-1">{!! Form::checkbox("custom_field_required[1]", 1) !!}</td>
                                                                    <td class="text-center col-xs-1"></td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="col-xs-3"></td>
                                                                    <td class="col-xs-2"></td>
                                                                    <td class="col-xs-3"></td>
                                                                    <td class="col-xs-2"></td>
                                                                    <td class="col-xs-1"></td>
                                                                    <td class="col-xs-1">
                                                                        <button id="btn-add" class="pull-right" tittle="Add&hellip;" type="button">
                                                                            <i class="fa fa-2x fa-plus-circle" aria-hidden="true"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane clearfix" id="shippingRate">
                                        <div class="col-xs-12">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label class="col-xs-4 control-label" for="status">@lang('admin/channels.channel_type_form_label_shipping_fee')</label>
                                                    <table id="custom_fields_table">
                                                    <tr>
                                                        <td align="center"><u><b>&nbsp;&nbsp;Region&nbsp;&nbsp;</b></u></td>
                                                        <td align="center"><u><b>&nbsp;&nbsp;Location&nbsp;&nbsp;</b></u></td>
                                                        <td align="center"><u><b>&nbsp;&nbsp;Shipping Fee&nbsp;&nbsp;</b></u></td>
                                                    </tr>
                                                    <tr id="shipping-rate-0">
                                                        <td class="col-xs-3">
                                                            {!! Form::text('region[0]', null, array('class' => 'form-control text', 'placeholder' => trans('admin/channels.channel_type_form_placeholder_region'))) !!}
                                                            <div class="error">{{ $errors->first('region') }}</div>
                                                        </td>
                                                        <td class="col-xs-3">
                                                            {!! Form::select('location[0]', $location, null, array('class' => 'form-control select2', 'style' => 'width:100%')) !!}
                                                            <div class="error">{{ $errors->first('location') }}</div>
                                                        </td>
                                                        <td class="col-xs-6">
                                                            <table>
                                                                <tr align="center">
                                                                    <th>
                                                                        RM&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! Form::number('base_amount[0]', 0, [ 'id' => 'base_amount', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;for the first&nbsp;&nbsp; 
                                                                    </th>
                                                                    <th>
                                                                        {!! Form::number('base_grams[0]', 0, [ 'id' => 'base_grams', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
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
                                                                        {!! Form::number('increment_amount[0]', 0, [ 'id' => 'increment_amount', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;for each&nbsp;&nbsp;
                                                                    </th>
                                                                    <th>
                                                                        {!! Form::number('increment_grams[0]', 0, [ 'id' => 'increment_grams', 'min' => '0', 'step' => 'any', 'style' => 'margin-top:2px; text-align:center; width:70px'] ) !!}
                                                                    </th>
                                                                    <th>
                                                                        &nbsp;&nbsp;grams after  base weight
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td class="text-center col-xs-1">
                                                            <button id="rate-delete" onclick = "delete_rate(0)" class="pull-right" tittle="Add&hellip;" type="button"><i class="fa fa-2x fa-times-circle" aria-hidden="true"></i>
                                                            </button>
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
                                   <button type="submit" id="btn_create_new_channel" class="btn btn-default">@lang('admin/channels.button_add_new_channel_type')</button>
                                </div> <!-- / .form-actions -->
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<script type="text/javascript">
    $(document).ready(function(){
       
        var rowIndex = 2;
        $('#btn-add').click(function(){
            var row = '<tr>'
                        + '<td class="col-xs-3"><input class="form-control" placeholder="@lang("admin/channels.channel_type_form_placeholder_field")" name="custom_field_name[' + rowIndex + ']" type="text"></td>'
                        + '<td class="col-xs-2"><input class="form-control" placeholder="@lang("admin/channels.channel_type_form_placeholder_api_field")" name="custom_field_api[' + rowIndex + ']" type="text"></td>'
                        + '<td class="col-xs-3"><textarea class="form-control slim-textarea" placeholder="@lang("admin/channels.channel_type_form_placeholder_desc")" name="custom_field_desc[' + rowIndex + ']"></textarea></td>'
                        + '<td class="col-xs-2"><input class="form-control" placeholder="@lang("admin/channels.channel_type_form_placeholder_default_value")" name="custom_field_default[' + rowIndex + ']" type="text"></td>'
                        + '<td class="text-center col-xs-1"><input name="custom_field_required[' + rowIndex + ']" type="checkbox" value="1"></td>'
                        + '<td class="text-center col-xs-1 dismiss-custom-field"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></td>'
                    + '</tr>';

            $(this).closest('tr').before(row);
            rowIndex++;
        });

        var rateRowIndex = 1;
        var locationOption = '{!! $locationOption !!}';
        $('#rate-add').click(function(){
            var rateRow = '<tr id="shipping-rate-' + rateRowIndex + '">'
                        + '<td class="col-xs-3"><input class="form-control text" placeholder="@lang("admin/channels.channel_type_form_placeholder_region")" name="region[' + rateRowIndex + ']" type="text"><div class="error">{{ $errors->first("region") }}</div></td>'
                        + '<td class="col-xs-3"><select class="form-control select2" placeholder="@lang("admin/channels.channel_type_form_placeholder_region")" name="location[' + rateRowIndex + ']" style="width:100%">'+locationOption+'</select><div class="error">{{ $errors->first("location") }}</div></td>'
                        + '<td class="col-xs-6"><table><tr align="center"><th>RM&nbsp;&nbsp;</th><th><input id="base_amount" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="base_amount[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;for the first&nbsp;&nbsp;</th><th><input id="base_grams" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="base_grams[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;grams.</th></tr><tr align="center"><th>RM&nbsp;&nbsp;</th><th><input id="increment_amount" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="increment_amount[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;for each&nbsp;&nbsp;</th><th><input id="increment_grams" min="0" step="any" style="margin-top:2px; text-align:center; width:70px" name="increment_grams[' + rateRowIndex + ']" type="number" value="0"></th><th>&nbsp;&nbsp;grams after  base weight</th></tr></table></td>'
                        + '<td class="text-center col-xs-1"><button id="rate-delete" onclick = "delete_rate(' + rateRowIndex + ')" class="pull-right" tittle="Add&hellip;" type="button"><i class="fa fa-2x fa-times-circle" aria-hidden="true"></i></button></td>'
                    + '</tr>';

            $(this).closest('tr').before(rateRow);
            $('#shipping-rate-'+rateRowIndex+' .select2').val('').trigger('change');
            $('#shipping-rate-'+rateRowIndex+' .select2').select2({
                allowClear: true,
                placeholder: "@lang('admin/channels.channel_type_form_placeholder_location')",
                tags: true,
                closeOnSelect: true,
            });
            rateRowIndex++;
        });

        $("#shipping-rate-0 .select2").val('').trigger('change');
        $("#shipping-rate-0 .select2").select2({
            allowClear: true,
            placeholder: "@lang('admin/channels.channel_type_form_placeholder_location')",
            tags: true,
            closeOnSelect: true,
        });

        $('#custom-fields').on('click','.close',function(){
            $(this).closest('tr').remove();
        });
    });
    
    function delete_rate(num) {
        $('#shipping-rate-'+num+' .text').val('').trigger('change');
        $('#shipping-rate-'+num).addClass('hide');
    }
</script>
@append