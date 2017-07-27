@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
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
                        <h3 class="box-title">@lang('product-management.box_header_bulk_update')</h3>
                        <span style="float:right">
                            <a class="btn bg-purple margin" role="button" href="{{ route('products.inventory.index', ['pid'=>implode(',', $product_ids)]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
                        </span>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <p style="display:none;">
                        <!-- <button name="save" id="save">Save</button> -->
                        <label><input type="checkbox" name="autosave" id="autosave" checked="checked" autocomplete="off"> Autosave</label>
                        </p>
                        <form class="form-inline">
                            <div class="form-group">
                                <label for="exampleInputName2">Hide Fields: </label>
                            </div>
                        </form>

                        <pre id="example1console" class="bg-info text-info">Loading data...</pre>

                        <div id="bulk-update-table"></div>
                        <div style="text-align:right">
                            <a class="btn bg-purple margin" role="button" href="{{ route('products.inventory.index', ['pid'=>implode(',', $product_ids)]) }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back</a>
                        </div>
                    </div>                        
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<script src="{{ asset('plugins/handsontable/handsontable.full.js',env('HTTPS',false)) }}"></script>
<link href="{{ asset('plugins/handsontable/handsontable.full.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/bulk_update.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script type="text/javascript">
    $(document).ready(function() {
        var
          getID = function(id) {
              return document.getElementById(id);
          },
          container = getID('bulk-update-table'),
          exampleConsole = getID('example1console'),
          autosave = getID('autosave'),
          save = getID('save'),
          autosaveNotification,
          hot;

        <?php
            $js_array = json_encode($product_ids);
            echo "var products = ". $js_array . ";\n";

            $js_categories = json_encode($categories);
            echo "var categories = ". $js_categories . ";\n";
        ?>

        var colsToHide = [];
        var hideByDefault = [];

        columnHeaders = ["Product Name", "Category ID","Category", "Channel Name", "System SKU", "Hubwire SKU", "Supplier SKU", "Qty", "Retail Price", "Listing Price", "Sales Start", "Sales End", "Warehouse Coordinates", "SKU Weight", "Tags", "Options"];
        cols = [{data: "name", type: 'text', width: "150"},
                {data: "category_id", type:'text', readOnly:true},
                {
                    data: "category_name", 
                    type: 'autocomplete',
                    strict: true,
                    allowInvalid: false,
                    visibleRows: 10,
                    width: "150",
                    source: categories
                },
                {data: "channel_name", type: 'text', readOnly: true, width: "100"},
                {data: "sku_id", type: 'text', readOnly: true},
                {data: "hubwire_sku", type: 'text', readOnly: true},
                {data: "sku_supplier_code", type: 'text'},
                {data: "channel_sku_quantity", type: 'numeric', format: '0', readOnly: true},
                {data: "channel_sku_price", type: 'numeric', format: '0.00'},
                {data: "channel_sku_promo_price", type: 'numeric', format: '0.00'},
                {data: "promo_start_date", type: 'date', dateFormat: 'YYYY-MM-DD'},
                {data: "promo_end_date", type: 'date', dateFormat: 'YYYY-MM-DD'},
                {data: "channel_sku_coordinates", type: 'text'},
                {data: "sku_weight", type: 'numeric', format: '0'},
                {data: "tags", type: 'text', width: "150"},
                {data: "options", type: 'text', readOnly: true, renderer: "html"}];

        $.each(columnHeaders, function(key, val) {
            htmlString = '';
            if (hideByDefault.indexOf(val)>-1) {
                htmlString = '<div class="checkbox"><label>'+
                        '<input type="checkbox" class="hide-fields" data-name="'+val+'" checked> '+val+
                        '&nbsp;&nbsp;</label></div>';
                colsToHide.push(val);
            }
            else {
            htmlString = '<div class="checkbox"><label>'+
                        '<input type="checkbox" class="hide-fields" data-name="'+val+'"> '+val+
                        '&nbsp;&nbsp;</label></div>';
            }

            $(".form-inline").append(htmlString);
        });

        function updateColumns() {
            var newCols = [];
            var newColHeaders = [];

            $.each(columnHeaders, function(key, val){
                if (colsToHide.indexOf(val) === -1) {
                    newCols.push(cols[key]);
                    newColHeaders.push(columnHeaders[key]);
                }
            });

            hot.updateSettings({
                columns: newCols,
                colHeaders: newColHeaders
            });
        }

        $(".hide-fields").change(function() {
            var fieldname = $(this).data('name');

            if($(this).is(":checked")) {
                colsToHide.push(fieldname);
            }
            else {
                colsToHide = $.grep(colsToHide, function(value) {
                    return value != fieldname;
                });
            }
            updateColumns();
        });

        hot = new Handsontable(container, {
            startCols: columnHeaders.length,
            colHeaders: columnHeaders,
            rowHeaders: true,
            manualColumnResize: true,
            manualRowResize: true,
            fixedColumnsLeft: 1,
            contextMenu: true,
            height: '600',
            stretchH: "all",
            columns: cols,
            afterChange: function (change, source) {
                if (!autosave.checked) {
                    return;
                }
                if (source === 'loadData') {
                    return;
                }
                // modify change variable to include product id
                $.each(change, function(key, val) {
                    dataset = hot.getSourceDataAtRow(val[0]);
                    change[key][0] = dataset.id+","+dataset.sku_id+","+dataset.channel_sku_id;
                });
                //console.log(change);
                clearTimeout(autosaveNotification);
                $.ajax({
                    type: "POST",
                    url: '{{route("inventory.bulk_update.save")}}',
                    data: {data: change},
                    dataType: 'json',
                    beforeSend: function() {
                        $(exampleConsole).html("Saving your changes. Please wait...");
                        exampleConsole.className = 'bg-info text-info';
                    },
                    success: function(response) {
                        //console.log(response);

                        if (response.success) {
                            loadData();
                            $(exampleConsole).html(response.message);
                            exampleConsole.className = 'bg-success text-success';
                        }
                        else {
                            $(exampleConsole).html(response.error);
                            exampleConsole.className = 'bg-danger text-danger';
                        }
                    },
                    error: function(response) {
                        exampleConsole.innerText  = 'An error has occurred while autosaving your changes. Please try again.';
                        exampleConsole.className = 'bg-danger text-danger';
                    }
                });
            }
        });

        // load data via ajax
        function loadData() {
            $.ajax({ //loads data to Handsontable
                url: '{{route("inventory.bulk_update.load")}}',
                dataType: 'json',
                data: {product_ids: products},
                type: 'POST',
                success: function(res){
                    if (res.success) {
                        hot.loadData(res.data);
                        hot.updateSettings({
                            startRows: res.data.length
                        });
                        exampleConsole.className = 'bg-info text-info';
                        exampleConsole.innerText = 'Changes will be autosaved.';
                    }
                    else {
                        exampleConsole.className = 'bg-danger text-danger';
                        exampleConsole.innerText  = res.error;
                    }
                },
                error: function(response) {
                    exampleConsole.className = 'bg-danger text-danger';
                    exampleConsole.innerText  = 'An error has occurred while loading data from the server. Please try again.';
                }
            });
        }
        loadData();
    });
</script>
@append