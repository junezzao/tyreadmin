@extends('layouts.master')

@section('header_scripts')
<!-- daterange picker -->
<link rel="stylesheet" href="{{ secure_asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
<!-- DataTables -->
<script src="{{ secure_asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ secure_asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<link href="{{ secure_asset('plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css">
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
                            @lang('admin/reports.box_header_generate_report')s
                        </h3>
                        
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <!-- report search filters -->
                        @include('admin.generate-report.report-search')
                        <span id="reportrange">
                            <span></span>
                        </span>
                        <!--
                        <button type="button" class="btn btn-success pull-right">
                            <span>
                                <i class="fa fa-download"></i> Download
                            </span>
                        </button>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop