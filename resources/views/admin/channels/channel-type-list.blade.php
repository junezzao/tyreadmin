@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('admin/channels.page_title_channels')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('admin/channels.content_header_channels')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content channels channel-type-page channel-list">
        <div class="row">
            <div class="col-xs-12">
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! session('success') !!}
                    </div>
                    @elseif(Session::has('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! session('error') !!}
                    </div>
                @endif
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('admin/channels.box_header_channel_types')
                        </h3>
                        @if($user->can('create.channeltype'))
                        <buttton type="button" id="new_channel" class="btn btn-default pull-right">
                            @lang('admin/channels.button_add_new_channel_type')
                        </button>
                        @endif
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table id="channels_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:8%">
                                    @lang('admin/channels.channel_type_table_id')
                                </th>
                                <th style="width:15%">
                                    @lang('admin/channels.channel_type_table_name')
                                </th>
                                <th style="width:15%">
                                    @lang('admin/channels.channel_type_table_status')
                                </th>
                                <th style="width:15%">
                                    @lang('admin/channels.channel_type_table_channel_count')
                                </th>
                                <th style="width:15%">
                                    @lang('admin/channels.channel_type_table_updated_at')
                                </th>
                                <th style="width:20%">
                                    @lang('admin/channels.channel_type_table_action')
                                </th>
                            </tr>
                            <tr>
                                <td style="width:8%"></td>
                                <td style="width:15%" data-index="1" class="search-col-text">
                                    @lang('admin/channels.channel_type_table_name')
                                </td>
                                <td style="width:15%" data-index="2" class="search-col-dd"></td>
                                <td style="width:15%"></td>
                                <td style="width:15%"></td>
                                <td style="width:20%"></td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">

<script type="text/javascript">
jQuery(document).ready(function(){
    $("#new_channel").click(function() {
        window.location.href = "{!! route('admin.channel-type.create') !!}";
    });

    $(document).on('click', "button.edit", function(e) {
        window.location.href = "{{route('admin.channel-type.edit', ['replaceme'])}}".replace("replaceme", $(this).data('id'));
    });

    $(document).on('click', '.confirmation', function (e) {
        //e.preventDefault();
        return confirm('Are you sure you want to '+$(this).text().toLowerCase()+' this channel type?');
    });
    //Custom search columns
    // Setup - add a text input to each the header cell
    $('#channels_table thead tr td.search-col-text').each(function(index, value){
        var title = $(this).text();
        $(this).html('<input id="search-col-'+$(this).data('index')+'" style="width:100%" type="text"/>');
    } );

    var table = jQuery('#channels_table').DataTable({
        "dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
        "ajax": '{!! route('admin.channel-type.table_data') !!}',
        "lengthMenu": [[30, 50, 100], [30, 50, 100]],
        "pageLength": 30,
        "order": [[4, "desc"]],
        "scrollX": true,
        "scrollY": false,
        "autoWidth": false,
        "orderCellsTop": true,
        "fnDrawCallback": function (o) {
            jQuery(window).scrollTop(0);
        },
        "columns": [
            { "data": "id", "name": "id", "targets": 0 },
            { "data": "name", "name": "name", "targets": 1 },
            { "data": "status", "name": "status", "targets": 2 },
            { "data": "channel_count", "name": "channel_count", "targets": 3 }, 
            { "data": "updated_at", "name": "updated_at", "targets": 4},
            { "data": "actions", "name": "actions", "targets": 5, "orderable": false}
        ],
        // to initialize drop down filter
        initComplete: function(){
            this.api().columns().every(function(){
                var column = this;
                if(column.index() == 2){
                    var select = $('<select class="form-control"><option value="">All</option></select>')
                        .appendTo($('.dataTable thead tr td:nth-child(' + 3 + ')').first().empty())
                        .on('change', function(){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column.search(val ? '^'+val+'$' : '', true, false).draw();
                        });
     
                    column.data().unique().sort().each(function(d, j){
                        select.append('<option value="'+d+'">'+d+'</option>')
                    });
                }
            });
        },
    });

    // Apply the search
    table.columns().every(function (){
        var that = this;
        $('#search-col-'+this.index()).on('keyup change', function (){
            if (that.search() !== this.value){
                that
                    .search(this.value)
                    .draw();
            }
        });
    });

    jQuery('#channels_table').offset().top;
});
</script>
@append