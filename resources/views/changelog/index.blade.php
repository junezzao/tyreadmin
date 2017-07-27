@extends('layouts.master')

@section('title')
    @lang('changelog.page_title_changelog_list')
@stop

@section('header_scripts')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js',env('HTTPS',false)) }}"></script>
@append

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('changelog.content_header_changelog')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('changelog.box_header_index')
                        </h3>
                        <div class="pull-right">
                        @if($admin->can('create.changelog'))
	                        <a href="{{route('changelog.create')}}" class="btn btn-default">
	                            @lang('changelog.button_add_new_changelog')
	                        </a> 
                        @endif
                            <a href="{{route('changelog.export')}}" class="btn btn-default">
                                @lang('changelog.button_export_changelog')
                            </a>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table id="pages_list" class="table table-condensed">
							<thead>
								 <tr>
								 	<th>@lang('changelog.form_label_title')</th>
								 	<th>@lang('changelog.form_label_content')</th>
								 	<th>@lang('changelog.label_date')</th>
								 	<th>@lang('changelog.label_actions')</th>
								 </tr>
							</thead>
							<tbody>
								@if(!empty($changelogs))
									@foreach($changelogs as $changelog)
										<tr>
										 	<td>{!! $changelog['title'] !!}</td>
										 	<td>{!! $changelog['content'] !!}</td>
										 	<td>{!! $changelog['created_at'] !!}</td>
										 	<td>{!! $changelog['actions'] !!}</td>
										</tr>
									@endforeach
								@endif	
							</tbody>
						</table>
                    </div>
                </div>
            </div>
        </div>
        <div class="html_to_replace"></div>
    </section>
@stop

@section('footer_scripts')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script type="text/javascript">
jQuery(document).ready(function(){
    var table = $('#pages_list').DataTable({
    	"order": [[ 2, 'desc' ]],
    	"columnDefs": [
    		{ "width": "18%", "targets": 0 },
			{ "width": "60%", "targets": 1 },
			{ "width": "12%", "targets": 2 },
			{ "width": "10%", "targets": 3 },
		]
    });

    $(document).on('click', '.confirmation', function (e) {
		//e.preventDefault();
        return confirm('Are you sure you want to delete this changelog?');
    });

    var actions_col = table.column(3);
    if ("{{Auth::user()->is('administrator|superadministrator')}}" == 1) {
        actions_col.visible(true);
        console.log("asdf");
    }
    else {
        actions_col.visible(false);
    }
});
</script>
@append
