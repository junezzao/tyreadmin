<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- scripts -->
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<!-- stylesheets -->

<link href="{{ asset('css/bootstrap.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
<link href="{{ asset('css/style.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('plugins/select2/select2.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">


@yield('header_scripts')
<title>@yield('title') | Hubwire </title>