@extends('layouts.master')

@section('title')
    @lang('product-management.page_title_product_mgmt_create_create')
@stop

@section('header_scripts')

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
                        <h3 class="box-title">@lang('product-management.box_header_create_create')</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body create-restock create-restock-create-page">
                        {!! Form::open(array('id' => 'create_product_form', 'url' => route('products.create.store'), 'method' => 'POST')) !!}
                            <div class="col-xs-12">
                                <div class="col-xs-6">
                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="merchandiser">@lang('product-management.create_form_label_merchandiser')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('merchandiser', $merchandiser, null, array('class' => 'form-control select2', 'placeholder' => trans('product-management.create_form_placeholder_merchandiser'))) !!}
                                            <div class="error">{{ $errors->first('merchandiser') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="batch_date">@lang('product-management.create_form_label_batch_date')</label>
                                        <div class="col-xs-9">
                                            {!! Form::text( 'batch_date', null, ['class' => 'form-control', 'placeholder' => trans('product-management.create_form_placeholder_batch_date'), 'id' => 'batch_date_input'] ) !!}
                                            <div class="error">{{ $errors->first('batch_date') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="merchant">@lang('product-management.create_form_label_merchant')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('merchant', $merchant, null, array('class' => 'form-control select2', 'id' => 'merchant_dropdown', 'placeholder' => trans('product-management.create_form_placeholder_merchant'))) !!}
                                            <div class="error">{{ $errors->first('merchant') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="channel">@lang('product-management.create_form_label_supplier')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('supplier', array(), null, array('class' => 'form-control select2', 'id' => 'supplier_dropdown', 'placeholder' => trans('product-management.create_form_placeholder_supplier'))) !!}
                                            <div class="error">{{ $errors->first('supplier') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3 control-label required" for="channel">@lang('product-management.create_form_label_channel')</label>
                                        <div class="col-xs-9">
                                            {!! Form::select('channel', array(), null, array('class' => 'form-control select2', 'id' => 'channel_dropdown', 'placeholder' => trans('product-management.create_form_placeholder_channel'))) !!}
                                            <div class="error">{{ $errors->first('channel') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label class="col-xs-3" for="remarks">@lang('product-management.create_form_label_remarks')</label>
                                        <div class="col-xs-9">
                                            {!! Form::textarea( 'remarks', null, ['class' => 'form-control', 'placeholder' => trans('product-management.create_form_placeholder_remarks')] ) !!}
                                            <div class="error">{{ $errors->first('remarks') }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12">@lang('product-management.create_label_price_notice')</label>
                                    </div>
                                </div>
                                
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <div class="form-group has-feedback pull-right">
                                            <button type="button" id="download-create-sheet" class="btn btn-default">@lang('product-management.button_download_create_template')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group pull-right">
                                    <div class="col-xs-6">
                                        <button data-toggle="modal" data-target="#uploadModal" type="button" id="btn_upload_create" class="btn btn-default">@lang('product-management.button_upload_create_sheet')</button>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}

                        {!! Form::open(array('id'=>'download-csv', 'url' => route('products.download.csv'), 'method' => 'POST')) !!}
                            <input type="hidden" name="link" value="{{ $templateUrl }}">
                            <input type="hidden" name="filename" value="new_product_form">
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="uploadModal" aria-labelledby="uploadModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Upload Create Product Sheet</h4>
                </div>
                <div class="modal-body">
                    <div id="log" class="alert alert-danger" role="alert" style="display: none;">
                    </div>
                    <span class="upload-form">
                        <label for="file_upload">Upload File</label>
                        <br/>
                        <input id="file_upload" type="file" name="product_sheet">
                        <p class="help-block">Upload your create product sheet here.</p>
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
@stop


@section('footer_scripts')
<link href="{{ asset('packages/blueimp/css/jquery.fileupload.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/jquery_ui_widgets.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.iframe-transport.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.fileupload.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<link href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        // setup array for channel type
        var channelTypes = [];
        @foreach($channelTypes as $id => $channelType)
            channelTypes[{{ $id }}] = '{{ $channelType }}';
        @endforeach

        // initialize date picker
        $('#batch_date_input').datepicker({
            format :  'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        $("optgroup[label='Inactive'] option").attr("disabled", "disabled");

        $(".select2").select2();

        // attach event to merchant dropdown change to populate supplier dropdown
        $('#merchant_dropdown').change(function(){
            // clear supplier dropdown
            $('#supplier_dropdown').find('optgroup').remove();
            $("#supplier_dropdown").trigger("change");
            // clear channel dropdown
            $('#channel_dropdown').find('optgroup').remove();
            $("#channel_dropdown").trigger("change");
            // get list of channels based on merchant
            if($(this).val() > 0){
                // display loading prompt
                waitingDialog.show('Getting supplier and channel list...', {dialogSize: 'sm'});
                var hideModal = false;
                $.ajax({
                    url: "/admin/suppliers/merchant/"+$(this).val(),
                    type: 'GET',
                    success: function(data) {
                        if(data==''){
                            // show placeholder
                            $('#supplier_dropdown').append('<option>N/A</option>');
                        }else{
                            // loop thru response and build new select options
                            var supplierOptions = '';
                            var inactiveSupplierOptions = '';
                            $.each(data, function( index, supplier ) {
                                if(supplier.active == true){
                                    supplierOptions += '<option value="'+supplier.id+'">';
                                    supplierOptions += supplier.name;
                                    supplierOptions += '</option>';
                                }else{
                                    inactiveSupplierOptions += '<option value="'+supplier.id+'" disabled="disabled">';
                                    inactiveSupplierOptions += supplier.name;
                                    inactiveSupplierOptions += '</option>';
                                }
                            });
                            if(supplierOptions != '')
                                $('#supplier_dropdown').append('<optgroup label="Active">'+supplierOptions+'</optgroup>');
                            if(inactiveSupplierOptions != '')
                                $('#supplier_dropdown').append('<optgroup label="Inactive">'+inactiveSupplierOptions+'</optgroup>');
                            $("#supplier_dropdown").trigger("change");
                        }
                    },
                    complete: function(){
                        if(hideModal)
                            waitingDialog.hide();
                        else
                            hideModal = true;;
                    }
                });
                $.ajax({
                    url: "/admin/channels/merchant/"+$(this).val()+"/channel_type/{{$warehouseTypeId}}",
                    type: 'GET',
                    success: function(data) {
                        if(data==''){
                            // show placeholder
                            $('#channel_dropdown').append('<option>N/A</option>');
                        }else{
                            // loop thru response and build new select options
                            var inactiveChannelOptions = '';
                            var channelTypeOptions = new Array;
                            $.each(data, function( index, channel ) {
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
                            $.each(channelTypeOptions, function( index, channelOptions ){
                                if(channelOptions !== undefined)
                                    $('#channel_dropdown').append('<optgroup label="'+channelTypes[index]+'">'+channelOptions+'</optgroup>');
                            });
                            if(inactiveChannelOptions != '')
                                $('#channel_dropdown').append('<optgroup label="Inactive">'+inactiveChannelOptions+'</optgroup>');
                            
                            $('#channel_dropdown').trigger("change");
                        }
                    },
                    complete: function(){
                        if(hideModal)
                            waitingDialog.hide();
                        else
                            hideModal = true;
                    }
                });
            }
        });

        // For uploading file
        $('#file_upload').fileupload({
            url: '/products/create/upload',
            dataType: 'json',
            add: function (e, data) {
                $('#log').empty();
                $('#log').hide();
                $('#progress').addClass('active');
                $('#progress').css('width', '0%');
                data.formData = $("#create_product_form").serializeArray();     
                data.submit();
            },
            done: function (e, data) {
                var result = data.result;
                $('#progress').removeClass('active');
                //console.log(result);
                
                if(result.success){
                    if(result.existed){

                    }
                    else{
                        $('<p/>').text('Create product batch created successfully! Now redirecting to the edit page...').appendTo('#log');
                        $('#log').removeClass('alert-danger').addClass('alert-success').show();
                        //console.log(result.data.items);
                        //updateProductsTable(result.data.items);
                        //location.reload(result.redirect);
                        window.location.replace(result.redirect);
                    }
                }
                else{
                    $('#log').empty();
                    $('<p/>').html('<h4>Upload process return error(s):-</h4>').appendTo('#log');
                    //console.log(result.error.messages);
                    if (result.error.messages!==undefined) {
                        $.each(result.error.messages, function (index, message){
                            // console.log(index);
                            $('#log').append('<p>'+message+'</p>');
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

        $("#download-create-sheet").click(function() {
            $("#download-csv").submit();
        });
    });
</script>
@append