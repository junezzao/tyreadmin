<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link href="{{ asset('bootstrap/css/bootstrap.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">

<link href="{{ asset('css/app.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('css/skins/skin-black.css', env('HTTPS', false)) }}">
<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css',env('HTTPS',false)) }}">

<script src="http://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>

<link href="{{ asset('plugins/select2/select2.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/select2.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/select2/select2.full.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>

<script src="{{ asset('js/initialize_select2.js',env('HTTPS',false)) }}"></script>

@yield('header_scripts')
<title>@yield('title') | Tyre Admin </title>
<link rel="icon" href="{{ asset('favicon.png',env('HTTPS',false)) }}">
@if(strtolower(env('APP_ENV')) != 'local')
	@include('includes.ga-script')
@endif
