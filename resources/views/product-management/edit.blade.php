@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_mgmt_edit')
@stop

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
@append

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
                        <h3 class="box-title">@lang('product-management.box_header_edit')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body create-restock create-restock-edit-page">
                        {!! Form::open(array('url' => route('products.destroy', ['type'=>$type, 'id'=>$id]), 'method' => 'DELETE', 'id' => 'delete-create-sheet')) !!}
                        {!! Form::close() !!}
                        {!! Form::open(array('url' => route('products.update', ['type'=>$type, 'id'=>$id]), 'method' => 'PUT')) !!}
                            <div class="col-xs-12">
                                <div class="col-xs-6">
                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="merchandiser">@lang('product-management.create_form_label_merchandiser')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('merchandiser', $adminList, $user_id, array('class' => 'form-control select2', 'placeholder' => trans('product-management.create_form_placeholder_merchandiser'))) !!}
                                            <div class="error">{{ $errors->first('merchandiser') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="batch_date">@lang('product-management.create_form_label_batch_date')</label>
                                        <div class="col-xs-9">
                                            {!! Form::text( 'batch_date', $batch_date, ['class' => 'form-control', 'placeholder' => trans('product-management.create_form_placeholder_batch_date'),'id' => 'batch_date_input'] ) !!}
                                            <div class="error">{{ $errors->first('batch_date') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="channel">@lang('product-management.create_form_label_supplier')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('supplier', $supplierList, $supplier_id, array('class' => 'form-control select2', 'id' => 'supplier_dropdown', 'placeholder' => trans('product-management.create_form_placeholder_supplier'))) !!}
                                            <div class="error">{{ $errors->first('supplier') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="channel">@lang('product-management.create_form_label_channel')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('channel', $channelList, $channel_id, array('class' => 'form-control select2', 'id' => 'channel_dropdown', 'placeholder' => trans('product-management.create_form_placeholder_channel'))) !!}
                                            <div class="error">{{ $errors->first('channel') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label" for="remarks">@lang('product-management.create_form_label_remarks')</label>
                                        <div class="col-xs-9">
                                            {!! Form::textarea( 'remarks', $batch_remarks, ['class' => 'form-control', 'placeholder' => trans('product-management.create_form_placeholder_remarks')] ) !!}
                                            <div class="error">{{ $errors->first('remarks') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group has-feedback">
                                        <label class="col-xs-4 control-label">@lang('product-management.create_form_label_merchant'): </label>
                                        <div class="col-xs-8">
                                            {{$merchant}}
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-4 control-label" for="remarks">@lang('product-management.create_form_label_status'): </label>
                                        <div class="col-xs-8">
                                            {{$statusLabel}}
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label class="col-xs-4 control-label" for="remarks">@lang('product-management.create_form_label_type'): </label>
                                        <div class="col-xs-8">
                                            @if($replenishment == 1)
                                                @lang('product-management.restock_form_label_table_show')
                                            @else
                                                @lang('product-management.create_form_label_table_show')
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="total_products" id="total_products" value="{{$no_of_items}}">
                            @if($status < 1)
                                <div class="col-xs-12">
                                    <div class="col-xs-6">
                                            <div class="form-group row pull-left">
                                                <button type="button" id="btn_delete_action" class="btn btn-danger">
                                                    @lang('product-management.button_delete_create')
                                                </button>
                                            </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-right: 0px;">
                                        <div class="form-group pull-right">
                                            <div class="col-xs-7 btn-right-align-div">
                                                <button type="button" id="btn_receive_action" class="btn btn-default">@lang('product-management.button_receive_create')</button>
                                            </div>
                                            <div class="col-xs-5 btn-right-align-div">
                                                <button type="submit" id="btn_save_action" class="btn btn-default">@lang('product-management.button_update_create')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        {!! Form::close() !!}
                        {!! Form::open(array('id' => 'update_status_form', 'url' => route('products.receive', [$id]), 'method' => 'POST')) !!}
                        {!! Form::close() !!}
                        <div class="col-xs-12">
                            <div class="form-group has-feedback">
                                <h4 class="merchant-header">@lang('product-management.create_form_label_table_products')</h4>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group has-feedback">
                                <table class="table table-bordered table-striped" id="product-list">
                                    <thead>
                                        <tr>
                                            <th>@lang('product-management.create_form_label_sku')</th>
                                            <th>@lang('product-management.create_form_label_name')</th>
                                            <th>@lang('product-management.create_form_label_category')</th>
                                            <th>@lang('product-management.create_form_label_color')</th>
                                            <th>@lang('product-management.create_form_label_size')</th>
                                            <th>@lang('product-management.create_form_label_tags')</th>
                                            <th>@lang('product-management.create_form_label_quantity')</th>
                                            <th>@lang('product-management.create_form_label_unit_cost')</th>
                                            <th>@lang('product-management.create_form_label_unit_price')</th>
                                            <th>@lang('product-management.create_form_label_weight')</th>
                                            @if($status < 1)
                                                <th>@lang('product-management.create_form_label_action')</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr data-index="{{$product['item_id']}}" class="product-item-{{$product['item_id']}}">
                                                <td>
                                                    <input name="item_id" type="hidden" value="{{$product['item_id']}}">
                                                    <input name="batch_id" type="hidden" value="{{$id}}">
                                                    <input name="product_id" type="hidden" value="{{$product['product_id']}}">
                                                    <input name="sku_id" type="hidden" value="{{$product['sku_id']}}">
                                                    <label>{{trans('product-management.create_form_label_supplier_sku')}}: </label>
                                                    <br>
                                                    <span>{{$product['supplier_sku']}}</span>
                                                    <br>
                                                    <label>{{trans('product-management.create_form_label_hw_sku')}}: </label>
                                                    <br>
                                                    <span class="hw_sku">{{$product['hw_sku']}}</span>
                                                </td>
                                                <td>
                                                    <span @if($replenishment == 0 && $status < 1)class="display-product value-label"@endif>{{$product['name']}}</span>
                                                    @if($replenishment == 0 && $status < 1)
                                                    <input name="name" class="edit-product" type="text" value="{{$product['name']}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['category']}}</span>
                                                </td>
                                                <td>
                                                    <span @if($replenishment == 0 && $status < 1)class="display-product value-label"@endif>{{$product['color']}}</span>
                                                    @if($replenishment == 0 && $status < 1)
                                                    <input name="color" class="edit-product" type="text" value="{{$product['color']}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <span @if($replenishment == 0 && $status < 1)class="display-product value-label"@endif>{{$product['size']}}</span>
                                                    @if($replenishment == 0 && $status < 1)
                                                    <input name="size" class="edit-product" type="text" value="{{$product['size']}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <span @if($status < 1)class="display-product value-label"@endif>{{$product['tags']}}</span>
                                                    @if($status < 1)
                                                    <input name="tags" class="edit-product" type="text" value="{{$product['tags']}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['item_quantity']}}</span>
                                                    <input name="item_quantity" class="edit-product" type="text" value="{{$product['item_quantity']}}">
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['unit_cost']}}</span>
                                                    <input name="item_cost" class="edit-product" type="text" value="{{$product['unit_cost']}}">
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['unit_price']}}</span>
                                                    <input name="unit_price" class="edit-product" type="text" value="{{$product['unit_price']}}">
                                                </td>
                                                <td>
                                                    <span @if($status < 1)class="display-product value-label"@endif>{{$product['weight']}}</span>
                                                    @if($status < 1)
                                                    <input name="weight" class="edit-product" type="text" value="{{$product['weight']}}">
                                                    @endif
                                                </td>
                                                @if($status < 1)
                                                    <td>
                                                        <span class="display-product">
                                                            <button data-index="{{$product['item_id']}}" type="button" class="edit btn btn-link">Edit</button> | <button type="button" data-index="{{$product['item_id']}}" class="remove btn btn-link">Remove</button>
                                                        </span>
                                                        <span class="edit-product">
                                                            <button data-index="{{$product['item_id']}}" type="button" class="confirm btn btn-link">Update</button> | 
                                                            <button data-index="{{$product['item_id']}}" type="button" class="cancel btn btn-link">Cancel</button>
                                                        </span>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="error">{{ $errors->first('total_products') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>

<script type="text/javascript">
    function updateLabelValue(){
        $('.value-label').each(function(index){
            $(this).text($(this).next().val());
        });
    }

    $(document).ready(function(){
        $("optgroup[label='Inactive'] option").attr("disabled", "disabled");

        // initialize date picker
        $('#batch_date_input').datepicker({
            format :  'yyyy-mm-dd',
            autoclose: true,
        });
        $('#btn_receive_action').on('click', function(){
            var receive = confirm('Are you sure you want to receive this product sheet? Action is irreversible. \n\nWARNING!!\nIf the target channel is a sales channel, quantities will be synced over immediately upon receiving the batch.\nPlease ensure that all products are barcoded and shelved first!');
            if(receive){
                window.location.replace('{{route('products.receive', [$id])}}');
            }
        });
        $('#btn_delete_action').on('click', function(){
            var remove = confirm('Are you sure you want to delete this product sheet? Action is irreversible.');
            if(remove){
                $('#delete-create-sheet').submit();
            }
        });
        $('.edit').on('click', function(){
            var product_id = $(this).data('index');
            $('.product-item-'+product_id+' .display-product').hide();
            $('.product-item-'+product_id+' .edit-product').show();
        });
        $('.cancel').on('click', function(){
            var product_id = $(this).data('index');
            $('.product-item-'+product_id+' .display-product').show();
            $('.product-item-'+product_id+' .edit-product').hide();
        });
        $('.confirm').on('click', function(){
            var product_id = $(this).data('index');
            waitingDialog.show('Updating product...', {dialogSize: 'sm'});
            $.ajax({
                'url': "/products/item/"+product_id+"/update",
                'method': 'POST',
                'data': $('.product-item-'+product_id+' input').serialize(),
                statusCode: {
                    500: function() {
                        var errorDiv = '<div class="dialog-remove-on-hide">An error has occured, please try again later.</div>';
                        //console.log($('#loading-modal-prompt .modal-content .modal-header'));
                        $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
                        $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
                        $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
                        $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        //console.log($('#loading-modal-prompt .modal-content .modal-header'));
                        $('#loading-prompt-dialog .modal-content .modal-header h3').html('Success!');
                        $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-success');
                        $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
                        updateLabelValue();
                        $('.product-item-'+product_id+' .hw_sku').html(response.response.sku.hubwire_sku);
                        $('.product-item-'+product_id+' .display-product').show();
                        $('.product-item-'+product_id+' .edit-product').hide();
                        setTimeout(function () {waitingDialog.hide()}, 500);
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var errorDiv = '<div class="dialog-remove-on-hide">Errors:<br/><ul>'+errorMsg+'</ul></div>';
                        //console.log($('#loading-modal-prompt .modal-content .modal-header'));
                        $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
                        $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
                        $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
                        $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
                    }
                }
            });
        });

        $('.remove').on('click', function(){
            var remove = confirm('Are you sure you want to delete this product from the list? Action is irreversible.');
            if(remove){
                var product_id = $(this).data('index');
                var removeObj = $(this).parents('.product-item-'+product_id);
                waitingDialog.show('Deleting product...', {dialogSize: 'sm'});
                $.ajax({
                    'url': "/products/item/"+product_id+"/delete/",
                    'method': 'POST',
                    'data': $('.product-item-'+product_id+' input').serialize(),
                    statusCode: {
                        500: function() {
                            var errorDiv = '<div class="dialog-remove-on-hide">An error has occured, please try again later.</div>';
                            //console.log($('#loading-modal-prompt .modal-content .modal-header'));
                            $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
                            $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
                            $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
                            $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
                        }
                    },
                    'success': function(response){
                        if(response.success == true){
                            //console.log($('#loading-modal-prompt .modal-content .modal-header'));
                            $('#loading-prompt-dialog .modal-content .modal-header h3').html('Successfully deleted product!');
                            $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-success');
                            $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
                            $('.product-item-'+product_id).remove();
                            productList.row(removeObj).remove().draw();
                            setTimeout(function () {waitingDialog.hide()}, 500);
                        }else{
                            var errorMsg = '';
                            $.each(response.error, function(index, item){
                                errorMsg += '<li>'+item+'</li>';
                            });
                            var errorDiv = '<div class="dialog-remove-on-hide">Errors:<br/><ul>'+errorMsg+'</ul></div>';
                            //console.log($('#loading-modal-prompt .modal-content .modal-header'));
                            $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
                            $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
                            $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
                            $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
                        }
                    }
                });
            }
        });
        // initialize datatable
        var productList = $('#product-list').DataTable({
        });
    });
</script>
@append