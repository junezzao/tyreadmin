@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('product-management.page_title_product_mgmt_transfer_show')
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
                        <div class="col-xs-8">
                            <h3 class="box-title">@lang('product-management.box_header_transfer_show') #{{$id}}</h3>
                        </div>
                        <div class="col-xs-4">
                            <h3 class="box-title pull-right">{{strtoupper(Config::get('globals.stock_transfer.statuses')[$stockTransfer->status])}}</h3>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-xs-12 to-print-1">
                            <div class="col-xs-6">
                                <div class="row">
                                    <label class="col-xs-4" for="do_type">@lang('product-management.transfer_form_label_do_type'): </label>
                                    <div class="col-xs-8">
                                        <p>{{Config::get('globals.stock_transfer.do_type')[$stockTransfer->do_type]}} {{($stockTransfer->do_type == 0)?'('.$stockTransfer->batch_id.')':''}}
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-xs-4" for="created_date">@lang('product-management.transfer_form_label_initiated_date'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->created_at}}
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-xs-4" for="recieved_date">@lang('product-management.transfer_form_label_received_date'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->receive_at}}
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-xs-4 control-label" for="merchant">@lang('product-management.transfer_form_label_merchant'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->merchant->name}}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-xs-4" for="origin_channel">@lang('product-management.transfer_form_label_origin_channel'): </label>
                                    <div class="col-xs-8">
                                        <p>{{!empty($stockTransfer->originating_channel->name)?$stockTransfer->originating_channel->name:'-'}}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-xs-4 control-label" for="target_channel">@lang('product-management.transfer_form_label_target_channel'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->target_channel->name}}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_pic'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->person_incharge}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_remarks'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->remarks}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="row">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_transport_co'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->transport_co}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_lorry_no'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->lorry_no}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_driver_name'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->driver_name}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-xs-4 control-label" for="remarks">@lang('product-management.transfer_form_label_driver_id'): </label>
                                    <div class="col-xs-8">
                                        <p>{{$stockTransfer->driver_id}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            @if ($stockTransfer->status==1 && $admin->can('edit.stocktransfer'))
                                @if($stockTransfer->do_type!=2)
                                {!! Form::open(array('url' => route('products.stock_transfer.receive', $id), 'method' => 'POST')) !!}
                                    <button type="submit" class="stock-transfer options btn btn-default pull-right">@lang('product-management.button_receive')</button>
                                {!! Form::close() !!}
                                @endif
                            
                            @elseif ($stockTransfer->status==0 && $admin->can('edit.stocktransfer'))
                                @if($stockTransfer->do_type!=2)
                                <a href="{{route('products.stock_transfer.edit', $id)}}" style="margin-left:10px;" class="btn btn-default pull-right">Edit</a>
                                @endif

                                {!! Form::open(array('url' => route('products.stock_transfer.transfer', $id), 'method' => 'POST')) !!}
                                    <button type="submit" class="stock-transfer options btn btn-default pull-right">@lang('product-management.button_transfer')</button>
                                {!! Form::close() !!}

                            @elseif ($stockTransfer->status==0 && $admin->can('delete.stocktransfer'))
                                {!! Form::open(array('url' => route('products.stock_transfer.destroy', $id), 'method' => 'DELETE')) !!}   
                                    <button type="submit" class="stock-transfer options btn btn-default pull-right">@lang('product-management.button_delete')</button>   
                                {!! Form::close() !!}
                            @endif

                            @if ($stockTransfer->status!=0)
                                <button id="print" class="stock-transfer btn btn-default pull-right">@lang('product-management.button_print')</button>
                                @if($stockTransfer->do_type == 2)
                                <a title="Go to manifest" href="{{URL::route('products.manifests.show', [$stockTransfer->manifest->id])}}" class="btn btn-default pull-right" style="margin-left: 10px; margin-right:5px;">Manifest ({{$stockTransfer->manifest->status}})</a>
                                <a title="Download Manifest" href="{{URL::route("products.stock_transfer.export", [$stockTransfer->id])}}" class="btn btn-black pull-right" style="margin-left: 5px;margin-right: 5px;"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @endif
                            @endif                        
                        </div>
                        <div class="col-xs-12 to-print-2">
                            <table id="items" width="100%" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        @if($stockTransfer->do_type == 2)
                                        <th>@lang('product-management.transfer_form_label_channel')</th>
                                        @endif
                                        <th>@lang('product-management.transfer_form_label_system_sku')</th>
                                        <th>@lang('product-management.transfer_form_label_hw_sku')</th>
                                        <th>@lang('product-management.transfer_form_label_prefix')</th>
                                        <th>@lang('product-management.transfer_form_label_product')</th>
                                        <th>@lang('product-management.transfer_form_label_options')</th>
                                        <th>@lang('product-management.transfer_form_label_tags')</th>
                                        <th>@lang('product-management.transfer_form_label_quantity')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            @if($stockTransfer->do_type == 2)
                                            <td>{{$item->channel_sku->channel->name}}</td>
                                            @endif
                                            <td>{{$item->channel_sku->sku_id}}</td>
                                            <td>{{$item->channel_sku->sku->hubwire_sku}}</td>
                                            <td>{{$item->channel_sku->product->brand}}</td>
                                            <td>{{$item->channel_sku->product->name}}</td>
                                            <td>{!! $item->options !!}</td>
                                            <td>{{$item->tags}}</td>
                                            <td>{{$item->quantity}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>          
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="jsForPrintWindow" class="hide">
        $(document).ready(function(){
            setTimeout( function() {
                window.print();
            }, 200);
        });
    </div>
    <div id="cssForPrintWindow" class="hide">
        .noPrint { 
            display:none; 
        }
        td {
            font-size:12px;
        }
        tr {
            font-size:14px;
        }
        table {
            line-height: 12px;
        }
    </div>
@stop

@section('footer_scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var table = $('#items').DataTable({
            "sDom": 't',
            "order": [[0, "asc"]],
            "pageLength": -1,
            "fnDrawCallback": function (o) {
                jQuery(window).scrollTop(0);
            }
        });

        // Print table Css and Js
        var css = '<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">';
        //var tableCss = '<link href="/packages/datatables/datatables.min.css" rel="stylesheet" type="text/css">';
        var printCss = '<style>' + $("#cssForPrintWindow").text() +' <\/style>';
        var jquery = '<script src="{{ asset("plugins/jQuery/jQuery-2.1.4.min.js") }}"><\/script>';
        var datatables = '<script src="{{ asset("plugins/datatables/jquery.dataTables.min.js")}}"><\/script>';
        var js = "<script type='text/javascript'>" + $("#jsForPrintWindow").text() + "<\/script>";

        $("#print").click(function() {  
            
            var header = $('.box-header').html();
            var info = '<div class="col-xs-12">'+$('.to-print-1').html()+'</div>';
            var table = $('.to-print-2').html();
            var printWindow = window.open();
            printWindow.document.write(css + printCss //+ tableCss 
                + jquery + datatables + js + header + info + table);
            printWindow.document.close();
        });

        $(document).on('click', '.options', function (e) {
            e.preventDefault();
            var c = confirm('Are you sure you want to perform this action? This cannot be undone.');
            if(c){
                $(this).parent().submit();
            }
        });
    });
</script>
@append