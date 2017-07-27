@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_edit')
@stop

@section('header_scripts')

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
                        <h3 class="box-title">{{$name}}</h3>
                        <div>
                            <i>{{$brand_name." By ".$merchant_name }}</i>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body edit-product-detail-page">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li @if($activeTab == 'product') class="active" @endif><a href="#product-tab" data-toggle="tab">@lang('product-management.edit_product_tab_product')</a></li>
                                <li @if($activeTab == 'sku') class="active" @endif><a href="#sku-tab" data-toggle="tab">@lang('product-management.edit_product_tab_sku')</a></li>
                                <li @if($activeTab == 'channel') class="active" @endif><a href="#channels-tab" data-toggle="tab">@lang('product-management.edit_product_tab_channel')</a></li>
                            </ul>
                            <div class="tab-content">
                                <!-- Product Tab -->
                                <div class="tab-pane @if($activeTab == 'product')active @endif" id="product-tab">
                                    <!-- Product Description -->
                                    <div class="row custom-row">
                                        <div class="col-xs-2 label-div">
                                            <label>@lang('product-management.edit_product_label_details')</label>
                                            <i>@lang('product-management.edit_product_desc_details')</i>
                                        </div>
                                        <form id="product-desc-form">
                                            <input type="hidden" name="type" value='product'>
                                            <div class="col-xs-10 content-div">
                                                <div class="content-row">
                                                    <label>@lang('product-management.edit_product_label_title')</label>
                                                    <input class="form-control" type="text" name="name" value="{{$name}}" placeholder="@lang('product-management.edit_product_placeholder_title')">
                                                </div>
                                                <div class="content-row">
                                                    <label>@lang('product-management.edit_product_label_active')</label>
                                                    {!! Form::select('active', ['Inactive', 'Active'], $active, array('class' => 'form-control select2-nosearch', 'style'=>'max-width: 100px')) !!}
                                                </div>
                                                <div class="content-row">
                                                    <label>@lang('product-management.edit_product_label_desc')</label>
                                                    <textarea class="product-desc" name="description" placeholder="@lang('product-management.edit_product_placeholder_desc')">
                                                        {{$description}}
                                                    </textarea>
                                                </div>
                                                <div class="content-row">
                                                    <label>@lang('product-management.edit_product_label_category')</label>
                                                    {!! Form::select('category_id', $categories, $category_id, array('class' => 'form-control select2', 'style'=>'max-width: 50%', 'placeholder' => 'Select Category')) !!}
                                                </div>
                                                <div class="content-row">
                                                    <div class="pull-right">
                                                        <button type="button" id="btn_update_desc_action" class="btn btn-default">@lang('product-management.edit_product_btn_product_update')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Inventory & Option -->
                                    <div class="row custom-row">
                                        <div class="col-xs-2 label-div">
                                            <label>@lang('product-management.edit_product_label_inv')</label>
                                            <i>@lang('product-management.edit_product_inv_details')</i>
                                        </div>
                                        <div class="col-xs-10 content-div">
                                            <div class="content-row">
                                                <label>Stocks by Channel(s)</label>
                                                <table class="table table-bordered inv-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 33.33%;">@lang('product-management.edit_product_table_label_channel')</th>
                                                            <th style="width: 33.33%;">@lang('product-management.edit_product_table_label_qty')</th>
                                                            <th style="width: 33.33%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($channelList as $chnlId => $channel)
                                                            <tr>
                                                                <td>{{$channel['name']}}</td>
                                                                <td>{{$channel['qty']}}</td>
                                                                <td class="centered">
                                                                    <a href="{{route('products.stock_transfer.create', ['p'=>$id, 'm'=>$merchant_id,'c'=>$chnlId])}}" target="_blank" class="btn btn-default">
                                                                        @lang('product-management.edit_product_btn_channel_stock')
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                            <div class="content-row">
                                                <label>Stocks by SKU(s)</label>
                                                <table class="table table-bordered inv-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 25%;">Hubwire SKU</th>
                                                            <th style="width: 25%;">Warehouse Coordinate</th>
                                                            <th style="width: 25%;">Warehouse @lang('product-management.edit_product_table_label_qty')</th>
                                                            <th style="width: 25%;">Physical Store</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($skuQuantityList as $name => $qty)
                                                            <tr>
                                                                <td>{{$name}}</td>
                                                                <td>{{$qty['warehouseCoordinate']}}</td>
                                                                <td>{{$qty['whqty']}}</td>
                                                                <td>{{$qty['psqty']}}</td>
                                                            </tr>
                                                        @endforeach
                                                            <tr class="total-row">
                                                                <td colspan="2" align="right"><strong>Total</strong></td>
                                                                <td><strong>{{$skuTotalList['whTotal']}}</strong></td>
                                                                <td><strong>{{$skuTotalList['psTotal']}}</strong></td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="content-row">
                                                <label class="label-inline">@lang('product-management.edit_product_label_colors'):</label>
                                                <span>{{implode(', ',$colors)}}</span>
                                            </div>
                                            <div class="content-row">
                                                <label class="label-inline">@lang('product-management.edit_product_label_sizes'):</label>
                                                <span>{{implode(', ',$sizes)}}</span>
                                            </div>
                                            <form id="product-tags-form">
                                                <input type="hidden" name="type" value="tags">
                                                <div class="content-row">
                                                    <label>@lang('product-management.edit_product_label_tags'):</label>
                                                    <select class="form-control select2-tags" id="tag-input" multiple="multiple" name="product_tags[]" data-placeholder="@lang('product-management.edit_product_placeholder_tags')" style="width: 100%;">
                                                        @foreach($tagsList as $tag)
                                                            <option value="{{$tag}}">{{$tag}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </form>
                                            <div class="content-row">
                                                <div class="pull-right">
                                                    <button type="button" id="btn_update_tags_action" class="btn btn-default">@lang('product-management.edit_product_btn_tags_update')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Images -->
                                    <div class="row custom-row">
                                        <div class="col-xs-2 label-div">
                                            <label>@lang('product-management.edit_product_label_images')</label>
                                            <i>@lang('product-management.edit_product_img_details_1')</i>
                                            <br>
                                            <i>@lang('product-management.edit_product_img_details_2')</i>
                                            <div class="label-btn">
                                                <span class="btn btn-default fileinput-button">
                                                    <span>
                                                        @lang('product-management.edit_product_btn_img_upload')
                                                    </span>
                                                    <input id="fileupload" type="file" name="upload_file" multiple="true">
                                                </span>
                                            </div>
                                            <div class="label-btn">
                                                {!! Form::open(array('url'=>route('inventory.product.updateImgOrder', [$id]), 'method'=>'POST', 'id'=>'img_sort_update'))!!}
                                                    <input type="hidden" value="{{$mediaSortOrder}}" name="img_sort_order" id="media_order">
                                                    <button type="button" id="btn-save-sort" class="btn btn-default">@lang('product-management.edit_product_btn_img_order')</button>
                                                {!!Form::close()!!}
                                            </div>
                                            <div class="label-btn">
                                                <button type="button" id="btn-sync-img" class="btn btn-default">@lang('product-management.edit_product_btn_img_sync')</button>
                                            </div>
                                        </div>
                                        <div class="col-xs-10 content-div" id="dropzone">
                                            <div class="col-xs-4 main-img">
                                                @if(!is_null($default_media))
                                                    <img id="default-img" data-id="$default_media['id']" data-media-id="{{$default_media['media_id']}}" src="{{$default_media['path'].'_230x330'}}">
                                                @else
                                                    <img src="http://placehold.it/230x330">
                                                @endif
                                            </div>
                                            <div class="col-xs-8 img-div" id="thumbnails-sortable">
                                                @if(!is_null($media))
                                                    @foreach($media as $mediaImg)
                                                        <div class="@if($mediaImg['media_id']==$default_media['media_id'])default_img @endif col-xs-3 prod-img" id="img-id-{{$mediaImg['media_id']}}">
                                                            <div class='img-overlay-container'>
                                                                <div class="img-overlay">
                                                                    <button data-id="{{$mediaImg['id']}}" data-media-id="{{$mediaImg['media_id']}}" type="button" class="btn-img-default btn btn-default">Set Default</button>
                                                                    <button data-id="{{$mediaImg['id']}}" data-media-id="{{$mediaImg['media_id']}}" type="button" class="btn-img-delete btn btn-default">Delete</button>
                                                                </div>
                                                            </div>
                                                            <img data-path="{{$mediaImg['path']}}" data-id="{{$mediaImg['id']}}" data-media-id="{{$mediaImg['media_id']}}" class="img-responsive" src="{{$mediaImg['path'].'_110x158'}}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- SKU Tab -->
                                <div class="tab-pane @if($activeTab == 'sku')active @endif" id="sku-tab">
                                    <div class="sku-desc">
                                        <i>@lang('product-management.edit_product_sku_details')</i>
                                    </div>
                                    <div class="sku-table">
                                        <table class="table" id="sku-list">
                                            <thead>
                                                <tr>
                                                    <th>@lang('product-management.edit_product_label_system_sku')</th>
                                                    <th>@lang('product-management.edit_product_label_hw_sku')</th>
                                                    <th>@lang('product-management.edit_product_label_supplier_sku')</th>
                                                    <th>@lang('product-management.edit_product_label_sku_weight')</th>
                                                    <th><!-- Actions --></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($skuList as $sku)
                                                    <tr data-index="{{$sku['skuId']}}" class="sku-item-{{$sku['skuId']}}">
                                                        <td>
                                                            {{$sku['skuId']}}
                                                            <input type="hidden" name="type" value="sku">
                                                            <input type="hidden" name="sku_id" value="{{$sku['skuId']}}">
                                                        </td>
                                                        <td>
                                                            {{$sku['hwSku']}}
                                                        </td>
                                                        <td>
                                                            <!-- Supplier SKU -->
                                                            <span class="display-sku value-label">
                                                                {{$sku['supplierSku']}}
                                                            </span>
                                                            <input class="edit-sku form-control" name="sku_supplier_code" class="edit-sku" type="text" value="{{$sku['supplierSku']}}">
                                                        </td>
                                                        <td>
                                                            <span class="display-sku value-label">
                                                                {{$sku['skuWeight']}}
                                                            </span>
                                                            <input class="edit-sku form-control" name="sku_weight" class="edit-sku" type="text" value="{{$sku['skuWeight']}}">
                                                        </td>
                                                        <td class="sku-td-btns">
                                                            <span class="display-sku">
                                                                <button data-index="{{$sku['skuId']}}" type="button" class="edit btn btn-link">
                                                                    <i class="fa fa-edit" title="Edit"></i>
                                                                </button>
                                                            </span>
                                                            <span class="edit-sku">
                                                                <button data-index="{{$sku['skuId']}}" type="button" class="confirm btn btn-link">
                                                                    <i class="fa fa-floppy-o" title="Update"></i>
                                                                </button>
                                                                <button data-index="{{$sku['skuId']}}" type="button" class="cancel btn btn-link">
                                                                    <i class="fa fa-ban" title="Cancel"></i>
                                                                </button>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Channel Tab -->
                                <div class="tab-pane @if($activeTab == 'channel')active @endif" id="channels-tab">
                                    <div class="channel-desc">
                                        <i>@lang('product-management.edit_product_chnl_details')</i>
                                    </div>
                                    <div class="channel-table">
                                        @foreach($channelList as $chnlId => $channel)
                                            <div class="channel-div" id="channel-id-{{$chnlId}}" data-index="{{$chnlId}}">
                                                <input type="hidden" name="type" value="channel_sku">
                                                <label>{{$channel['name']}}</label>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('product-management.edit_product_label_chnl_sku')</th>
                                                            <th>@lang('product-management.edit_product_label_hw_sku')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_qty')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_live')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_price')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_sale')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_sale_start')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_sale_end')</th>
                                                            <th>@lang('product-management.edit_product_label_chnl_coord')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($channel['chnlSkuList'] as $chnlSkuId => $chnlSku)
                                                            <tr>
                                                                <td>{{$chnlSkuId}}</td>
                                                                <td>{{$chnlSku['hwSku']}}</td>
                                                                <td>{{$chnlSku['qty']}}</td>
                                                                <td>{{$chnlSku['live']}}</td>
                                                                <td>
                                                                    <span class="display-channel channel-value-label">
                                                                        {{$chnlSku['price']}}
                                                                    </span>
                                                                    <input class="edit-channel form-control" name="channel_sku[{{$chnlSkuId}}][channel_sku_price]" type="text" value="{{$chnlSku['price']}}">
                                                                </td>
                                                                <td>
                                                                    <span class="display-channel channel-value-label">
                                                                        {{$chnlSku['sale']}}
                                                                    </span>
                                                                    <input class="edit-channel form-control" name="channel_sku[{{$chnlSkuId}}][channel_sku_promo_price]" type="text" value="{{$chnlSku['sale']}}">
                                                                </td>
                                                                <td>
                                                                    <span class="display-channel channel-value-label">
                                                                        {{$chnlSku['sale_start']}}
                                                                    </span>
                                                                    <input class="edit-channel form-control datepicker" name="channel_sku[{{$chnlSkuId}}][promo_start_date]" type="text" value="{{$chnlSku['sale_start']}}">
                                                                </td>
                                                                <td>
                                                                    <span class="display-channel channel-value-label">
                                                                        {{$chnlSku['sale_end']}}
                                                                    </span>
                                                                    <input class="edit-channel form-control datepicker" name="channel_sku[{{$chnlSkuId}}][promo_end_date]" type="text" value="{{$chnlSku['sale_end']}}">
                                                                </td>

                                                                <td>
                                                                    <span class="display-channel channel-value-label">
                                                                        {{$chnlSku['coordinates']}}
                                                                    </span>
                                                                    <input class="edit-channel form-control" name="channel_sku[{{$chnlSkuId}}][channel_sku_coordinates]" type="text" value="{{$chnlSku['coordinates']}}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="pull-right">
                                                    <span class="display-channel">
                                                        <button data-index="{{$chnlId}}" type="button" class="edit-channel-btn btn btn-default">
                                                            @lang('product-management.edit_product_btn_chnl_edit')
                                                        </button>
                                                    </span>
                                                    <span class="edit-channel">
                                                        <button data-index="{{$chnlId}}" type="button" class="cancel-channel btn btn-default">
                                                            @lang('product-management.edit_product_btn_chnl_cancel')
                                                        </button>
                                                        <button data-name="{{$channel['name']}}" data-index="{{$chnlId}}" type="button" class="confirm-channel btn btn-default">
                                                            @lang('product-management.edit_product_btn_chnl_update')
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',env('HTTPS',false)) }}"></script>
<link href="{{ asset('plugins/jquery-ui-1.12.0/jquery-ui.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/jquery-ui-1.12.0/jquery-ui.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
<!-- File uploader -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<script src="{{ asset('packages/blueimp/js/load-image.all.min.js', env('HTTPS',false)) }}" type="text/javascript"></script>
<link href="{{ asset('packages/blueimp/css/jquery.fileupload.css', env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/jquery_ui_widgets.js', env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.iframe-transport.js', env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.fileupload.js', env('HTTPS',false)) }}" type="text/javascript"></script>
<link href="{{ asset('plugins/datepicker/datepicker3.css', env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js', env('HTTPS',false)) }}" type="text/javascript"></script>

