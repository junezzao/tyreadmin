@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_issuing_companies')
@stop

@section('content')
	<!-- Content Header (Page header) http://stackoverflow.com/questions/26973442/laravel-blade-check-box -->
    <section class="content-header">
    	<h1>@lang('admin/page-titles.page_title_issuing_company_create')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_issuing_company_create')</h3>
	            	</div>
	            	<div class="box-body">
	            		{!! Form::open(['route' => 'admin.issuing_companies.store','files'=>true]) !!}
	            		  <div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Company Name']) !!}
							      <span class="text-danger">{{ $errors -> first('name')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg','GST Registration',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      <div class="radio">
							        <label>
							          {!! Form::radio('gst_reg',1, true) !!} Yes 
							        </label>
							        &nbsp;&nbsp;
							        <label>
							          {!! Form::radio('gst_reg',0) !!} No 
							        </label>
							        <label>
							        <span class="text-danger">{{ $errors -> first('gst_reg')}}</span>
							    	</label>

							      </div>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg_no','GST Registration Number',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('gst_reg_no',null,['class'=>'form-control','placeholder'=>'GST Registration Number']) !!}
							      <span class="text-danger">{{ $errors -> first('gst_reg_no')}}</span>
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
							    {!! Form::label('logo','Logo',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      	  {!! Form::file('logo',null,['class'=>'form-control']) !!}
									  <span class="file-custom"></span>
									  <span class="text-danger">{{ $errors -> first('logo')}}</span>
								</div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('prefix','Prefix',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::text('prefix',null,['class'=>'text-uppercase form-control','placeholder'=>'Prefix']) !!}
							      <span class="text-danger">{{ $errors -> first('prefix')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('date_format','Document Number Date Format',['class'=>'col-sm-4 control-label required']) !!}
							    <div class="col-sm-8">
							      {!! Form::select('date_format',Config::get('globals.date_format_invoice_no'),null,['class'=>'form-control select2','placeholder'=>'Select Date format']) !!}
							      <span class="text-danger">{{ $errors -> first('date_format')}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							  	<div class="col-sm-12">
							  	<i><span class="glyphicon glyphicon-info-sign"></span> Document Number will look like this &#60;Prefix&#62;-&#60;Date in date format&#62;-00001</i>
							    </div>
							  </div>
						</div>
						<div class="col-sm-6">
							  <fieldset id="extra_row">
							  <div class="form-group row">
							    {!! Form::label('extra_info','Additional Information',['class'=>'col-sm-4 control-label']) !!}
							    <div class="col-sm-4">
							     <span class="text-danger">{{ $errors -> first('extras')}}</span>
							     <?php echo Form::text('extra[0]', null, ['class'=>'form-control','placeholder' => 'Field Name&hellip;']); ?>
							     <span class="text-danger">{{ $errors -> first('extra.0')}}</span>
							    </div>
							    <div class="col-sm-4">
							     <span class="text-danger">{{ $errors -> first('extra_detail')}}</span>
							     <?php echo Form::text('extra_detail[0]',  null, ['class'=>'form-control','placeholder' => 'Field Detail']); ?>
							     <span class="text-danger">{{ $errors -> first('extra_detail.0')}}</span>
							    </div>
							   </div>
							 <?php
							 	if(!empty(old('extra')) && count(old('extra'))-1 > 0):
							 		for($i=1;$i<count(old('extra'));$i++):
							 ?>
							 	<div class="form-group row">
							 	<div class="col-sm-4"><div class="col-sm-4"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>
							    <div class="col-sm-4">
							     <?php echo Form::text('extra['.$i.']', null, ['class'=>'form-control','placeholder' => 'Field Name&hellip;']); ?>
							     <span class="text-danger">{{ $errors -> first('extra.'.$i)}}</span>
							    </div>
							    <div class="col-sm-4">
							     <?php echo Form::text('extra_detail['.$i.']',  null, ['class'=>'form-control','placeholder' => 'Field Detail']); ?>
							     <span class="text-danger">{{ $errors -> first('extra_detail.'.$i)}}</span>
							    </div>
							   </div>
							<?php
									endfor;
								endif;
							?>
							  </fieldset>
							  <div class="form-group row">
							  		<div class="col-sm-4"></div>
							  		<div class="col-sm-8"><button id="btn-add" tittle="Add&hellip;" type="button" class="btn"><i class="fa fa fa-2x fa-plus-circle" aria-hidden="true"></i></button></div>
							  </div>

						</div>
						<div class="col-sm-12">
							<div class="col-sm-12">
								<div class="form-group row pull-right">
									<button type="submit" class="btn btn-default">Create New Issuing Company</button>
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
	$('#btn-add').click(function(){
		var str = '<div class="form-group row">';
			str +='			<div class="col-sm-4"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			str +='			<div class="col-sm-4">';
			str +='				<?php echo Form::text('extra1',  null, ['name'=>'extra[]','class'=>'form-control','placeholder' => 'Field Name']);?>';
			str +='			</div>';
			str +='			<div class="col-sm-4">';
			str +='				<?php echo Form::text('extra_detail1',  null, ['name'=>'extra_detail[]','class'=>'form-control','placeholder' => 'Field Detail']); ?>';
			str +='			</div>';
			str +='</div>';
		$('#extra_row').append(str);
		return false;
	});
	$('#extra_row').on('click','.close',function(){
		$(this).parent().parent().remove();
	});
	// Load new custom fields when changing channel type
    $("input[name='prefix']").change(function () {
        if($(this).val() != '')
            $.ajax({
                'url': "/admin/channels/"+$(this).val()+"/channel_type_fields",
                'method': 'GET',
                'dataType': 'json',
                'success': function(response){
                    if(response.success == true){
                        $('.custom-fields').html(response.view);
                    }
                }
            });
        else
            $('.custom-fields').html('');
    });
    $("input[name='prefix']").change(function () {
        if($(this).val() != '')
            $.ajax({
                'url': "/admin/channels/"+$(this).val()+"/channel_type_fields",
                'method': 'GET',
                'dataType': 'json',
                'success': function(response){
                    if(response.success == true){
                        $('.custom-fields').html(response.view);
                    }
                }
            });
        else
            $('.custom-fields').html('');
    });

    $('input[name=gst_reg]').change(function(){
		if($(this).val() == 0){
			$('label[for=gst_reg_no]').removeClass('required');
			$('input[name=gst_reg_no]').val('');
			$('input[name=gst_reg_no]').prop('disabled', 'disabled');
		}else{
			$('label[for=gst_reg_no]').addClass('required');
			$('input[name=gst_reg_no]').prop('disabled', '');
		}
	});

});
</script>
@append