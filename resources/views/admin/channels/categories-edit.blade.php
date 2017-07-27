@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('admin/channels.page_title_categories_update')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/channels.content_header_channels')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! session('success') !!}
                    </div>
                @endif


                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">@lang('admin/channels.box_header_categories_manage') for {{$channel_type->name}}</h3>
                        <div class="pull-right">
                            <input type="hidden" name="channel_type_id" value="{{$channel_type->id}}" />
                            <button type="button" id="btn-update-category" class="btn btn-default">@lang('admin/channels.button_update_categories')</button>
                            <button type="button" class="btn btn-default" onclick="window.location.href='{{route('admin.categories.download_products_with_outdated_category', [$channel_type->id])}}'"><i class="fa fa-download"></i>&nbsp;&nbsp;@lang('admin/channels.button_download_products_with_outdated_category')</button>
                            <button type="button" id="toggle-categories" class="btn btn-default">@lang('admin/channels.button_hide_categories')</button>
                        </div>
                    </div><!-- /.box-header -->
                

                    <div class="box-body">
                        <div id="categories-container">
                            <table id="categories" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Category ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $name=>$id)
                                    @if($id != 0)
                                        <tr>
                                            <td>{{$name}}</td>
                                            <td>{{$id}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
<style type="text/css">
    .btn-link {
        padding: 0;
        font-size: 13px;
    }
    table.dataTable thead > tr > th{
        padding-right: 20px;
    }
</style>

<script>
    $(document).ready(function() {
        $("#categories").DataTable();

        $("#toggle-categories").click(function() {
            $(this).text($("#categories-container").is(":visible") ? "@lang('admin/channels.button_display_categories')" : "@lang('admin/channels.button_hide_categories')");
            $("#categories-container").toggleClass("hide");
        });

        deleteMapping();

        // Prompt update category confirmation
        $(document).on('click', '#btn-update-category', function (e) {
            if(confirm('Are you sure you want to update?')){
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.categories.update", $channel_type->id) }}',
                    beforeSend: function() {
                        waitingDialog.show('Updating categories...', {dialogSize: 'sm'});
                    },
                    success: function(response) {
                        if(response.success) {
                            alert('Categories have been updated successfully.');
                            $('div.box-body').remove();
                            $('div.box').append(response.mapHtml);
                            $("#categories").DataTable();

                            addMapping();
                            saveMapping();
                        }
                    },
                    complete: function() {
                        waitingDialog.hide();        
                    },  
                });
            }
        });
    });

    function deleteMapping() {
        $(".delete").on('click', function() {
            $(this).closest('div.map').remove();

            $('div.map').each(function(index) {
                $(this).children('span').html(index+1);
            });
        });
    }

    function addMapping() {
        $("#add-btn").on('click', function() {
            $('.btn-div').before($('<div class="map"><span style="width:14px; display:inline-block; text-align:right">' + ($('div.map').length+1) +'</span>. <input type="text" /> to <input type="text" /> <a href="javascript:void(0)" class="delete">delete</a></div>'));

            $(".delete").off('click');
            deleteMapping();
        });
    }

    function saveMapping() {
        // Prompt remap category confirmation
        $(document).on('click', '#btn-remap-category', function (e) {
            if(confirm('Are you sure you want to save?')){
                var data = [];
                $('div.map').each(function(index) {
                    var from = $(this).children('input').eq(0).val().trim();
                    var to = $(this).children('input').eq(1).val().trim();

                    if(from.length > 0 && to.length > 0) {
                        data.push({
                            "from" : from,
                            "to" : to
                        });
                    }
                });

                $('input[name=data]').val(JSON.stringify(data));
                $('#remap-category-form').submit();
            }
        });
    }
</script>
@append