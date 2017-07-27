@extends('layouts.master')

@section('header_scripts')
@append

@section('title')
	@lang('admin/page-titles.page_title_issuing_companies')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/page-titles.page_title_issuing_company_view')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/page-titles.box_header_issuing_company_view')</h3>
	            	</div>
	            	<div class="box-body">
	            		  <div class="col-sm-6">
							  <div class="form-group row">
							    {!! Form::label('name','Name',['class'=>'col-sm-4 form-control-label']) !!}
							    <div class="col-sm-8">
							      {!! html_entity_decode($issuing_company->name) !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg','GST',['class'=>'col-sm-4 form-control-label']) !!}
							    <div class="col-sm-8">
							      {!! html_entity_decode($issuing_company->gst_reg) !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('gst_reg_no','GST Registration Number',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $issuing_company->gst_reg_no !!}
							    </div>
							  </div>

							  <div class="form-group row">
							    {!! Form::label('address','Address',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							     {!! nl2br($issuing_company->address) !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('logo','Logo',['class'=>'col-sm-4 form-control-label 	']) !!}
							    <div class="col-sm-8">
							      	<img src="{!! $issuing_company->logo_url!==''?$issuing_company->logo_url:'http://placehold.it/350x150' !!}" class="img-thumbnail">
								</div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('prefix','Prefix',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $issuing_company->prefix !!}
							    </div>
							  </div>
							  <div class="form-group row">
							    {!! Form::label('date_format','Date Format',['class'=>'col-sm-4 form-control-label ']) !!}
							    <div class="col-sm-8">
							      {!! $issuing_company->date_format !!}
							      <span class="text-info row">{{ $issuing_company->example_date}}</span>
							    </div>
							  </div>
							  <div class="form-group row">
							  	<div class="col-sm-12">
							  	<i><span class="glyphicon glyphicon-info-sign"></span> Document Number will look like this {!!$issuing_company->document_format!!}</i>
							    </div>
							  </div>
						</div>
						<div class="col-sm-6">
							  <fieldset id="extra_row">
							  <div class="form-group row">
							    {!! Form::label('extra','Additional Information',['class'=>'col-sm-4 form-control-label']) !!}
							    <?php
							     	$i=0;
							     	if(!empty($issuing_company->extra)):
							     	foreach($issuing_company->extra as $field => $value):
							     		if($i>0) echo '<div class="col-sm-4"></div>';
							    ?>
								<div class="col-sm-8">
								    {!! (!empty($field))?$field:'' !!} :
							    	{!! (!empty($value))?$value:'' !!}
							    </div>
								<?php
										$i++;
									endforeach;
									endif;
								?>
							  </fieldset>

						</div>
						 <div class="col-sm-12">
								<div class="col-sm-12">
									<div class="form-group row pull-right">
										@if($user->can('edit.issuingcompany'))
                                    		<a href="{{route('admin.issuing_companies.edit',$issuing_company->id)}}">
												<button type="button" class="btn btn-default">Update Issuing Company Details</button>
											</a>
										@endif
										<!--<button type="button" onclick="javascript:history.back();" class="btn btn-default">Back</button>-->
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