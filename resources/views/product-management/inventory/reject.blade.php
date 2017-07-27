@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_mgmt_reject')
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
                        <h3 class="box-title">@lang('product-management.box_header_reject')</h3>
                        <span style="float:right">
                            <a class="btn bg-purple margin" role="button" href="{{ route('products.inventory.index', ['pid'=>implode(',', $product_ids)]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
                        </span>
                    </div><!-- /.box-header -->
                    <div class="box-body bulk-reject-page">
                        <div class="col-xs-offset-9 col-xs-3 pull-right">
                            @if(isset($channels))
                                {!! Form::select( 'filter', $channels, null, ['class' => 'form-control', 'placeholder' => trans('product-management.reject_product_placeholder_channel'), 'id' => 'channel-filter'] ) !!}
                            @endif
                        </div>
                        <form id="reject-form" method="POST" action="{{route('inventory.reject.store')}}">
                            <input type="hidden" name="original-products" value="{{ $original_products }}" />
                            <input type="hidden" name="product_ids" value="{{ implode(',', $product_ids) }}"/>
                            <div class="col-xs-12">
                                @foreach($products as $productId => $product)
                                    <div class="product-accordion">
                                        <div class="product-title accordion-display">
                                            <h4>{{$product['name']}}</h4>
                                        </div>
                                        <div class="product-content">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('product-management.reject_product_table_label_hw_sku')</th>
                                                        <th>@lang('product-management.reject_product_table_label_channel')</th>
                                                        <th>@lang('product-management.reject_product_table_label_qty')</th>
                                                        <th>@lang('product-management.reject_product_table_label_reject_qty')</th>
                                                        <th>@lang('product-management.reject_product_table_label_reason')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($product['skus']))
                                                        @foreach($product['skus'] as $hwSkuId => $hwSku)
                                                            @foreach($hwSku as $index => $sku)
                                                                <tr>
                                                                    @if($index == 0)
                                                                        <td rowspan="{{count($hwSku)}}"><b>{{$hwSku[0]['hubwire_sku']}}</b></td>
                                                                    @endif
                                                                    <td class="chnl-td chnl-id-{{$sku['channel_id']}}">{{$sku['channel_name']}}</td>
                                                                    <td class="chnl-td chnl-id-{{$sku['channel_id']}}">{{$sku['qty']}}</td>
                                                                    @if($sku['qty'] > 0)
                                                                        <td class="chnl-td chnl-id-{{$sku['channel_id']}}">
                                                                            <div class="form-group">
                                                                                <input class="form-control qty-input" type="number" min="1" max="{{$sku['qty']}}" name="qty[{{$hwSkuId}}][{{$sku['channel_id']}}]">
                                                                                <span class="help-block"></span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="chnl-td chnl-id-{{$sku['channel_id']}}">
                                                                            <div class="form-group">
                                                                                {!! Form::select( 'remarks['.$hwSkuId.']['.$sku["channel_id"].']', $reasons, null, ['class' => 'select2 form-control reason-select', 'data-placeholder' => trans('product-management.reject_product_placeholder_reason')] ) !!}
                                                                            <span class="help-block"></span>
                                                                            </div>
                                                                        </td>
                                                                    @else
                                                                        <td class="chnl-td chnl-id-{{$sku['channel_id']}}">
                                                                            <div class="form-group">
                                                                                <input class="form-control" type="number" max="{{$sku['qty']}}" disabled>
                                                                            </div>
                                                                        </td>
                                                                        <td class="chnl-td chnl-id-{{$sku['channel_id']}}">
                                                                            <div class="form-group">
                                                                                {!! Form::select( 'remarks['.$hwSkuId.']['.$sku["channel_id"].']', $reasons, null, ['class' => 'select2 form-control reason-select', 'data-placeholder' => trans('product-management.reject_product_placeholder_reason'), 'disabled'] ) !!}
                                                                            <span class="help-block"></span>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5">
                                                                <i>@lang('product-management.reject_product_label_channel')</i>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    <a style="margin-top: 20px; margin-bottom: 0px;" class="btn bg-purple margin" role="button" href="{{ route('products.inventory.index', ['pid'=>implode(',', $product_ids)]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
                                    <button id="submit-reject" type="button" class="btn btn-info">@lang('product-management.reject_product_btn_submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')

<script type="text/javascript">
    function attachEvents(){
        $('.accordion-hide').off('click');
        $('.accordion-display').off('click');
        $('.accordion-hide').on('click', function(){
            var child = $(this).next();
            $(this).removeClass('accordion-hide').addClass('accordion-display');;
            child.slideDown('fast', function(){
                attachEvents();
            });
        });
        $('.accordion-display').on('click', function(){
            var child = $(this).next();
            $(this).removeClass('accordion-display').addClass('accordion-hide');;
            child.slideUp('fast', function(){
                attachEvents();
            });
        });
    }
    $(document).ready(function(){
        // to disabled input type number scrolling
        $(':input[type=number]').on('mousewheel',function(e){ $(this).blur(); });

        attachEvents();
        $(".select2").val('').trigger('change');
        $(".select2").select2({
            allowClear: true,
            placeholder: "@lang('product-management.reject_product_placeholder_reason')",
            tags: true,
        });
        $('#channel-filter').change(function() {    
            var item=$(this);
            //console.log(item.val())
            if(item.val() > 0){
                $('.chnl-td').hide();
                $('.chnl-id-'+item.val()).show();
            }else{
                $('.chnl-td').show();
            }
        });
        $("#submit-reject").click(function() {
            $('.form-group').removeClass('has-error has-warning').find('.help-block').html('');
            var error = false;
            var qtyArray = [];
            var reasonArray = [];
            $("#reject-form .qty-input").each(function() {
                var qtyInput = $(this);
                var qtyValue = qtyInput.val();
                var reasonDropdown = qtyInput.closest('tr').find('.reason-select');
                //console.log(qtyValue);
                if(qtyValue != ""){
                    qtyArray.push(qtyValue);
                    // check if qty exceeds allowed qty
                    if(parseInt(qtyValue) > parseInt(qtyInput.attr('max'))){
                        qtyInput.closest('.form-group').addClass('has-error').find('.help-block').html('@lang('product-management.reject_product_error_exceed_qty')');
                        qtyInput.closest('.product-content').prev('.accordion-hide').trigger('click');
                        qtyInput.focus();
                        error = true;
                    }else if(parseInt(qtyValue) < 1){
                        qtyInput.closest('.form-group').addClass('has-error').find('.help-block').html('@lang('product-management.reject_product_error_qty_less_than_1')');
                        qtyInput.closest('.product-content').prev('.accordion-hide').trigger('click');
                        qtyInput.focus();
                        error = true;
                    }
                    // if contains value, check if reason is selected
                    if(reasonDropdown.val() === null){
                        reasonDropdown.closest('.form-group').addClass('has-error').find('.help-block').html('@lang('product-management.reject_product_error_missing_reason')');
                        reasonDropdown.closest('.product-content').prev('.accordion-hide').trigger('click');
                        reasonDropdown.focus();
                        error = true;
                    }
                }else if(reasonDropdown.val() !== null){
                    qtyInput.closest('.form-group').addClass('has-error').find('.help-block').html('@lang('product-management.reject_product_error_missing_qty')');
                    qtyInput.closest('.product-content').prev('.accordion-hide').trigger('click');
                    qtyInput.focus();
                    error = true;
                }
            });
            if(qtyArray.length < 1 && !error){
                error = true;
                alert("Please reject at least one SKU.");
            }
            if(!error){
                $('#reject-form').submit();
            }
        });
    });
</script>
@append