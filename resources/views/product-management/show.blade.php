@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_mgmt_create_show')
@stop

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
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
                        <h3 class="box-title">@lang('product-management.box_header_create_show')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body create-restock create-restock-show-page">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="merchandiser">@lang('product-management.create_form_label_merchandiser'): </label>
                                    <div class="col-xs-8">
                                        {{$admin}}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="batch_date">@lang('product-management.create_form_label_batch_date'): </label>
                                    <div class="col-xs-8">
                                        {{$batch_date}}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="merchant">@lang('product-management.create_form_label_merchant'): </label>
                                    <div class="col-xs-8">
                                        {{$merchant}}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="merchant">@lang('product-management.create_form_label_supplier'): </label>
                                    <div class="col-xs-8">
                                        {{$supplier}}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label required" for="merchant">@lang('product-management.create_form_label_channel'): </label>
                                    <div class="col-xs-8">
                                        {{$channel}}
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.create_form_label_remarks'): </label>
                                    <div class="col-xs-8">
                                        {{$batch_remarks}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
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

                            @if (strtolower($statusLabel)=='received' || strtolower($statusLabel)=='pending')
                                <div class="col-xs-12">
                                    <a href='{{ route("products.get_barcode_csv", $id) }}' id="download_barcode" class="btn btn-default pull-left" data-batch-id={{$id}} target="_blank">@lang('product-management.button_download_barcode')</a>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="form-group has-feedback col-xs-12">
                                <h4 class="merchant-header">@lang('product-management.create_form_label_table_products')</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group has-feedback col-xs-12">
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr>
                                                <td>
                                                    <label>{{trans('product-management.create_form_label_supplier_sku')}}: </label>
                                                    <br>
                                                    <span class="display-product value-label">{{$product['supplier_sku']}}</span>
                                                    <br>
                                                    <label>{{trans('product-management.create_form_label_hw_sku')}}: </label>
                                                    <br>
                                                    <span class="display-product value-label">{{$product['hw_sku']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['name']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['category']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['color']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['size']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['tags']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['item_quantity']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['unit_cost']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['unit_price']}}</span>
                                                </td>
                                                <td>
                                                    <span class="display-product value-label">{{$product['weight']}}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group pull-right">
                                @if($status < 1 && \Auth::user()->can('edit.restock'))
                                    <div class="col-xs-6">
                                            @if($replenishment == 1)
                                                <a href="{{route('products.restock.edit', [$id])}}">
                                                    <button type="button" id="btn_edit_action" class="btn btn-default">@lang('product-management.button_edit_restock')</button>
                                                </a>
                                            @else
                                                <a href="{{route('products.create.edit', [$id])}}">
                                                    <button type="button" id="btn_edit_action" class="btn btn-default">@lang('product-management.button_edit_create')</button>
                                                </a>
                                            @endif
                                    </div>
                                    <div class="col-xs-6">
                                        <button type="button" id="btn_receive_action" class="btn btn-default">@lang('product-management.button_receive_create')</button>
                                    </div>
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
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">

<script type="text/javascript">
    $(document).ready(function(){
        $('#product-list').DataTable({
        });
        $('#btn_receive_action').on('click', function(){
            var receive = confirm('Are you sure you want to receive this product sheet? Action is irreversible. \n\nWARNING!!\nIf the target channel is a sales channel, quantities will be synced over immediately upon receiving the batch.\nPlease ensure that all products are barcoded and shelved first!');
            if(receive){
                window.location.replace('{{route('products.receive', [$id])}}');
            }
        });

        /*$("#download_barcode").click(function(){
            window.location.href =
            var batch_id = $(this).data('batch-id');
            waitingDialog.show('Checking if barcode list exists...', {dialogSize: 'sm'});

            $.ajax({
                url: '{{ route("products.get_barcode_csv", $id) }}',
                success: function(result)
                {
                    if(result.success){
                        waitingDialog.hide();
                        window.location.href = result.url;
                    }else{
                        waitingDialog.hide();
                    }
                }
            });
        });*/
    });
</script>
@append