<script type="text/javascript">
    function updateSkuLabelValue(){
        $('.value-label').each(function(index){
            $(this).text($(this).next().val());
        });
    }

    function updateChannelLabelValue(){
        $('.channel-value-label').each(function(index){
            $(this).text($(this).next().val());
        });
    }

    /* Update sortable image order into input field */
    function updateImageOrder(){
        var sortOrder = [];
        $('#thumbnails-sortable').find('.img-responsive').each(function(){
            sortOrder.push($(this).data('id'));
        });
        $('#media_order').val(sortOrder.join(','));
        reattachImgBtnEvents();
        //console.log(sortOrder);
    };

    function displayAjaxError(msg){
        var msg = msg || 'An error has occured, please try again later.';
        var errorDiv = '<div class="dialog-remove-on-hide">'+msg+'</div>';
        $('#loading-prompt-dialog .modal-content .modal-header h3').html('Failed!');
        $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-danger');
        $('#loading-prompt-dialog .modal-content .modal-body').prepend(errorDiv);
        $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
    }

    function displayAjaxSuccess(){
        $('#loading-prompt-dialog .modal-content .modal-header h3').html('Success!');
        $('#loading-prompt-dialog .modal-content .modal-body .progress .progress-bar').addClass('progress-bar-success');
        $('#loading-prompt-dialog .modal-content .modal-body .progress').removeClass('active');
        setTimeout(function () {waitingDialog.hide()}, 500);
    }

    function reattachImgBtnEvents(){
        $('.btn-img-delete, .btn-img-default').off('click');
        $('.btn-img-delete').on('click', function(){
            var proceed = confirm('Are you sure you want to delete this image? Action is irreversible.');
            if(proceed){
                var mediaId = $(this).data('media-id');
                var productMediaId = $(this).data('id');

                waitingDialog.show('Deleting image...', {dialogSize: 'sm'});

                $.ajax({
                    'url': "{{route('inventory.product.deleteImg', [$id])}}",
                    'method': 'POST',
                    'data': 'media_id='+mediaId+'&product_media_id='+productMediaId,
                    statusCode: {
                        500: function() {
                            displayAjaxError();
                        }
                    },
                    'success': function(response){
                        if(response.success == true){
                            $('#img-id-'+mediaId).remove();
                            updateImageOrder();
                            $('#btn-save-sort').trigger("click");
                        }else{
                            var errorMsg = '';
                            $.each(response.error, function(index, item){
                                errorMsg += '<li>'+item+'</li>';
                            });
                            var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                            displayAjaxError(error);
                        }
                    }
                });
            }
        });

        $('.btn-img-default').on('click', function(){
            var productMediaId = $(this).data('id');
            var mediaId = $(this).data('media-id');

            waitingDialog.show('Setting default image...', {dialogSize: 'sm'});

            $.ajax({
                'url': "{{route('inventory.product.setDefaultImg', [$id])}}",
                'method': 'POST',
                'data': 'product_media_id='+productMediaId,
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        // rearrange image and submit new order
                        $('.prod-img').removeClass('default_img');
                        $('#img-id-'+mediaId).addClass('default_img');
                        var defaultImgDiv = $("<div />").append($('#img-id-'+mediaId).clone()).html();
                        $('#img-id-'+mediaId).remove();
                        $('#thumbnails-sortable').prepend(defaultImgDiv);

                        // reattach events
                        $('#img-id-'+mediaId).hover(function(){
                            $(this).find('.img-overlay-container').show();
                            var img = $(this).find('.img-responsive');
                            $('#default-img').attr('src', img.data('path')+'_230x330');
                            $('#default-img').data('media-id', img.data('media-id'));
                            $('#default-img').data('id', img.data('id'));
                        },function(){
                            $(this).find('.img-overlay-container').hide();
                        });

                        $('#img-id-'+mediaId+' .btn-img-delete').on('click', function(){
                            var proceed = confirm('Are you sure you want to delete this image? Action is irreversible.');
                            if(proceed){
                                var mediaId = $(this).data('media-id');
                                var productMediaId = $(this).data('id');

                                waitingDialog.show('Deleting image...', {dialogSize: 'sm'});

                                $.ajax({
                                    'url': "{{route('inventory.product.deleteImg', [$id])}}",
                                    'method': 'POST',
                                    'data': 'media_id='+mediaId+'&product_media_id='+productMediaId,
                                    statusCode: {
                                        500: function() {
                                            displayAjaxError();
                                        }
                                    },
                                    'success': function(response){
                                        if(response.success == true){
                                            $('#img-id-'+mediaId).remove();
                                            updateImageOrder();
                                            $('#btn-save-sort').trigger("click");
                                        }else{
                                            var errorMsg = '';
                                            $.each(response.error, function(index, item){
                                                errorMsg += '<li>'+item+'</li>';
                                            });
                                            var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                                            displayAjaxError(error);
                                        }
                                    }
                                });
                            }
                        });
                        // get current image order and update it
                        updateImageOrder();
                        $('#btn-save-sort').trigger("click");
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                }
            });
        });
    }

    $(document).ready(function(){
        $('.datepicker').datepicker({
            format :  'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
        /* Product Tab */
        var initial_tags = {!!json_encode($tags)!!};

        /* Product Description */
        $(".product-desc").wysihtml5({
            toolbar: {
                "html": true,
            },
        });

        /* Submit product title/description update */
        $('#btn_update_desc_action').on('click', function(){
            waitingDialog.show('Updating product...', {dialogSize: 'sm'});
            $.ajax({
                'url': "{{route('products.inventory.update', [$id])}}",
                'method': 'PUT',
                'data': $('#product-desc-form').serialize(),
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        displayAjaxSuccess();
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                }
            });
        });

        /* Submit product tags */
        $('#btn_update_tags_action').on('click', function(){
            waitingDialog.show('Updating product tags...', {dialogSize: 'sm'});
            $.ajax({
                'url': "{{route('products.inventory.update', [$id])}}",
                'method': 'PUT',
                'data': $('#product-tags-form').serialize(),
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        displayAjaxSuccess();
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                }
            });
        });

        /* Initialize Select2 Elements */
        $(".select2-tags").val(initial_tags);
        $(".select2-tags").select2({
            tags: true,
            tokenSeparators: [","]
        });

        /* Sortable image */
        $( "#thumbnails-sortable" ).sortable({
            placeholder: {
                element: function(currentItem) {
                    return $("<div class='col-xs-2 ui-sortable-placeholder'><img src='http://placehold.it/110x158?text=%2B'></div>")[0];
                },
                update: function(container, p) {
                    return;
                }
            },
            //"col-xs-2 ui-sortable-placeholder",
            helper:"clone",
            update: function() {
                updateImageOrder();
            },
            opacity: 0.5,
        });

        /* Image events */

        /* Image overlay */
        $('.prod-img').hover(function(){
            $(this).find('.img-overlay-container').show();
            var img = $(this).find('.img-responsive');
            $('#default-img').attr('src', img.data('path')+'_230x330');
            $('#default-img').data('media-id', img.data('media-id'));
            $('#default-img').data('id', img.data('id'));
        },function(){
            $(this).find('.img-overlay-container').hide();
        });

        $('.btn-img-delete').on('click', function(){
            var proceed = confirm('Are you sure you want to delete this image? Action is irreversible.');
            if(proceed){
                var mediaId = $(this).data('media-id');
                var productMediaId = $(this).data('id');

                waitingDialog.show('Deleting image...', {dialogSize: 'sm'});

                $.ajax({
                    'url': "{{route('inventory.product.deleteImg', [$id])}}",
                    'method': 'POST',
                    'data': 'media_id='+mediaId+'&product_media_id='+productMediaId,
                    statusCode: {
                        500: function() {
                            displayAjaxError();
                        }
                    },
                    'success': function(response){
                        if(response.success == true){
                            $('#img-id-'+mediaId).remove();
                            updateImageOrder();
                            $('#btn-save-sort').trigger("click");
                        }else{
                            var errorMsg = '';
                            $.each(response.error, function(index, item){
                                errorMsg += '<li>'+item+'</li>';
                            });
                            var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                            displayAjaxError(error);
                        }
                    }
                });
            }
        });

        $('.btn-img-default').on('click', function(){
            var productMediaId = $(this).data('id');
            var mediaId = $(this).data('media-id');

            waitingDialog.show('Setting default image...', {dialogSize: 'sm'});

            $.ajax({
                'url': "{{route('inventory.product.setDefaultImg', [$id])}}",
                'method': 'POST',
                'data': 'product_media_id='+productMediaId,
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        // rearrange image and submit new order
                        $('.prod-img').removeClass('default_img');
                        $('#img-id-'+mediaId).addClass('default_img');
                        var defaultImgDiv = $("<div />").append($('#img-id-'+mediaId).clone()).html();
                        $('#img-id-'+mediaId).remove();
                        $('#thumbnails-sortable').prepend(defaultImgDiv);

                        // reattach events
                        $('#img-id-'+mediaId).hover(function(){
                            $(this).find('.img-overlay-container').show();
                            var img = $(this).find('.img-responsive');
                            $('#default-img').attr('src', img.data('path')+'_230x330');
                            $('#default-img').data('media-id', img.data('media-id'));
                            $('#default-img').data('id', img.data('id'));
                        },function(){
                            $(this).find('.img-overlay-container').hide();
                        });

                        $('#img-id-'+mediaId+' .btn-img-delete').on('click', function(){
                            var proceed = confirm('Are you sure you want to delete this image? Action is irreversible.');
                            if(proceed){
                                var mediaId = $(this).data('media-id');
                                var productMediaId = $(this).data('id');

                                waitingDialog.show('Deleting image...', {dialogSize: 'sm'});

                                $.ajax({
                                    'url': "{{route('inventory.product.deleteImg', [$id])}}",
                                    'method': 'POST',
                                    'data': 'media_id='+mediaId+'&product_media_id='+productMediaId,
                                    statusCode: {
                                        500: function() {
                                            displayAjaxError();
                                        }
                                    },
                                    'success': function(response){
                                        if(response.success == true){
                                            $('#img-id-'+mediaId).remove();
                                            updateImageOrder();
                                            $('#btn-save-sort').trigger("click");
                                        }else{
                                            var errorMsg = '';
                                            $.each(response.error, function(index, item){
                                                errorMsg += '<li>'+item+'</li>';
                                            });
                                            var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                                            displayAjaxError(error);
                                        }
                                    }
                                });
                            }
                        });

                        // get current image order and update it
                        updateImageOrder();
                        $('#btn-save-sort').trigger("click");
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                }
            });
        });

        /* Image upload */
        var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;

        // To get the total number of files being uploaded
        var noOfUploadFiles =0;
        $('#fileupload').fileupload({
            imageMinWidth: 800,
            imageMinHeight: 1148,
            url: '{{route('inventory.product.upload', [$id])}}',
            dropZone: $('#dropzone'),
            change: function (e, data) {
                var idx=0;
                $.each(data.files, function (index, file) {
                    idx++;
                });
                noOfUploadFiles = idx;
            },
            add: function (e, data) {
                waitingDialog.show('Uploading image...', {dialogSize: 'sm'});
                var uploadErrors = [];
                var file = data.files[0];
                var imgCount = 0;
                var errorMsg = '';
                // Check the number of images, do not allow more than 12 images
                $('#thumbnails-sortable').children('.prod-img').each(function(){
                    imgCount++;
                });
                //console.log(imgCount);
                //console.log(noOfUploadFiles);
                imgCount = imgCount + noOfUploadFiles;
                //console.log(imgCount);
                if(imgCount <= 12){
                    loadImage(file,function(img){
                        var size = $(img).attr('width')+'x'+$(img).attr('height');
                        if(size != '800x1148'){
                            uploadErrors.push('"'+file['name']+'" size dimensions must in 800x1148 pixels.');
                        }
                        if(file['type'].length && !acceptFileTypes.test(file['type'])) {
                            uploadErrors.push('"'+file['name']+'" is not an accepted file type.');
                        }
                        if(file['size'] > (500*1024)) {
                            uploadErrors.push('"'+file['name']+'" filesize is too big. max size is 500KB.');
                        }
                        if(uploadErrors.length > 0) {
                            $.each(uploadErrors, function(index, item){
                                errorMsg += '<li>'+item+'</li>';
                            });
                            var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                            setTimeout(function () {displayAjaxError(error);}, 500);
                        } else {
                            data.submit();
                        }
                    },{});
                }else{
                    displayAjaxError('Attempted upload exceeded max allowed images (12). Please delete some images to proceed.');
                }
            },
            fail: function (e, data) {
                var errorMsg = '';
                $.each(data.messages, function (index, error) {
                    errorMsg += '<li>'+error+'</li>';
                });
                var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                displayAjaxError(error);
            },
            done: function (e, data) {
                //console.log(data);
                var result = data.result;
                //console.log(result);
                if(result.success){
                    var div = $('<div/>', {class: 'col-xs-3 prod-img ui-sortable-handle', id:'img-id-'+result.response.media_id} );

                    var overlayDivCtnr = $('<div/>', {class: 'img-overlay-container'} ).appendTo(div);

                    var overlayDiv = $('<div/>', {class: 'img-overlay'} ).appendTo(overlayDivCtnr);

                    var btnDefault = $('<button/>', {class: 'btn btn-img-default btn-default', text: 'Set Default', 'data-media-id': result.response.media_id, 'data-id': result.response.id} ).appendTo(overlayDiv);

                    var btnDelete = $('<button/>', {class: 'btn btn-img-delete btn-default', text: 'Delete', 'data-media-id': result.response.media_id, 'data-id': result.response.id} ).appendTo(overlayDiv);

                    var img = $('<img/>', {src: result.response.media_path+'_110x158',class:'img-responsive', 'data-path': result.response.media_path, 'data-media-id': result.response.media_id, 'data-id': result.response.id}).appendTo(div);

                    img.on('click', function(){
                        $('#default-img').attr('src', $(this).data('path')+'_230x330');
                        $('#default-img').data('media-id', $(this).data('media-id'));
                        $('#default-img').data('id', $(this).data('id'));
                    });

                    div.hover(function(){
                        $(this).find('.img-overlay-container').show();
                        var img = $(this).find('.img-responsive');
                        $('#default-img').attr('src', img.data('path')+'_230x330');
                        $('#default-img').data('media-id', img.data('media-id'));
                        $('#default-img').data('id', img.data('id'));
                    },function(){
                        $(this).find('.img-overlay-container').hide();
                    });

                    div.appendTo('#thumbnails-sortable');
                    // Update the image sort hidden input field to include the new image.
                    updateImageOrder();
                    $('#btn-save-sort').trigger("click");
                }
                else{
                    var errors = '';
                    $.each(result.error, function(key, message){
                        errors += '<li>' + message + '</li>';
                    });
                    displayAjaxError('Errors:<br/><ul>' + errors + '</ul>');
                }
            },
            stop: function(e, data){

            },
        }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

        // To update the product images's order
        /* Submit image order */
        $('#btn-save-sort').on('click', function(){
            if(!$('.modal').is(':visible')){
                waitingDialog.show('Updating image order...', {dialogSize: 'sm'});
            }
            /* Perform AJAX request here */
            $.ajax({
                url: $('#img_sort_update').attr('action'),
                type: 'POST',
                data: $('#img_sort_update').serialize(),
                success: function(response){
                    if(response.success == true){
                        displayAjaxSuccess();
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                },
            });
        });

        /* Sync Images */
        $('#btn-sync-img').on('click', function(){
            waitingDialog.show('Creating image syncs...', {dialogSize: 'sm'});
            /* Perform AJAX request here */
            $.ajax({
                url: '{{route('inventory.product.syncImages', [$id])}}',
                type: 'POST',
                success: function(response){
                    if(response.success == true){
                        displayAjaxSuccess();
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                },
            });
        });

        /* SKU Tab */
        /* Button events */
        $('.edit').on('click', function(){
            var product_id = $(this).data('index');
            $('.sku-item-'+product_id+' .display-sku').hide();
            $('.sku-item-'+product_id+' .edit-sku').show();
        });
        $('.cancel').on('click', function(){
            var product_id = $(this).data('index');
            $('.sku-item-'+product_id+' .display-sku').show();
            $('.sku-item-'+product_id+' .edit-sku').hide();
        });
        $('.confirm').on('click', function(){
            var sku_id = $(this).data('index');
            waitingDialog.show('Updating System SKU ID '+sku_id+'...', {dialogSize: 'sm'});
            $.ajax({
                'url': "{{route('products.inventory.update', [$id])}}",
                'method': 'PUT',
                'data': $('.sku-item-'+sku_id+' input').serialize(),
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        displayAjaxSuccess();
                        updateSkuLabelValue();
                        $('.sku-item-'+sku_id+' .display-sku').show();
                        $('.sku-item-'+sku_id+' .edit-sku').hide();
                        setTimeout(function () {waitingDialog.hide()}, 500);
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                }
            });
        });

        /* Channel Tab */
        $('.edit-channel-btn').on('click', function(){
            var channel_id = $(this).data('index');
            $('#channel-id-'+channel_id+' .display-channel').hide();
            $('#channel-id-'+channel_id+' .edit-channel').show();
        });
        $('.cancel-channel').on('click', function(){
            var channel_id = $(this).data('index');
            $('#channel-id-'+channel_id+' .display-channel').show();
            $('#channel-id-'+channel_id+' .edit-channel').hide();
        });
        $('.confirm-channel').on('click', function(){
            var channel_id = $(this).data('index');
            var channel_name = $(this).data('name');
            waitingDialog.show('Updating '+channel_name+' channel...', {dialogSize: 'sm'});
            $.ajax({
                'url': "{{route('products.inventory.update', [$id])}}",
                'method': 'PUT',
                'data': $('#channel-id-'+channel_id+' input').serialize(),
                statusCode: {
                    500: function() {
                        displayAjaxError();
                    }
                },
                'success': function(response){
                    if(response.success == true){
                        displayAjaxSuccess();
                        updateChannelLabelValue();
                        $('#channel-id-'+channel_id+' .display-channel').show();
                        $('#channel-id-'+channel_id+' .edit-channel').hide();
                        setTimeout(function () {waitingDialog.hide()}, 500);
                    }else{
                        var errorMsg = '';
                        $.each(response.error, function(index, item){
                            errorMsg += '<li>'+item+'</li>';
                        });
                        var error = 'Errors:<br/><ul>'+errorMsg+'</ul>';
                        displayAjaxError(error);
                    }
                }
            });
        });
    });
</script>
@append