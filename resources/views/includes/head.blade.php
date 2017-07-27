<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.5 -->
<link href="{{ asset('bootstrap/css/bootstrap.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<!-- Application Stylesheet -->


<link href="{{ asset('css/app.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('css/skins/skin-black.css', env('HTTPS', false)) }}">
<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css',env('HTTPS',false)) }}">
<!--<link rel="stylesheet" href="{{ asset('css/ionicons.min.css',env('HTTPS',false)) }}">-->

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<script src="{{ asset('js/jquery-v1.11.1.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<!-- for select2 -->
<link href="{{ asset('plugins/select2/select2.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/select2.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/select2/select2.full.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<!-- Initialize select2 dropdowns -->
<script src="{{ asset('js/initialize_select2.js',env('HTTPS',false)) }}"></script>

<!-- AdminLTE App -->
<!--script src="{{ asset('dist/js/app.min.js') }}"></script-->
@yield('header_scripts')
<title>@yield('title') | Tyre Admin </title>
<link rel="icon" href="{{ asset('favicon.png',env('HTTPS',false)) }}">
@if(strtolower(env('APP_ENV')) != 'local')
	@include('includes.ga-script')
@endif
