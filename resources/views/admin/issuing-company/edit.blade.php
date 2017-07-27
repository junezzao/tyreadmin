@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_issuing_companies')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.page_title_issuing_company_update')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_issuing_company_edit')</h3>
	            	</div>
	            	<div class="box-body">
	            		{!! Form::open(array('url' => route('admin.issuing_companies.update', [$id]), 'method' => 'PUT', 'id'=>'form', 'files'=>true )) !!}
	            		<div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('name',$name,['class'=>'form-control','placeholder'=>'Company Name']) !!}
							      <span class="text-danger">{{ $errors -> first('name')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg','GST Registration',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      <div class="radio">
							        	@if($gst_reg == 1)
								          	<label>{!! Form::radio('gst_reg', 1, true) !!} Yes</label> &nbsp;&nbsp;
								          	<label>{!! Form::radio('gst_reg', 0) !!} No</label>
								        @else
								        	<label>{!! Form::radio('gst_reg', 1) !!} Yes</label> &nbsp;&nbsp;
								          	<label>{!! Form::radio('gst_reg', 0, true) !!} No</label>
								        @endif
							        <label>
							        <span class="text-danger">{{ $errors -> first('gst_reg')}}</span>
							    	</label>
							      </div>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg_no','GST Registration Number',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('gst_reg_no',$gst_reg_no,['class'=>'form-control','placeholder'=>'GST Registration Number']) !!}
							      <span class="text-danger">{{ $errors -> first('gst_reg_no')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4 required']) !!}
							    <div class="col-sm-8">
							     {!! Form::textarea('address',$address,['class'=>'form-control','placeholder'=>'Company Address']) !!}
							     <span class="text-danger">{{ $errors -> first('address')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('logo','Logo',['class'=>'col-sm-4 required']) !!}
							    <div class="col-sm-8">
							    	<img src="{!! $logo_url!==''?$logo_url:'http://placehold.it/350x150' !!}" class="img-thumbnail">
							      	  {!! Form::hidden('logo_url', $logo_url) !!}
									  {!! Form::file('logo',null,['class'=>'form-control']) !!}
									  <span class="file-custom"></span>
									  <span class="text-danger">{{ $errors -> first('logo')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('prefix','Prefix',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('prefix',$prefix,['class'=>'text-uppercase form-control','placeholder'=>'Prefix','readonly' => 'true']) !!}
							      <span class="text-danger">{{ $errors -> first('prefix')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('date_format','Document Number Date format',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('date_format',$date_format,['class'=>'form-control','placeholder'=>'Select Date format','readonly' => 'true']) !!}
							      <span class="text-danger">{{ $errors -> first('date_format')}}</span>
							    </div>
							  </div>
							   <div class="form-group row">
							  	<div class="col-sm-12">
							  	<i><span class="glyphicon glyphicon-info-sign"></span> Document Number will look like this {!!$document_format!!}</i>
							    </div>
							  </div>
						</div>
						<div class="col-sm-6">
							<fieldset id="extra_row">
							   	<div class="form-group row">
								    {!! Form::label('Extra','Additional Information',['class'=>'col-sm-4 control-label']) !!}
								    <div class="col-sm-4">
								    	{!! Form::text('extra[0]', !empty($extra[0])?$extra[0]:null, ['class'=>'form-control', 'placeholder' => 'Field Name&hellip;']) !!}
								     	<span class="text-danger">{{ $errors -> first('extra.0') }}</span>
								    </div>
								    <div class="col-sm-4">
								     	{!! Form::text('extra_detail[0]', !empty($extra_detail)?$extra_detail[0]:null, ['class'=>'form-control', 'placeholder' => 'Field Detail']) !!}
								     	<span class="text-danger">{{ $errors -> first('extra_detail.0') }}</span>
								    </div>
							   	</div>

							 	@if(!empty(old('extra')) && count(old('extra'))-1 > 0)
							 		@for( $i = 1; $i < count(old('extra')); $i++ )
									 	<div class="form-group row">
									 	<div class="col-sm-4"></div>
									    <div class="col-sm-4">
									    	{!! Form::text('extra['.$i.']', null, ['class'=>'form-control', 'placeholder' => 'Field Name&hellip;']) !!}
									     	<span class="text-danger">{{ $errors -> first('extra.'.$i) }}</span>
									    </div>
									    <div class="col-sm-4">
									    	{!! Form::text('extra_detail['.$i.']',  null, ['class'=>'form-control', 'placeholder' => 'Field Detail']) !!}
									     	<span class="text-danger">{{ $errors -> first('extra_detail.'.$i) }}</span>
									    </div>
									   </div>
									@endfor
								@else
									@for($i = 1; $i < count($extra); $i++)
										<div class="form-group row">
									   		<div class="col-sm-4"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
										    <div class="col-sm-4">
										    	{!! Form::text('extra['.$i.']', $extra[$i], ['class'=>'form-control', 'placeholder' => 'Field Name&hellip;']) !!}
										     	<span class="text-danger">{{ $errors -> first('extra.'.$i) }}</span>
										    </div>
										    <div class="col-sm-4">
										    	{!! Form::text('extra_detail['.$i.']',  $extra_detail[$i], ['class'=>'form-control', 'placeholder' => 'Field Detail']) !!}
										     	<span class="text-danger">{{ $errors -> first('extra_detail.'.$i) }}</span>
										    </div>
									   	</div>
							   		@endfor
								@endif
							</fieldset>
							<div class="form-group row">
							  	<div class="col-sm-4"></div>
							  	<div class="col-sm-8"><button id="btn-add" tittle="Add&hellip;" type="button" class="btn"><i class="fa fa-2x fa-plus-circle" aria-hidden="true"></i></button></div>
							</div>

						</div>
						<div class="col-sm-12">
							<div class="col-sm-12">
								<div class="form-group row pull-right">
									<button type="submit" class="btn btn-default">Update Issuing Company</button>
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
	
	if($(this).val() == 0){
		$('label[for=gst_reg_no]').removeClass('required');
	}else{
		$('label[for=gst_reg_no]').addClass('required');
	}

	$('input[name=gst_reg]').change(function(){
		if($(this).val() == 0){
			$('label[for=gst_reg_no]').removeClass('required');
			$('input[name=gst_reg_no]').val('');
			$('input[name=gst_reg_no]').prop('readonly', 'readonly');
		}else{
			$('label[for=gst_reg_no]').addClass('required');
			$('input[name=gst_reg_no]').prop('readonly', '');
		}
	});

	$('#btn-add').click(function(){
		var str = '<div class="form-group row">';
			str +='			<div class="col-sm-4"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			str +='			<div class="col-sm-4">';
			str +='				<?php echo Form::text('extra1',  null, ['name'=>'extra[]', 'class'=>'form-control', 'placeholder' => 'Field Name']);?>';
			str +='			</div>';
			str +='			<div class="col-sm-4">';
			str +='				<?php echo Form::text('extra_detail1',  null, ['name'=>'extra_detail[]', 'class'=>'form-control', 'placeholder' => 'Field Detail']); ?>';
			str +='			</div>';
			str +='</div>';
		$('#extra_row').append(str);
		return false;
	});
	$('#extra_row').on('click','.close',function(){
		$(this).parent().parent().remove();
	});
});
</script>
@append