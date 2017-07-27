@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS',false)) }}"></script>
@append

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
                        <h3 class="box-title">@lang('product-management.box_header_print_barcode')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        {!! Form::open(['name'=>'form','method'=>'POST', 'route'=>'inventory.download_barcode']) !!}
                        <button type="submit" class="btn btn-default"><i class="fa fa-print"> Download Barcode</i></button>
                        
                            <div class="form-group">
                                <table class="table">
                                    <thead>
                                    <th></th>
                                    <th>HW SKU</th>
                                    <th>Supplier SKU</th>
                                    <th>Product</th>
                                    <!-- <th>Unit Cost</th>
                                    <th>Retail Price</th>
                                    <th>Retail Price w/o GST</th> -->
                                    <th>Colour</th>
                                    <th>Size</th>
                                    <th>Copies</th>
                                    </thead>
                                    <tbody>
                                        <?php $i=0; ?>
                                        @foreach($products as $product)
                                        <tr>
                                            <td>{{++$i}}</td>
                                            <td>{!!Form::hidden("items[$i][hubwire_sku]",$product->hubwire_sku)!!}{{$product->hubwire_sku}}</td>
                                            <td>{!!Form::hidden("items[$i][supplier_code]",$product->sku_supplier_code)!!}{{$product->sku_supplier_code}}</td>
                                            <td>{!!Form::hidden("items[$i][name]",$product->name)!!}{!!Form::hidden("items[$i][brand_name]",$product->brand_name)!!}{{$product->name}}</td>

                                            <!-- <td>{!!Form::hidden("items[$i][unit_cost]",'')!!}</td>
                                            <td>{!!Form::hidden("items[$i][retail]",$product->channel_sku_price)!!}{{$product->channel_sku_price}}</td>
                                            <td>{!!Form::hidden("items[$i][retail2]",number_format($product->channel_sku_price/1.06,2))!!}{{(number_format($product->channel_sku_price/1.06,2))}}</td> -->
                                            <td>{!!Form::hidden("items[$i][colour]",$product->colour)!!}{!!$product->colour!!}</td>
                                            <td>{!!Form::hidden("items[$i][size]",$product->size)!!}{!!$product->size!!}</td>
                                            <td align="center">{!! Form::number("items[$i][copies]", $product->channel_sku_quantity, ['class' => 'form-control', 'placeholder' => 'Print Copies'] ) !!}</td>
                                        </td>
                                        @endforeach
                                    </tbody>
                                 </table>   
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
    $(document).ready(function() {
       
    });
</script>
@append