@extends('layouts.master')

@section('title')
    @lang('auth.page_title_dashboard')
@endsection

@section('header_scripts')
<!--link href="{{ secure_asset('css/signin.css') }}" rel="stylesheet" type="text/css"-->
@append

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        UPLOAD TEST
      </h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-xs-12">
        @if (count($errors) > 0)

  <div class="alert alert-danger">

    <strong>Whoops!</strong> There were some problems with your input.<br><br>

    <ul>

      @foreach ($errors->all() as $error)

        <li>{{ $error }}</li>

      @endforeach

    </ul>

  </div>

@endif
        <div class="col-xs-12">
        <h1>{{ $msg }}</h1>
        </div>
          {!! Form::open(array('url' => 'upload', 'files'=>true)) !!}
            {!! Form::file('testuploadfile') !!}
            {!! Form::submit('Upload File') !!}
          {!! Form::close() !!}
        </div>
        <div class="col-xs-12">
          {!! Form::open(array('url' => 'uploads', 'files'=>true)) !!}
            {!! Form::file('testuploadfiles[]', ['multiple' => 'multiple']) !!}
            {!! Form::submit('Upload Files') !!}
          {!! Form::close() !!}
        </div>
        <div class="col-xs-12">
          <h3>DELETE IMG</h3>
        </div>
        <div class="col-xs-12">
          {!! Form::open(array('url' => 'deleteupload', 'files'=>true)) !!}
            {!! Form::text('deletemediaid') !!}
            {!! Form::submit('Delete Files') !!}
          {!! Form::close() !!}
        </div>
    </div>
	</section>
    
@endsection

@section('footer_scripts')
<script type="text/javascript">

</script>
@append

