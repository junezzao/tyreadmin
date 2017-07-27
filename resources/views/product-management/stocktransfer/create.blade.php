@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('product-management.page_title_product_mgmt_transfer_create')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="errors"></div>
    <section class="content-header">
      <h1>@lang('product-management.content_header_product_mgmt')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">@lang('product-management.box_header_transfer_create')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        {!! Form::open(array('url' => route('products.stock_transfer.store'), 'method' => 'POST', 'id'=>'form_do')) !!}
                        <div class="col-xs-12">
                            <div class="col-xs-6">
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="do_type">@lang('product-management.transfer_form_label_do_type'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('do_type', $doType, $doSelection, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('product-management.transfer_form_placeholder_do_type'))) !!}
                                        <div class="error">{{ $errors->first('do_type') }}</div>
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="merchant_id">@lang('product-management.transfer_form_label_merchant'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select( 'merchant_id', $merchants, $merchant_id, ['class' => 'form-control select2', 'placeholder' => trans('product-management.transfer_form_placeholder_merchant'), 'id' => 'merchant_dropdown'] ) !!}
                                        <div class="error">{{ $errors->first('merchant_id') }}</div>
                                    </div>
                                </div>

                                <div class="form-group has-feedback" id="ori_channel">
                                    <label class="col-xs-4 control-label required" for="origin_channel">@lang('product-management.transfer_form_label_origin_channel'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('originating_channel_id', $channels, $channel_id, array('class' => 'form-control channel_dropdown select2', 'placeholder' => trans('product-management.transfer_form_placeholder_origin_channel'))) !!}
                                        <div class="error">{{ $errors->first('originating_channel_id') }}</div>
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="target_channel">@lang('product-management.transfer_form_label_target_channel'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('target_channel_id', $channels, null, array('class' => 'form-control channel_dropdown select2', 'placeholder' => trans('product-management.transfer_form_placeholder_target_channel'))) !!}
                                        <div class="error">{{ $errors->first('target_channel_id') }}</div>
                                    </div>
                                </div>

                                <div class="form-group" style="display: none;" id="procurement">
                                    <label for="batch_id" class="col-sm-4 control-label">Procurement Batch</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('batch_id','', array('class'=>'form-control','id'=>'batch_id')) !!}
                                        <div class="type_error text-danger"></div>
                                    </div>
                                </div>

                                <input type="hidden" name="do_status" id="do_status" value=''>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="pic">@lang('product-management.transfer_form_label_pic'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('pic', $users, null, array('class' => 'form-control select2', 'placeholder' => trans('product-management.transfer_form_placeholder_pic'))) !!}
                                        <div class="error">{{ $errors->first('pic') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_remarks'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::textarea( 'remarks', null, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_remarks')] ) !!}
                                        <div class="error">{{ $errors->first('remarks') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="transport_co">@lang('product-management.transfer_form_label_transport_co'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'transport_co', null, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_transport_co')] ) !!}
                                        <div class="error">{{ $errors->first('transport_co') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="lorry_no">@lang('product-management.transfer_form_label_lorry_no'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'lorry_no', null, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_lorry_no')] ) !!}
                                        <div class="error">{{ $errors->first('lorry_no') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="driver_name">@lang('product-management.transfer_form_label_driver_name'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'driver_name', null, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_driver_name')] ) !!}
                                        <div class="error">{{ $errors->first('driver_name') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="driver_id">@lang('product-management.transfer_form_label_driver_id'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'driver_id', null, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_driver_id')] ) !!}
                                        <div class="error">{{ $errors->first('driver_id') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="btngrp-add" class="col-xs-12">
                            <a id="btnAdd" class="stock-transfer-create options btn btn-default">@lang('product-management.button_add')</a>
                            <a id="btnClear" class="stock-transfer-create options btn btn-default">@lang('product-management.button_clear')</a>
                        </div>

                        <div class="col-xs-12" id="upload-div">
                                <div class="form-group pull-right">
                                    <div class="col-xs-6">
                                        <button data-toggle="modal" data-target="#uploadModal" type="button" id="btn_upload_create" class="btn btn-default">@lang('product-management.button_upload_transfer_sheet')</button>
                                    </div>
                                </div>
                            </div>
                        <div class="col-xs-12">
                            <div class="form-group has-feedback">
                                <table id="do_items" width="100%" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            @if(isset($chnlSku['channel']) || !empty(Request::old('channel')))
                                            <th id="channel_col">@lang('product-management.transfer_form_label_channel')</th>
                                            @endif
                                            <th>@lang('product-management.transfer_form_label_system_sku')</th>
                                            <th>@lang('product-management.transfer_form_label_hw_sku')</th>
                                            <th>@lang('product-management.transfer_form_label_prefix')</th>
                                            <th>@lang('product-management.transfer_form_label_product')</th>
                                            <th>@lang('product-management.transfer_form_label_options')</th>
                                            <th>@lang('product-management.transfer_form_label_tags')</th>
                                            <th>@lang('product-management.transfer_form_label_available_qty')</th>
                                            <th>@lang('product-management.transfer_form_label_quantity')</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($chnlSkus) && count($chnlSkus)>0)
                                            @foreach($chnlSkus as $chnlSkuId => $chnlSku)
                                                <tr data-prodid="{{$chnlSku['prodid']}}">
                                                    @if(isset($chnlSku['channel']))
                                                    <td><input type="hidden" name="channel[{{$chnlSkuId}}]" value="{{$chnlSku['channel']}}"> {{$chnlSku['channel']}}</td>
                                                    @endif
                                                    <td>
                                                        <input type="hidden" name="prodid[{{$chnlSkuId}}]" value="{{$chnlSku['prodid']}}">
                                                        <input type="hidden" name="csid[{{$chnlSkuId}}]" value="{{$chnlSkuId}}">
                                                        <input type="hidden" name="options[{{$chnlSkuId}}]" value="{{$chnlSku['options']}}">
                                                        <input type="hidden" name="status[{{$chnlSkuId}}]" value="{{$chnlSku['status']}}">
                                                        <input type="hidden" name="sale_price[{{$chnlSkuId}}]" value="{{$chnlSku['sale_price']}}">
                                                        <input type="hidden" name="price[{{$chnlSkuId}}]" value="{{$chnlSku['price']}}">
                                                        <input type="hidden" name="brand_prefix[{{$chnlSkuId}}]" value="{{$chnlSku['brand_prefix']}}">
                                                        <input type="hidden" name="pname[{{$chnlSkuId}}]" value="{{$chnlSku['pname']}}">
                                                        <input type="hidden" name="skuid[{{$chnlSkuId}}]" value="{{$chnlSku['skuid']}}">
                                                        <input type="hidden" name="tags[{{$chnlSkuId}}]" value="{{$chnlSku['tags']}}">
                                                        <input type="hidden" name="hubwire_sku[{{$chnlSkuId}}]" value="{{$chnlSku['hubwire_sku']}}">
                                                        <input type="hidden" name="quantity[{{$chnlSkuId}}]" value="{{$chnlSku['quantity']}}">
                                                        {{$chnlSku['skuid']}}
                                                    </td>
                                                    <td>{{$chnlSku['hubwire_sku']}}</td>
                                                    <td>{{$chnlSku['brand_prefix']}}</td>
                                                    <td>{{$chnlSku['pname']}}</td>
                                                    <td>{!!$chnlSku['options']!!}</td>
                                                    <td>{{$chnlSku['tags']}}</td>
                                                    <td>{{$chnlSku['quantity']}}</td>
                                                    <td><input class="qty" type="number" max="{{$chnlSku['quantity']}}" value="{{$chnlSku['quantity']}}" name="channel_sku_quantity[{{$chnlSkuId}}]" id="csc{{$chnlSkuId}}"></td>
                                                    <td><i style="cursor:pointer" class="remove fa fa-trash-o"></i></td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        @if (Request::old()!==null && !empty(Request::old()))
                                            @foreach(Request::old('channel_sku_quantity') as $channel_sku_id => $quantity)
                                                <tr data-prodid="{{old('prodid')[$channel_sku_id]}}">
                                                @if(isset(old('channel')[$channel_sku_id]))
                                                    <td><input type="hidden" name="channel[{{$channel_sku_id}}]" value="{{old('channel')[$channel_sku_id]}}"> {{old('channel')[$channel_sku_id]}}</td>
                                                 @endif
                                                <td>
                                                    <input type="hidden" name="prodid[{{$channel_sku_id}}]" value="{{old('prodid')[$channel_sku_id]}}">
                                                    <input type="hidden" name="skuid[{{$channel_sku_id}}]" value="{{old('skuid')[$channel_sku_id]}}">
                                                    <input type="hidden" name="pname[{{$channel_sku_id}}]" value="{{old('pname')[$channel_sku_id]}}">
                                                    <input type="hidden" name="options[{{$channel_sku_id}}]" value="{{old('options')[$channel_sku_id]}}">
                                                    <input type="hidden" name="tags[{{$channel_sku_id}}]" value="{{old('tags')[$channel_sku_id]}}">
                                                    <input type="hidden" name="hubwire_sku[{{$channel_sku_id}}]" value="{{old('hubwire_sku')[$channel_sku_id]}}">
                                                    <input type="hidden" name="brand_prefix[{{$channel_sku_id}}]" value="{{old('brand_prefix')[$channel_sku_id]}}">
                                                    <input type="hidden" name="quantity[{{$channel_sku_id}}]" value="{{old('quantity')[$channel_sku_id]}}">
                                                    {{old('skuid')[$channel_sku_id]}}
                                                </td>
                                                <td>{{old('hubwire_sku')[$channel_sku_id]}}</td>
                                                <td>{{old('brand_prefix')[$channel_sku_id]}}</td>
                                                <td>{{old('pname')[$channel_sku_id]}}</td>
                                                <td>{!! old('options')[$channel_sku_id] !!}</td>
                                                <td>{{old('tags')[$channel_sku_id]}}</td>
                                                <td>{{old('quantity')[$channel_sku_id]}}</td>
                                                <td>
                                                    <input max="{{old('quantity')[$channel_sku_id]}}" value="{{$quantity}}" name="channel_sku_quantity[{{$channel_sku_id}}]" id="csc'{{$channel_sku_id}}'" type="number">
                                                </td>
                                                <td><i style="cursor:pointer" class="remove fa fa-trash-o"></i></td></tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group pull-right">
                                <button type="button" id="create_new_transfer" class="btn btn-default">@lang('product-management.button_create_new_transfer')</button>
                                <button type="button" id="save_as_draft" class="btn btn-primary">@lang('product-management.button_save_as_draft')</button>
                            </div> <!-- / .form-actions -->
                        </div>
                        {!! Form::close() !!}
                        <div class="modal fade" id="uploadModal" aria-labelledby="uploadModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title">Upload Transfer Sheet</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="log" class="alert alert-danger" role="alert" style="display: none;">
                                        </div>
                                        <div>
                                        {!! Form::open(array('id'=>'download-csv', 'url' => route('products.download.csv'), 'method' => 'POST')) !!}
                                            <button type="button" id="download-create-sheet" class="btn btn-default">@lang('product-management.button_download_transfer_template')</button>
                                            <input type="hidden" name="link" value="{{ $templateUrl }}">
                                            <input type="hidden" name="filename" value="stock-out">
                                        {!! Form::close() !!}
                                        </div>
                                        <span class="upload-form">
                                            <label for="file_upload">Upload File</label>
                                            <br/>
                                            <input id="file_upload" type="file" name="product_sheet">
                                            <p class="help-block">Upload your transfer sheet here.</p>
                                        </span>
                                        <span class="progress-div">
                                            <label>Upload Progress</label>
                                            <div class="progress progress-sm">
                                                <div id="progress" class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                    </div>
                </div>
            </div>
        </div>
        <div class="html_to_replace"></div>
    </section>
@stop

@section('footer_scripts')
<style type="text/css">
    .modal-body {
        max-height: 600px;
        overflow: scroll;
    }
</style>
<link href="{{ asset('packages/blueimp/css/jquery.fileupload.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/jquery_ui_widgets.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.iframe-transport.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.fileupload.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var merchant_id = $('#merchant_dropdown').val();
        $("optgroup[label='Inactive'] option").attr("disabled", "disabled");

        // setup array for channel type
        var channelTypes = [];
        @foreach($channelTypes as $id => $channelType)
            channelTypes[{{ $id }}] = '{{ $channelType }}';
        @endforeach

        // type - warning, danger, success, info, etc
        function displayAlert(message, type) {
            $(".errors").html('<div class="alert alert-'+type+' alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
              '<span aria-hidden="true" style="font-size:inherit;">&times;</span></button>'+message+
            '</div>');
        }

        function clearItems() {
            var c = confirm('Are you sure you want to remove all items?');
            if(c){
                $('#do_items tbody').empty();
            }
        }

        // attach event to on merchant dropdown change
        $('#merchant_dropdown').change(function(){
            merchant_id = parseInt($(this).val());
            // For uploading file
            $('#file_upload').fileupload({
                url: '/products/stock_transfer/create/upload/'+merchant_id,
                dataType: 'json',
                add: function (e, data) {
                    $('#do_items tbody').empty();
                    $('#log').empty();
                    $('#log').hide();
                    $('#progress').addClass('active');
                    $('#progress').css('width', '0%');
                    merchant_id = $("#merchant_dropdown").val();
                    data.submit();
                },
                done: function (e, data) {
                    var result = data.result;
                    $('#progress').removeClass('active');
                    // console.log(result);

                    if(result.success){
                        $('#uploadModal').modal('hide');
                        $('#channel_col').remove();
                        $('thead tr' ).prepend( '<th id="channel_col">Channel</th>' );
                        $.each(result.skus, function(){
                            var tr = $('<tr data-prodid="'+this.product.id+'"></tr>');
                            var options = '';
                            $.each(this.sku_options, function(index, option){
                                options+=option.option_name+':'+option.option_value+' ';
                            });
                            var tags = '';
                            $.each(this.product.tags, function(index, tag){
                                tags+=tag.value+',';
                            });
                            tags = tags.slice(0,-1);

                            var tmp = '';
                            tmp += '<input type="hidden" name="skuid['+this.channel_sku_id+']" value="'+this.sku.sku_id+'"/> ';
                            // tmp += '<input type="hidden" name="channel['+this.channel_sku_id+']" value="'+this.channel.name+'"/> ';
                            // tmp += '<input type="hidden" name="hubwire_sku['+this.channel_sku_id+']" value="'+this.sku.hubwire_sku+'"/> ';
                            // tmp += '<input type="hidden" name="csid['+this.channel_sku_id+']" value="'+this.channel_sku_id+'"/> ';
                            tmp += '<input type="hidden" name="prodid['+this.channel_sku_id+']" value="'+this.product.id+'"/> ';
                            // tmp += '<input type="hidden" name="pname['+this.channel_sku_id+']" value="'+this.product.name+'"/> ';
                            // tmp += '<input type="hidden" name="brand_prefix['+this.channel_sku_id+']" value="'+this.product.brand+'"/> ';
                            // tmp += '<input type="hidden" name="options['+this.channel_sku_id+']" value="'+options+'"/> ';
                            // tmp += '<input type="hidden" name="tags['+this.channel_sku_id+']" value="'+tags+'"/> ';
                            // tmp += '<input type="hidden" name="quantity['+this.channel_sku_id+']" value="'+this.channel_sku_quantity+'"/> ';

                            var td = $('<td>'+tmp+this.channel.name+'</td><td>'+this.sku.sku_id+'</td><td>'+this.sku.hubwire_sku+'</td><td>'+this.product.brand+'</td><td>'+this.product.name+'</td><td>'+options+'</td><td>'+tags+'</td><td>'+this.channel_sku_quantity+'</td><td><input class="qty" type="number" min="0" max="'+this.channel_sku_quantity+'" value="'+this.quantity+'" name="channel_sku_quantity['+this.channel_sku_id+']" id="cs'+this.channel_sku_id+'"></td><td><i style="cursor:pointer" class="remove fa fa-trash-o"></i></td>');
                            tr.append(td);
                            $('#do_items tbody').prepend(tr);
                            $('.remove').click(function(){
                                $(this).closest('tr').remove();
                            });
                        });

                    }
                    else{

                        $('#log').empty();
                        $('<p/>').html('<h4>Upload process return error(s):-</h4>').appendTo('#log');
                        //console.log(result.error.messages);
                        if (result.error.messages!==undefined) {
                            $.each(result.error.messages, function (index, message){
                                // console.log(message);
                                $('<p/>').html(message[0]).appendTo('#log');
                            });
                        }
                        else {
                            $('<p/>').text("An error has occurred on the server. Please try again.").appendTo('#log');
                        }
                        $('#log').removeClass('alert-success').addClass('alert-danger').show();
                        setTimeout(function(){
                            $('#progress').css('width', '0%');
                        }, 1000);
                    }
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress').css('width', progress + '%');
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');

            var do_type = $('select[name="do_type"]').val();
            var url = "/admin/channels/merchant/"+$(this).val();
            if(do_type==2) url += '/channel_type/12';
            // clear channel dropdown
            $('.channel_dropdown').find('optgroup').remove();
            $(".channel_dropdown").trigger("change");
            // get list of channels based on merchant
            if($(this).val() > 0){
                // display loading prompt
                waitingDialog.show('Getting channel list...', {dialogSize: 'sm'});
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        if(do_type==2) data.channels = data;
                        if(data.channels==''){
                            // show placeholder
                            $('.channel_dropdown').append('<option>N/A</option>');
                        }else{
                            // loop thru response and build new select options
                            // var channelOptions = '';
                            var inactiveChannelOptions = '';
                            var channelTypeOptions = new Array;
                            if (data.channels!==undefined) {
                                $.each(data.channels, function( index, channel ) {
                                    if(channel.status == 'Active'){
                                        var channelOption = '';
                                        channelOption += '<option value="'+channel.id+'">';
                                        channelOption += channel.name;
                                        channelOption += '</option>';
                                        if(channelTypeOptions[channel.channel_type_id] === undefined)
                                            channelTypeOptions[channel.channel_type_id] = '';
                                        channelTypeOptions[channel.channel_type_id] += channelOption;
                                    }else{
                                        inactiveChannelOptions += '<option value="'+channel.id+'" disabled="disabled">';
                                        inactiveChannelOptions += channel.name;
                                        inactiveChannelOptions += '</option>';
                                    }
                                });
                                //console.log(channelTypeOptions);

                                $.each(channelTypeOptions, function( index, channelOptions ){
                                    if(channelOptions !== undefined)
                                        $('.channel_dropdown').append('<optgroup label="'+channelTypes[index]+'">'+channelOptions+'</optgroup>');
                                });
                                // if(channelOptions != '')
                                //     $('.channel_dropdown').append('<optgroup label="Active">'+channelOptions+'</optgroup>');
                                if(inactiveChannelOptions != '')
                                    $('.channel_dropdown').append('<optgroup label="Inactive">'+inactiveChannelOptions+'</optgroup>');

                                $('.channel_dropdown').trigger("change");
                            }
                        }
                    },
                    complete: function(){
                        waitingDialog.hide();
                    }
                });
            }
        });


        // get list of channels based on merchant
        if($('#merchant_dropdown').val() > 0){
            $('#merchant_dropdown').change();
        }

        // change delivery order type
        $('select[name="do_type"]').change(function() {
            var option = $(this).val();
            if(option>=0 && $('#do_items tbody tr').length){
                clearItems();
            }
            check_do_type(option);
        });

        function check_do_type(option)
        {
            if(option==0) {
                // procurement
                $('#procurement').slideDown('fast');
            }
            else if(option==2)
            {
                // hide originating channel
                $('#ori_channel').slideUp('fast');
                // load the warehouse channel list
                $('#merchant_dropdown').change();

                $('#upload-div').show();
                $('#btngrp-add').hide();
                $('#save_as_draft').hide();

            }

            if(option!=0 || option.length==0)
            {
                //normal or placeholder value
                $('#procurement').slideUp('fast');
            }

            if(option!=2 || option.length==0)
            {
                //normal or placeholder value
                $('#ori_channel').slideDown('fast');
                $('#upload-div').hide();
                $('#btngrp-add').show();
                $('#merchant_dropdown').change();
                $('#save_as_draft').show();
            }
        }

        check_do_type($('select[name="do_type"]').val());

        // populate items table with all skus from procurement batch
        $('#batch_id').on('keypress', function(e)
        {
            if (e.which == 13 ) {
                e.preventDefault();
                var channel_id = $('select[name="originating_channel_id"]').val();
                var merchantId = $('select[name="merchant_id"]').val();
                if(channel_id < 0 || channel_id.length==0) return;
                var ajaxUrl = "{{route('products.stock_transfer.getBatch', ['batchId', 'merchantId',  'channelId'])}}".replace('batchId', $(this).val());
                ajaxUrl = ajaxUrl.replace('merchantId', merchantId);
                ajaxUrl = ajaxUrl.replace('channelId', channel_id);

                $.ajax({
                    url: ajaxUrl,
                    method: 'GET',
                    beforeSend: function() {
                        // display loading prompt
                        waitingDialog.show('Finding procurement batch...', {dialogSize: 'sm'});
                    },
                    success: function(response){
                        response = JSON.parse(response);
                        //console.log(response['items']);
                        if(!response['success']){
                            //console.log(response['message']);
                            //alert(response.message);
                        }
                        else{
                            //console.log(response);
                            $('#do_items tbody').empty();
                            var found=0;
                            var qty=0;
                            for(var i in response['items']){
                                if(response.items[i].channel_sku_quantity>0){
                                    found++;
                                    qty=parseInt(response.items[i].item_quantity)<=parseInt(response.items[i].channel_sku_quantity)?response.items[i].item_quantity:response.items[i].channel_sku_quantity;
                                    var tmp = '<input type="hidden" name="prodid['+response.items[i].channel_sku_id+']" value="'+response.items[i].product_id+'"/>';
                                    tmp += '<input type="hidden" name="tags['+response.items[i].channel_sku_id+']" value="'+response.items[i].tags+'"/>';
                                    tmp += '<input type="hidden" name="skuid['+response.items[i].channel_sku_id+']" value="'+response.items[i].sku_id+'"/>';
                                    tmp += '<input type="hidden" name="pname['+response.items[i].channel_sku_id+']" value="'+response.items[i].name+'"/>';
                                    tmp += '<input type="hidden" name="options['+response.items[i].channel_sku_id+']" value="'+response.items[i].options+'"/>';
                                    tmp += '<input type="hidden" name="csid['+response.items[i].channel_sku_id+']" value="'+response.items[i].channel_sku_id+'"/>';
                                    tmp += '<input type="hidden" name="quantity['+response.items[i].channel_sku_id+']" value="'+qty+'"/>';
                                    var tr = $('<tr></tr>');
                                    var td = $('<td>'+tmp+response.items[i].sku_id+'</td><td>'+response.items[i].hubwire_sku+'</td><td>'+response.items[i].brand+'</td><td>'+response.items[i].name+'</td><td>'+response.items[i].options+'</td><td>'+response.items[i].tags+'</td><td>'+qty+'</td><td><input type="number" name="channel_sku_quantity['+response.items[i].channel_sku_id+']" value="'+qty+'" max="'+qty+'"></td><td><i style="cursor:pointer" class="remove fa fa-trash-o"></i></td>');
                                    tr.append(td);
                                    $('#do_items tbody').prepend(tr);
                                }
                            }

                            if (found==0) {
                                displayAlert('The procurement batch was not found. Please try again.', "danger");
                            }
                        }
                    },
                    complete: function(){
                        waitingDialog.hide();
                    }
                });
            }
        });

        $('.remove').click(function(){
            $(this).closest('tr').remove();
        });
        // add items to table
        $('#btnAdd').click(function() {
            if($('select[name="originating_channel_id"]').val()>0 && $('select[name="merchant_id"]').val()>0){

                var url = "{{route('products.stock_transfer.addItemsModal', ['channel_id', 'merchant_id'])}}".replace("channel_id", $('select[name="originating_channel_id"]').val());
                url = url.replace("merchant_id", $('select[name="merchant_id"]').val());
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('.html_to_replace').html(response);
                        $('#ajax_form').modal();
                    },
                });
            }
            else
            {
                $('select[name="originating_channel_id"]').focus();
            }
        });

        // clear all items from table
        $('#btnClear').click(function(){
            clearItems();
        });

        // save as draft
        $('#save_as_draft').click(function(e) {
            if ($.trim($("#do_items tbody").html())!='') {
                $('#do_status').val('0');
                $('#form_do').submit();
            }
            else {
                alert("There are no items to transfer.");
            }
        });

        // create new transfer
        $('#create_new_transfer').click(function(e) {
            if ($.trim($("#do_items tbody").html())=='') {
                alert("There are no items to transfer.");
            }
            else {
                $('#do_status').val('1');
                var c = confirm('Are you sure you want to perform this action?');
                if(c){
                    $('.errors').html('');
                    waitingDialog.show('Processing...', {dialogSize: 'sm'});
                    // check if target channel is active products.stock_transfer.process
                    var targetChannelId = $('select[name=target_channel_id]').val();
                    var postData = {
                        'targetChannelId': targetChannelId,
                    };
                    // var formData = $("#form_do").serializeArray();
                    // formData.push({"name" : 'targetChannelId', "value": targetChannelId});
                    // console.log(formData);
                    $.ajax({
                        url: "{{ route('products.stock_transfer.process') }}",
                        method: 'POST',
                        data: postData,
                        success: function(response){
                            if(response.success == false){
                                displayAlert(response.message, 'danger');
                                $('html, body').animate({
                                    scrollTop: $('.errors').offset().top - 100
                                }, 1000);
                            }
                            else{
                                //console.log('bar');
                                $('#form_do').submit();
                            }
                        },
                        complete: function(){
                            waitingDialog.hide();
                        }
                    });
                }
            }
        });

        $("#download-create-sheet").click(function() {
            console.log('download csv');
            $("#download-csv").submit();
        });

    });
</script>
@append