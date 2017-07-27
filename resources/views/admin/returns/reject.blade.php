@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/fulfillment.page_title_fulfillment')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/fulfillment.content_header_fulfillment')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('admin/fulfillment.box_header_returns_reject')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body">
	            	{!! Form::open(array('url'=> route('admin.fulfillment.return.update',[$id]), 'method' => 'put', 'class' => 'form-inline')) !!}
	            	{!!Form::hidden('action', 'reject')!!}
	            	<div class="col-sm-6">
						<div class="form-group row">
	            		{!! Form::label('remark','Remark',['class'=>'col-sm-5 form-control-label required']) !!}
						    <div class="col-sm-7">
						      {!! Form::select('remark',$reasons,null,['class'=>'form-control','id'=>'remark-options','placeholder'=>'Please select reason']) !!}
						      <span class="text-danger">{{ $errors -> first('remark')}}</span>
						    </div>
						</div>
						<div class="form-group row " id="remark-div">
	            		</div>
	            	</div>
	            	<div class="col-sm-12">
						<div class="col-sm-12">
							<div class="form-group row pull-left">
								<button type="submit" class="btn btn-default">Reject</button>
							</div>
						</div>
					</div>
					{!! Form::close()!!}
	            </div>
	        </div>
	    </div>
   	</section>
@stop


@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script type="text/javascript">
jQuery(document).ready(function(){
		$('#remark-options').change(function()
			{
				var elem = $('<div class="col-sm-7">{!! Form::textarea('remark',null,['class'=>'form-control','placeholder'=>'Remark'])!!}</div>')
				if($(this).val() == 3)
					$('#remark-div').html(elem);
				else
					$('#remark-div').html('');
			});
	});
</script>
@append