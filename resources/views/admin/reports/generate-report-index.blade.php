@extends('layouts.master')

@section('header_scripts')
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css', env('HTTPS', false)) }}">
@append

@section('title')
    @lang('admin/reports.page_title_reports')
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="errors"></div>
    <section class="content-header">
        <h1>@lang('admin/reports.content_header_reports')</h1>
        @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            @lang('admin/reports.box_header_generate_report')
                        </h3>
                        
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        @include('admin.reports.partial.generate-report-filter')
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('footer_scripts')

@append