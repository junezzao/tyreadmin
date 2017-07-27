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
	              		<h3 class="box-title">@lang('product-management.box_header_categories')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            		<div class="col-xs-12 col-lg-6">
	            			{!! Form::open(array('url' => route('products.categories.update',['id'=>$category->id]), 'method' => 'PUT')) !!}
	            			<div class="col-xs-12">
		            			
			            			<div class="form-group has-feedback">
			            				<label class="col-xs-3 control-label required" for="name">@lang('product-management.categories_form_label_name')</label>
			            				<div class="col-xs-9">
	   										{!! Form::text( 'name', $category->name, ['class' => 'form-control','required', 'placeholder' => trans('product-management.categories_form_placeholder_name')] ) !!}
	   										<div class="error">{{ $errors->first('name') }}</div>
						                </div>
						            </div>
						            
						            <div id="merchant-field" class="form-group has-feedback">
			            				<label class="col-xs-3 control-label" for="merchant">@lang('product-management.categories_form_label_parent')</label>
			            				<div class="col-xs-9">
			            					{!! Form::select('parent_id', $parents, $category->parent_id, array('class' => 'form-control select2', 'placeholder' => trans('product-management.categories_form_placeholder_parent'))) !!}
						                	<div class="error">{{ $errors->first('parent_id') }}</div>
						                </div>
						            </div>
						        
				         	</div>

				         	<div class="col-xs-12">
				         		@if($user->can('delete.category'))
				         		<div class="form-group pull-left">
				         			<button type="button" id="btn_delete" class="btn btn-warning">@lang('product-management.button_delete_category')</button>
				         		</div>
				         		@endif
				         		<div class="form-group pull-right">
					               <button type="submit" id="btn_create_new_category" class="btn btn-default">@lang('product-management.button_create_new_category')</button>
					            </div> <!-- / .form-actions -->
					        </div>
				        {!! Form::close() !!}
	            		</div>
	            		
	            </div>
	        </div>
	    </div>
	    
		<div class="hide product-count">
			<input type="text" class="form-control product-count min" placeholder="{{trans('categories.categories_placeholder_min')}}" />&nbsp;&mdash;&nbsp;<input type="text" class="form-control product-count max" placeholder="{{trans('categories.categories_placeholder_max')}}" />
		</div>
   	</section>
@stop

@section('footer_scripts')

<script type="text/javascript">

$(document).ready(function(){

	$('#btn_delete').click(function(){
			@if($category->total_product > 0)
			alert('Please reassign all product for this category to another before deleting.');
			return;
			@elseif($category->has_child)
				alert('You are deleting a parent category. All children will be assigned to parent of this category.');
			@endif
			var c = confirm('Are you sure to proceed?');
			if(c)
			{
				$('<form action="{{URL::route('products.categories.destroy',['id'=>$category->id])}}" method="POST"><?php echo Form::token();?><input type="hidden" name="_method" value="DELETE"></form>').appendTo('body').submit();
			}
			
	});

});

</script>
@append