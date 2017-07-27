@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_mgmt_delete')
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
                        <h3 class="box-title">@lang('product-management.box_header_delete')</h3>
                        <span style="float:right">
                            <a class="btn bg-purple margin" role="button" href="{{ route('products.inventory.index', ['pid'=>implode(',', $product_ids)]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
                        </span>
                    </div><!-- /.box-header -->
                    <div class="box-body bulk-reject-page">
                        <div class="col-xs-3 pull-right">
                            @if(isset($channels))
                                {!! Form::select( 'filter', $channels, null, ['class' => 'form-control', 'placeholder' => trans('product-management.reject_product_placeholder_channel'), 'id' => 'channel-filter'] ) !!}
                            @endif
                        </div>
                        <form id="delete-form" method="POST" action="{{route('inventory.delete.store')}}">
                            <input type="hidden" name="original-products" value="{{ $original_products }}" />
                            <input type="hidden" name="product_ids" value="{{ implode(',', $product_ids) }}"/>
                            <div class="col-xs-12">
                                @foreach($products as $productId => $product)
                                    <div class="product-accordion">
                                        <div class="product-title accordion-display row">
                                            <div class="col-xs-12">
                                                <h4>{{$product['name']}}</h4>
                                            </div>
                                            <div class="col-xs-8 form-group">
                                                {!! Form::text('reason-delete['.$productId.']', null, array('class' => 'form-control reason-delete', 'placeholder' => trans('product-management.delete_product_placeholder_reason'))) !!}
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="col-xs-3">
                                                <label style="line-height:29px">{!! Form::checkbox('reason-apply-all', 1, null) !!} @lang('product-management.delete_product_checkbox_reason_apply_to_all')</label>
                                                <button style="line-height:29px; color:#f00!important" type="button" title="Remove" class="close alert-danger remove-product"><i class="fa fa-minus-circle"></i></button>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="width:25%">@lang('product-management.reject_product_table_label_hw_sku')</th>
                                                        <th>@lang('product-management.reject_product_table_label_channel')</th>
                                                        <th style="width:20%">@lang('product-management.reject_product_table_label_qty')</th>
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
                                    <a class="btn bg-purple margin" role="button" href="{{ route('products.inventory.index', ['pid'=>implode(',', $product_ids)]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
                                    <button id="submit-delete" type="button" class="btn btn-info">@lang('product-management.reject_product_btn_submit')</button>
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
        attachEvents();
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
        $("#submit-delete").click(function() {
            $('.form-group').removeClass('has-error has-warning').find('.help-block').html('');
            var error = false;
            $("#delete-form input.reason-delete").each(function() {
                var reason_delete_ele = $(this);
                var reason_delete = reason_delete_ele.val();
                if(reason_delete.length <= 0) {
                    reason_delete_ele.closest('.form-group').addClass('has-error').find('.help-block').html("Reason of deleting must be specified.");
                    error = true;
                }
            });
            if(!error){
                $('#delete-form').submit();
            }
        });

        $('input.reason-delete').on('click', function(event){
            event.stopPropagation();
        }); 
        $('input[name="reason-apply-all"]').on('click', function(event){
            event.stopPropagation();
            $('input[name="reason-apply-all"]').not(this).prop('checked', false);

            if($(this).is(":checked")) {
                var reason_delete = $(this).closest('.product-accordion').find('input.reason-delete').val();
                $('input.reason-delete').val(reason_delete);
            }
        });
        $('button.remove-product').on('click', function(event){
            event.stopPropagation();
            if(confirm('Proceed to remove this product from the list?')) {
                $(this).closest('.product-accordion').remove();
            }
        });
    });
</script>
@append