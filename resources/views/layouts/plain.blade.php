<!DOCTYPE html>
<html>
	<head>
	    @include('includes.head')
	</head>
	<body class="plain">
		<div class="container">

		    <header class="row">
		    </header>

		    <div id="main" class="row">
		    	@include('flash::message')
		        @yield('content')

		    </div>

		    <footer class="row">
		    </footer>

		</div>
		@include('includes.footer_scripts')
	</body>
</html>