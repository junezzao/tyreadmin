@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('brands.page_title_brands')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('brands.content_header_brands')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('brands.box_header_brands_deactivated')</h3>
	              		<div class="pull-right">
	              			<a class="btn btn-default right" href="{{route('brands.index')}}">Back to All Brands</a>
	            		</div>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<table id="brands-table" class="table table-bordered table-striped" style="width:100%">
	                    <thead>
							<tr>
								<th>@lang('brands.brands_table_brand_name')</th>
								<th>@lang('brands.brands_table_prefix')</th>
								<th>@lang('brands.brands_table_merchant')</th>
								<th>@lang('brands.brands_table_updated_at')</th>
								<th></th>
							</tr>
		                    <tr class="search-row">
	                            <td class="search dropdown 0" data-field="brand" id="brands-filter"></td>
	                            <td class="search text 1"></td>                   
	                            <td class="search dropdown 2" data-field="merchant"></td>
	                            <td class="search text 3"></td>
	                            <td></td>
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
<style type="text/css">
	.btn-link {
		padding: 0;
		font-size: 13px;
	}
	.search-row input {
		max-width: 125px!important;
	}
	.search-row select {
	    max-width: 150px!important;
	}
	input.product-count {
		max-width: 50px!important;
	}
</style>

<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script type="text/javascript">
jQuery(document).ready(function(){

	$('#brands-table thead td.search.text').each( function () {
        $(this).append( '<input type="text" class="form-control"/>' );
    } );

    @if($user->can('edit.brand'))
	$('#brands-table').on('click','.confirm',function(){
		var c = confirm('Are you sure? '+$(this).data('message'));
		var url = $(this).data('href');
		var status = $(this).data('status');
		if(c)
		{
			$('<form action="'+url+'" method="POST"><?php echo Form::token();?><input type="hidden" name="active" value="'+status+'"><input type="hidden" name="_method" value="PUT"></form>').appendTo('body').submit();
		}
	});
	@endif

    var channel_id = '{{ $channel_id }}';
	var table = jQuery('#brands-table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<ip>',
		"ajax": '{{ URL::to("brands/table_data") }}?active=0' + (channel_id.length > 0 ? '&channel_id='+channel_id : ''),
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[4, "desc"]],
		"orderCellsTop": true,
		"scrollX": true,
		"fnDrawCallback": function (o) {
			jQuery(window).scrollTop(0);
		},
		"columnDefs": [
            { "data": "name", "name": "name", "targets": 0 },
            { "data": "prefix", "name": "prefix", "targets": 1 },
            { "data": "merchant", "name": "merchant", "targets": 2},
            { "data": "updated_at", "name": "updated_at", "targets": 3 },
            { "data": "actions", "name": "actions", "targets": 4, "orderable": false }
        ],
        // to initialize drop down filter
        initComplete: function(){
            this.api().columns().every(function(){
                var column = this;
                if(column.index() == 0 || column.index() == 2){
                    var select = $('<select class="form-control"><option value="">All</option></select>')
                        .appendTo($('.dataTable thead tr td:nth-child(' + (column.index() + 1) + ')').first().empty())
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
            // Do auto filter based on query string
            var filterChannel = '{{Input::get("channel")}}';
            if(filterChannel != ''){
                $('#brands-filter select').val(filterChannel).trigger('change');
            }
        }
    });
    
    if ("{{Auth::user()->is('clientuser|clientadmin')}}" == 1) {
    	var merchantCol = table.column( 2 );
    	merchantCol.visible(false);
    }

	// Apply the search
    table.columns().every( function () {
    	var that = this;
        $( 'input', 'thead tr.search-row td.text.search.'+that.index() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
});
</script>
@append