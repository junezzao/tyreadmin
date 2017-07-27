@extends('layouts.master')

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
@append

@section('title')
	@lang('admin/configurations.page_title_configurations')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('admin/configurations.content_header_configurations')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	        	<div class="box">
		        	<div class="box-body table-responsive no-padding">
			        	<table class="table table-bordered">
		                    <tr>
		                    	@foreach($headers as $header)
		                    		<th>{{ $header }}</th>	
		                   		@endforeach
		                    </tr>
		                    @foreach($modules as $module)
				        		<tr>
					        		<td>{{ $module['name'] }}</td>
					        		<td>{{ $module['slug'] }}</td>
					        		<td>{{ $module['description'] }}</td>
					        		<td>{{ $module['status'] }}</td>
					        		<td><button class="btn btn-{{ ($module['action'] == 'Enable' ? 'success' : 'danger') }}" data-slug="{{ $module['slug'] }}" data-action="{{ strtolower($module['action']) }}">{{ $module['action'] }}</button></td>
				        		</tr>
				        	@endforeach
		                </table>
		        	</div>
		        </div>	
	        </div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<script type="text/javascript">
jQuery(document).ready(function(){
	
	jQuery('.btn').click(function(){
		var slug = jQuery(this).attr('data-slug');
		var action = jQuery(this).attr('data-action');

		jQuery.ajax({
			method: 'POST',
			url: '/admin/config/'+action,
			data: {slug: slug, action: action}
		})
		.done(function (data) {
	         location.reload();
	    });
	});
});
</script>
@append
