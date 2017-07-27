@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('product-management.page_title_categories')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('product-management.content_header_categories')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">
	              			@if ($user->can('create.category'))
	              				@lang('product-management.box_header_categories')
	              			@else
	              				@lang('product-management.box_header_categories_list')
	              			@endif
	              		</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		@if ($user->can('create.category'))
	            		<div class="col-xs-12 col-lg-5">
	            			{!! Form::open(array('url' => route('products.categories.store'), 'method' => 'POST')) !!}
	            			{!! Form::hidden('id',0) !!}
	            			<div class="col-xs-12">

			            			<div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="name">@lang('product-management.categories_form_label_name')</label>
			            				<div class="col-xs-9">
	   										{!! Form::text( 'name', null, ['class' => 'form-control','required', 'placeholder' => trans('product-management.categories_form_placeholder_name')] ) !!}
	   										<div class="error">{{ $errors->first('name') }}</div>
						                </div>
						            </div>

						            <div id="merchant-field" class="form-group has-feedback">
			            				<label class="col-xs-3 control-label" for="merchant">@lang('product-management.categories_form_label_parent')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('parent_id', $parents, null, array('class' => 'form-control select2', 'placeholder' => trans('product-management.categories_form_placeholder_parent'))) !!}
						                	<div class="error">{{ $errors->first('parent_id') }}</div>
						                </div>
						            </div>

				         	</div>

				         	<div class="col-xs-12">
				         		<div class="form-group pull-right">
					               <button type="submit" id="btn_create_new_user" class="btn btn-default">@lang('product-management.button_create_new_category')</button>
					            </div> <!-- / .form-actions -->
					        </div>
				        {!! Form::close() !!}
	            		</div>
	            		@endif

	            		<div class="col-xs-12 {{ ($user->can('create.category')) ? 'col-lg-7' : '' }}">
		            		<table id="categories-table" class="table table-bordered table-striped" style="width:100%">
		                    <thead>
								<tr>
									<th style="width:40%">@lang('product-management.categories_table_category_name')</th>
									<th style="width:20%">@lang('product-management.categories_table_product_count')</th>
									<th style="width:20%">@lang('product-management.categories_table_updated_at')</th>
									<th style="width:10%"></th>
								</tr>
			                    <tr class="search-row">
		                            <td class="search text 0 data-field="category" id="categories-filter""></td>
		                            <td class="search product-count 1"></td>
		                            <td class="search 2"></td>
		                            <td></td>
		                        </tr>

		                    </thead>
		                    <tbody>
		                    	@foreach($categories as $category)
		                    	<tr>
		                    		<td>{!!str_replace($category->name,'<b>'.$category->name.'</b>',$category->full_name)!!}</td>
		                    		<td><a href="{{ URL::route('products.inventory.index',['category_id'=>$category->id])}}">{{$category->total_product}}</a></td>
		                    		<td>{{$category->updated_at}}</td>
		                    		<td>
		                    			@if ($user->can('edit.category'))
		                    				<a href="{{ URL::route('products.categories.edit',['id'=>$category->id]) }}">Edit</a>
		                    			@endif
		                    		</td>
		                    	</tr>
		                        @endforeach
		                    </tbody>
		                    </table>
	                	</div>
	            	</div>
	            </div>
	        </div>
	    </div>

		<div class="hide product-count">
			<input type="text" class="form-control product-count min" placeholder="{{trans('product-management.categories_placeholder_min')}}" />&nbsp;&mdash;&nbsp;<input type="text" class="form-control product-count max" placeholder="{{trans('product-management.categories_placeholder_max')}}" />
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
		max-width: 250px!important;
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

	// Custom search function for searching range of product count
    $.fn.dataTable.ext.search.push(
	    function( settings, data, dataIndex ) {
	        var min = parseInt( $('.min').val() );
	        var max = parseInt( $('.max').val() );

	        var prodCount = parseFloat( data[1] ) || 0; // use data for the product count column

	        if ( ( isNaN( min ) && isNaN( max ) ) ||
	             ( isNaN( min ) && prodCount <= max ) ||
	             ( min <= prodCount   && isNaN( max ) ) ||
	             ( min <= prodCount   && prodCount <= max ) )
	        {
	            return true;
	        }
	        return false;
	    }
	);

	$('#categories-table thead td.search.text').each( function () {
        $(this).append( '<input type="text" class="form-control"/>' );
    } );

    $('#categories-table thead td.search.dropdown').each( function () {
        $(this).append( $("."+$(this).data('field')).html() );
    } );

    $('#categories-table thead td.search.product-count').append( $('.hide.product-count').html() );

    @if($user->can('edit.category'))
		$('#categories-table').on('click','.confirm',function(){
			var c = confirm('Are you sure? '+$(this).data('message'));
			var url = $(this).data('href');
			var status = $(this).data('status');
			if(c)
			{
				$('<form action="'+url+'" method="POST"><?php echo Form::token();?><input type="hidden" name="active" value="'+status+'"><input type="hidden" name="_method" value="PUT"></form>').appendTo('body').submit();
			}
		});
	@endif

	var table = jQuery('#categories-table').DataTable({
		"dom": '<"clearfix"lf><"clearfix"ip>t<ip>',
		"lengthMenu": [[50, 75, 100], [50, 75, 100]],
		"pageLength": 50,
		"orderCellsTop": true,
		"scrollX": true,
		"fnDrawCallback": function (o) {
			jQuery(window).scrollTop(0);
		},
		"columnDefs": [
            { "data": "name", "name": "name", "targets": 0 },
            { "data": "product_count", "name": "product_count", "targets": 1 },
            { "data": "updated_at", "name": "updated_at", "targets": 2 },
            { "data": "actions", "name": "actions", "targets": 3, "orderable": false }
        ],
        // to initialize drop down filter
        initComplete: function(){
        	/*
            this.api().columns().every(function(){
                var column = this;
                if(column.index() == 0){
                    var select = $('<select class="form-control"><option value="">All</option></select>')
                        .appendTo($('.dataTable thead tr td:nth-child(' + (column.index() + 1) + ')').first().empty())
                        .on('change', function(){
                        	console.log($(this).val());
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column.search(val).draw();
                        });

                    column.data().unique().sort().each(function(d, j){
                        select.append('<option value="'+$('<span>'+d+'</span>').text()+'">'+d+'</option>')
                    });
                }
            });
            */
        }
    });


    $(document).on('click', "button.edit", function(e) {
    	window.location.href = "{{route('products.categories.edit', ['replaceme'])}}".replace("replaceme", $(this).data('id'));
	});

	$(document).on('click', '.confirmation', function (e) {
		//e.preventDefault();
        return confirm('Are you sure you want to delete this category?');
    });

	// Apply the search
    table.columns().every( function () {
    	var that = this;
        $( 'input', 'thead tr.search-row td.text.search.'+that.index() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that.search( this.value ).draw();
            }
        } );


        $( 'input', 'thead tr.search-row td.product-count.search.'+that.index() ).on( 'keyup change', function () {
            table.draw();
        } );

    } );


});
</script>
@append