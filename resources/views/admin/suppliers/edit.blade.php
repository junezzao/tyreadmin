@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_supplier_update')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.content_header_suppliers')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_supplier_edit')</h3>
	            	</div>
	            	<div class="box-body">
	            		{!! Form::model($supplier,['route' => ['admin.suppliers.update', $supplier->id], 'method'=>'put', 'id'=>'form', 'files'=>false ]) !!}
	            		  <div class="col-sm-10">
	            		  	@if(!$user->is('clientadmin|clientuser'))
        					   <div class="form-group row">
							    {!! Form::label('merchant_id','Merchant',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::select('merchant_id',$merchants, null,['class'=>'form-control select2','placeholder'=>'Select Merchant...']) !!}
							      <span class="text-danger">{{ $errors -> first('merchant_id')}}</span>
							    </div>
							  </div>
							  @else
							  	{!! Form::hidden('merchant_id', $user->merchant_id) !!}
							  @endif
							  
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Company Name']) !!}
							      <span class="text-danger">{{ $errors -> first('name')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('active','Status',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! Form::select('active',['Inactive','Active'], null,['class'=>'form-control select2-nosearch','placeholder'=>'Select...']) !!}
							      <span class="text-danger">{{ $errors -> first('active')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('phone','Phone',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('phone',null,['class'=>'form-control','placeholder'=>'Phone']) !!}
							      <span class="text-danger">{{ $errors -> first('phone')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4 required']) !!}
							    <div class="col-sm-8">
							     {!! Form::textarea('address',null,['class'=>'form-control','placeholder'=>'Company Address']) !!}
							     <span class="text-danger">{{ $errors -> first('address')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('contact_person','Contact Person',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('contact_person',null,['class'=>'form-control','placeholder'=>'Contact Person']) !!}
							      <span class="text-danger">{{ $errors -> first('contact_person')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('email','Email',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::email('email',null,['class'=>'form-control','placeholder'=>'Supplier Email']) !!}
							      <span class="text-danger">{{ $errors -> first('email')}}</span>
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('mobile','Mobile Number',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('mobile',null,['class'=>'form-control','placeholder'=>'Mobile Number']) !!}
							      <span class="text-danger">{{ $errors -> first('mobile')}}</span>
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('registration_no','Registration No.',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('registration_no',null,['class'=>'form-control','required'=>true,'placeholder'=>'Company Registration Number']) !!}
							      <span class="text-danger">{{ $errors -> first('registration_no')}}</span>
							    </div>
							  </div>
							  						  
						</div>
						<div class="col-sm-12">
							<div class="col-sm-6">
								<div class="form-group row pull-left">
									@if($user->can('delete.supplier'))
									<button type="button" id="btn-delete" data-href="{{URL::route('admin.suppliers.destroy',[$supplier->id])}}" class="btn btn-danger">Delete Supplier</button>
									@endif
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group row pull-right">
									<button type="submit" class="btn btn-default">Update Supplier</button>
								</div>
							</div>
						</div>
					{!! Form::close() !!}
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>

@stop


@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function(){
	$('#btn-delete').click(function(){
		var c = confirm('Are you sure?');
		var url = $(this).data('href');
		if(c)
		{
			$('#form input[name="_method"]').val('DELETE');
			$('#form').submit();
		}

	});
});
</script>
@append