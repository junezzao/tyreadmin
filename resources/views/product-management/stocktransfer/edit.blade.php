@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('product-management.page_title_product_mgmt_transfer_edit')
@stop

@section('content')
    <!-- Content Header (Page header) -->
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
                        <h3 class="box-title">@lang('product-management.box_header_transfer_edit')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        {!! Form::open(array('url' => route('products.stock_transfer.update', $id), 'method' => 'PUT', 'id'=>'form_do')) !!}
                        <div class="col-xs-12">
                            <div class="col-xs-6">     
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="merchant_id">@lang('product-management.transfer_form_label_merchant'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select( 'merchant_id', $merchants, $stockTransfer->merchant_id, ['class' => 'form-control select2', 'disabled'=>'true', 'placeholder' => trans('product-management.transfer_form_placeholder_merchant'), 'id' => 'merchant_dropdown'] ) !!}
                                        <div class="error">{{ $errors->first('merchant_id') }}</div>
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="origin_channel">@lang('product-management.transfer_form_label_origin_channel'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('originating_channel_id', $channels, $stockTransfer->originating_channel_id, array('disabled'=>'true', 'class' => 'form-control select2', 'placeholder' => trans('product-management.transfer_form_placeholder_origin_channel'))) !!}
                                        <div class="error">{{ $errors->first('originating_channel_id') }}</div>
                                    </div>
                                    <input type="hidden" name="originating_channel_id" value="{{$stockTransfer->originating_channel_id}}" />
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="target_channel">@lang('product-management.transfer_form_label_target_channel'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('target_channel_id', $channels, $stockTransfer->target_channel_id, array('class' => 'form-control channel_dropdown select2', 'placeholder' => trans('product-management.transfer_form_placeholder_target_channel'))) !!}
                                        <div class="error">{{ $errors->first('target_channel_id') }}</div>
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="do_type">@lang('product-management.transfer_form_label_do_type'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('do_type', $doType, $stockTransfer->do_type, array('class' => 'form-control select2-nosearch', 'placeholder' => trans('product-management.transfer_form_placeholder_do_type'))) !!}
                                        <div class="error">{{ $errors->first('do_type') }}</div>
                                    </div>
                                </div>

                                <div class="form-group" style="display: none;" id="procurement">
                                    <label for="batch_id" class="col-sm-4 control-label">Procurement Batch</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('batch_id',!empty($stockTransfer->batch_id)?$stockTransfer->batch_id:'', array('class'=>'form-control','id'=>'batch_id')) !!}
                                        <div class="type_error text-danger"></div>
                                    </div>
                                </div>

                                <input type="hidden" name="do_status" id="do_status" value=''>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="pic">@lang('product-management.transfer_form_label_pic'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::select('pic', $users, $stockTransfer->person_incharge, array('class' => 'form-control select2', 'placeholder' => trans('product-management.transfer_form_placeholder_pic'))) !!}
                                        <div class="error">{{ $errors->first('pic') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_remarks'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::textarea( 'remarks', $stockTransfer->remarks, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_remarks')] ) !!}
                                        <div class="error">{{ $errors->first('remarks') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">     
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="transport_co">@lang('product-management.transfer_form_label_transport_co'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'transport_co', $stockTransfer->transport_co, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_transport_co')] ) !!}
                                        <div class="error">{{ $errors->first('transport_co') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="lorry_no">@lang('product-management.transfer_form_label_lorry_no'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'lorry_no', $stockTransfer->lorry_no, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_lorry_no')] ) !!}
                                        <div class="error">{{ $errors->first('lorry_no') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="driver_name">@lang('product-management.transfer_form_label_driver_name'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'driver_name', $stockTransfer->driver_name, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_driver_name')] ) !!}
                                        <div class="error">{{ $errors->first('driver_name') }}</div>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="driver_id">@lang('product-management.transfer_form_label_driver_id'): </label>
                                    <div class="col-xs-8">
                                        {!! Form::text( 'driver_id', $stockTransfer->driver_id, ['class' => 'form-control', 'placeholder' => trans('product-management.transfer_form_placeholder_driver_id')] ) !!}
                                        <div class="error">{{ $errors->first('driver_id') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 search">                        
                            <a id="btnAdd" class="options btn btn-default">@lang('product-management.button_add')</a>
                            <a id="btnClear" class="options btn btn-default">@lang('product-management.button_clear')</a>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group has-feedback">
                                <table id="do_items" width="100%" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
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
                                        @foreach($items as $item)
                                            <tr data-prodid="{{$item->channel_sku->product_id}}">
                                                <td>
                                                    <input type="hidden" name="prodid[{{$item->channel_sku_id}}]" value="{{$item->channel_sku->product_id}}"> 
                                                    <input type="hidden" name="csid[{{$item->channel_sku_id}}]" value="{{$item->channel_sku_id}}">
                                                    <input type="hidden" name="options[{{$item->channel_sku_id}}]" value="{{$item->options}}"> 
                                                    <input type="hidden" name="status[{{$item->channel_sku_id}}]" value="{{$item->status}}"> 
                                                    <input type="hidden" name="brand_prefix[{{$item->channel_sku_id}}]" value="{{$item->channel_sku->product->brand}}">
                                                    <input type="hidden" name="pname[{{$item->channel_sku_id}}]" value="{{$item->channel_sku->product->name}}"> 
                                                    <input type="hidden" name="skuid[{{$item->channel_sku_id}}]" value="{{$item->channel_sku->sku_id}}"> 
                                                    <input type="hidden" name="tags[{{$item->channel_sku_id}}]" value="{{$item->tags}}"> 
                                                    <input type="hidden" name="hubwire_sku[{{$item->channel_sku_id}}]" value="{{$item->channel_sku->sku->hubwire_sku}}"> 
                                                    <input type="hidden" name="quantity[{{$item->channel_sku_id}}]" value="{{$item->quantity}}"> 
                                                    {{$item->channel_sku->sku_id}}
                                                </td>
                                                <td>{{$item->channel_sku->sku->hubwire_sku}}</td>
                                                <td>{{$item->channel_sku->product->brand}}</td>
                                                <td>{{$item->channel_sku->product->name}}</td>
                                                <td>{!! $item->options !!}</td>
                                                <td>{{$item->tags}}</td>
                                                <td>{{$item->channel_sku->channel_sku_quantity}}</td>
                                                <td><input class="qty" type="number" max="{{$item->channel_sku->channel_sku_quantity}}" value="{{$item->quantity}}" name="channel_sku_quantity[{{$item->channel_sku_id}}]" id="csc{{$item->channel_sku_id}}"></td>
                                                <td><i style="cursor:pointer" class="remove fa fa-trash-o"></i></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group pull-right">
                                <button type="button" id="create_new_transfer" class="btn btn-default">@lang('product-management.button_update_transfer')</button>
                                <button type="button" id="save_as_draft" class="btn btn-primary">@lang('product-management.button_save_as_draft')</button>
                            </div> <!-- / .form-actions -->
                        </div>
                        {!! Form::close() !!}
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        // setup array for channel type
        var channelTypes = [];
        @foreach($channelTypes as $id => $channelType)
            channelTypes[{{ $id }}] = '{{ $channelType }}';
        @endforeach

        function clearItems() {
            var c = confirm('Do you want to remove all items from the table?');
            if(c){
                $('#do_items tbody').empty();
            }
        }

        $("optgroup[label='Inactive'] option").attr("disabled", "disabled");

        $('.channel_dropdown').find('option').remove();
        // get list of channels based on merchant
        if($('#merchant_dropdown').val() > 0){
            $.ajax({
                url: "/admin/channels/merchant/"+$('#merchant_dropdown').val(),
                type: 'GET',
                beforeSend: function() {
                    // display loading prompt
                    waitingDialog.show('Getting channel list...', {dialogSize: 'sm'});
                }, 
                success: function(data) {
                    if(data.channels==''){
                        // show placeholder
                        $('.channel_dropdown').append('<option>N/A</option>');
                    }else{
                        // loop thru response and build new select options
                        // var channelOptions = '';
                        var inactiveChannelOptions = '';
                        var channelTypeOptions = new Array;
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
                        // $('.channel_dropdown').append(channelOptions);
                        $.each(channelTypeOptions, function( index, channelOptions ){
                            if(channelOptions !== undefined)
                                $('.channel_dropdown').append('<optgroup label="'+channelTypes[index]+'">'+channelOptions+'</optgroup>');
                        });
                        // if(channelOptions != '')
                        //     $('.channel_dropdown').append('<optgroup label="Active">'+channelOptions+'</optgroup>');
                        if(inactiveChannelOptions != '')
                            $('.channel_dropdown').append('<optgroup label="Inactive">'+inactiveChannelOptions+'</optgroup>');
                        
                        //$('.channel_dropdown').trigger("change");

                        $("select[name=target_channel_id]").val("{{$stockTransfer->target_channel_id}}").trigger('change');
                    }
                },
                complete: function(){
                    waitingDialog.hide();
                }
            });
        }

        // display or hide procurement batch ID input field on page load
        var option = $('select[name="do_type"]').val();
        if(option==1 || option.length==0){
            //normal or placeholder value
            $('#procurement').slideUp('fast');
        }
        else if(option==0) {
            // procurement 
            $('#procurement').slideDown('fast');
        }

        // change delivery order type
        $('select[name="do_type"]').change(function() {
            var option = $(this).val();
            if(option>=0 && $('#do_items tbody tr').length){
                clearItems();
            }
            if(option==1 || option.length==0){
                //normal or placeholder value
                $('#procurement').slideUp('fast');
            }
            else if(option==0) {
                // procurement 
                $('#procurement').slideDown('fast');
            }          
        });


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
                            console.log(response['message']);
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
                                alert('No items found for that procurement batch'); 
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
                    $('#form_do').submit();
                }
            }
        });

    });
</script>
@append