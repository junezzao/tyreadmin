@extends('layouts.master')

@section('title')
    Error
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <!-- <section class="content-header">
      <h1>Title</h1>
    </section> -->

    <!-- Main content -->
    <section class="content-header">
      <h1>
        500 Error
      </h1>
    </section>
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-red">500</h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>
                <p>
                Please report this issue by clicking <a href="mailto:techsupport@hubwire.com">here</a> and we will work on fixing it right away (Please remember to include details about your session and how the error occured).
                <br><br>
                Meanwhile, you may <a href="{{route('data.index')}}">return to the main page</a>.
                </p>
            </div>
        </div>
    </section>
@stop