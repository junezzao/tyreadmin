@extends('layouts.plain')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('changelog.box_header_index')
                        </h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
						@if(!empty($data))
							@foreach($data as $changelog)
								<h5>{!! $changelog['title'] !!}</h5>
								<p>{!! $changelog['content'] !!}</p>
								<hr/>
							@endforeach
						@endif
                    </div>
                </div>
            </div>
        </div>
        <div class="html_to_replace"></div>
    </section>
@stop
