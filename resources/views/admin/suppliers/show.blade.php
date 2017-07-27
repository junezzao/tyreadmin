@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_supplier_view')
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
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_supplier_view')</h3>
	            	</div>
	            	<div class="box-body">
	            		  <div class="col-sm-10">
	            		  	  @if(!$user->is('clientadmin|clientuser'))
        					   <div class="form-group row">
							    {!! Form::label('merchant_id','Merchant',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! !empty($supplier->merchant)?$supplier->merchant->name:'' !!}
							      
							    </div>
							  </div>
							  @endif
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! $supplier->name !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('phone','Phone',['class'=>'col-sm-4 control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $supplier->phone !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4 ']) !!}
							    <div class="col-sm-8">
							     {!! $supplier->address !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('contact_person','Contact Person',['class'=>'col-sm-4 control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $supplier->contact_person !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('email','Email',['class'=>'col-sm-4 control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $supplier->email !!}
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('mobile','Mobile Number',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! $supplier->mobile !!}
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('registration_no','Registration No.',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! $supplier->registration_no !!}
							    </div>
							  </div>
							  						  
						</div>
						<div class="col-sm-12">
							<div class="col-sm-6">
								<div class="form-group row pull-left">
									@if($user->can('edit.supplier'))
									<a href="{{URL::route('admin.suppliers.edit',[$supplier->id])}}" class="btn btn-default">Edit Supplier</a>
									@endif
								</div>
							</div>
							
						</div>
					</div>
	            </div>
	        </div>
	    </div>
   	</section>

@stop


@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function(){

});
</script>
@append