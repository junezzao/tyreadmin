@extends('layouts.master')

@section('title')
Error
@stop

@section('content')
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-red">{{ $errorCode }}</h2>
            <div class="error-content">
                <h1>{{ config('globals.status_code.'.$errorCode) }}</h1>
                <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>
                <p>
                Please report this issue by clicking <a href="mailto:{{ env('SYSTEM_SUPPORT_EMAIL') }}">here</a> and we will work on fixing it right away. Meanwhile, you may <a href="{{route('data.index')}}">return to the main page</a>.
                </p>
            </div>
        </div>
    </section>
@stop