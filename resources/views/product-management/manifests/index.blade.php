@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
    @lang('admin/fulfillment.page_title_manifest_list')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="errors"></div>
    <section class="content-header">
        <h1>@lang('product-management.content_header_manifest_list')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('product-management.box_title_manifest_list')
                        </h3>
                        
                    </div><!-- /.box-header -->
                    <div class="box-body">                      
                        <div class="col-xs-12">
                            <table id="manifests-table" width="100%" class="table table-striped" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('admin/fulfillment.manifest_table_id')}}</th>              
                                    <th>{{trans('admin/fulfillment.manifest_table_status')}}</th>
                                    <th>{{trans('admin/fulfillment.manifest_table_attended_by')}}</th>
                                    <th>{{trans('admin/fulfillment.manifest_table_created_at')}}</th>
                                    <th>{{trans('admin/fulfillment.manifest_table_created_by')}}</th>
                                    <th>{{trans('admin/fulfillment.manifest_table_picked_up')}}</th>                  
                                    <th>{{trans('admin/fulfillment.manifest_table_completed_at')}}</th>
                                    <th>{{trans('admin/fulfillment.manifest_table_actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
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
<script type="text/javascript">
    // type - warning, danger, success, info, etc
    function displayAlert(message, type) {
        $(".errors").html('<div class="alert alert-'+type+' alert-dismissible" role="alert">'+
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
          '<span aria-hidden="true" style="font-size:inherit;">&times;</span></button>'+message+
        '</div>');
    }

    $(document).ready(function() {

        var manifestsTable = $('#manifests-table').DataTable({
            "sDom": '<"H"lpr>t<"F"i>',
            "processing": false,
            "ajax": "{{route('products.manifests.search',['type'=>1])}}",
            "lengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250 , 500]],
            "serverSide": true,
            "order": [[3, "desc"]],
            "fnDrawCallback": function(oSettings){
                var select2 = $(".select2.we-dropdown").select2().on('change', function(){
                    var dropdown = $(this).closest('.weDropdown');
                    var spinner = dropdown.prev();
                    var name = dropdown.prev().prev();
                    var pickupBtn = $(this).closest('tr').find('button.pick-up');
                    var pickupDate = $(this).closest('tr').find('.pickup-date');
                    var status = $(this).closest('tr').find('.status');
                    var selectedName = dropdown.find('option:selected').text();
                    var selectedId = $(this).val();
                    // console.log(name.data('url'));
                    // console.log($(this).val());
                    // console.log(selectedName);

                    var postData = {
                        'user_id': selectedId,
                    };
                    // perform ajax
                    $.ajax({
                        type:"POST",
                        data: postData,
                        url: name.data('url'),
                        beforeSend: function() {
                            dropdown.toggle();
                            spinner.toggle();
                            name.toggle().off('click');
                        },
                        success:function(response){
                           // console.log(response);
                            if(response.success == true){
                                name.html(selectedName);
                                name.data('user-id', selectedId);
                                pickupBtn.hide(400, function(){
                                    pickupBtn.remove();
                                });
                                // console.log(response.response.pickup_date);
                                pickupDate.html(response.response.pickup_date);
                                status.html(response.response.status);
                            }else{
                                // show error message
                                if (response.error!==undefined) {
                                    var msg = '';
                                    $.each(response.error, function (index, message){
                                        // console.log(index);
                                        msg += message;
                                    });
                                    displayAlert(msg, 'danger');
                                }
                            }
                        },
                        complete: function() {
                            // waitingDialog.hide();
                            // loadCounter(); 
                            spinner.toggle();
                            name.on('click', function(){
                                var userLink = $(this);
                                userLink.next().next().toggle();
                                userLink.toggle();
                                userLink.next().next().find('.select2').val(userLink.data('user-id')).select2('open');
                                // console.log(userLink.data('id'));
                            });
                        },
                    });
                });
                $('.assign-user-link').on('click', function(){
                    var userLink = $(this);
                    userLink.next().next().toggle();
                    userLink.toggle();
                    userLink.next().next().find('.select2').val(userLink.data('user-id')).select2('open');
                    // console.log(userLink.data('id'));
                });
            },
            "columnDefs": [
                {
                    "targets": [ 2, 4, 7 ],
                    "searchable": false,
                    "orderable": false 
                }
            ],
            "columns": [
                {"data":"id", "name":"id", "targets":0},
                {"data":"status", "name":"status", "targets":1, 'className':'status'},
                {"data":"admin_name", "name":"admin_name", "targets":2},
                {"data":"created_at", "name":"created_at", "targets":3},
                {"data":"creator_name", "name":"creator_name", "targets":4},
                {"data":"pickup_date", "name":"pickup_date", "targets":5, 'className':'pickup-date'},  
                {"data":"updated_at", "name":"updated_at", "targets":6},
                {"data":"actions", "name":"actions", "targets":7}
            ]
        });

        $("#generate").click(function() {
            $.ajax({
                type:"POST",
                data: { channel_types: $("#channel_types").select2('val') },
                url: "{{route('admin.fulfillment.manifests.store')}}",
                beforeSend: function() {
                    waitingDialog.show('Generating manifest...', {dialogSize: 'sm'});
                },
                success:function(response){
                    if (response.success==true) {
                        displayAlert(response.message, 'success');
                        manifestsTable.ajax.reload();
                    }
                    else {
                        var msg = (response.message!==undefined)? response.message:'An error has occurred while generating the picking manifest.';
                        displayAlert(msg, 'danger');
                    }
                },
                complete: function() {
                    waitingDialog.hide();
                    loadCounter(); 
                },
            });
        });

        $(document).on("click",".pick-up", function (e) {       
            var id = $(this).data('id');
            //console.log(itemId);
            $.ajax({
                type:"POST",
                data: {id: id},
                url: "{{route('products.manifests.pickup')}}",
                beforeSend: function() {
                    waitingDialog.show('Processing...', {dialogSize: 'sm'});
                },
                success:function(response){
                    if (response.success) 
                        manifestsTable.ajax.reload();
                    
                    else 
                        displayAlert('An error has occurred.', 'danger');
                },
                complete: function() {
                    waitingDialog.hide();        
                },
            });
        });
        
    });
</script>
@append