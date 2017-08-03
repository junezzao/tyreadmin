<!DOCTYPE html>
<html>
	<head>
	    @include('includes.head')
		<?php $userId = Session::get('user')['user_id']; ?>
	</head>	
	<body class="hold-transition skin-black sidebar-mini">
		<div class="wrapper">
			<header class="main-header">
		        @include('includes.header')
		    </header>

			<aside class="main-sidebar">
				@include('partials.menus.nav-sidebar')
			</aside>

  			<div class="content-wrapper">
		    	@include('flash::message')
		        @yield('content')
		    </div>

		    <footer class="main-footer">
		        @include('includes.footer')
		    </footer>
		</div>
		@include('includes.footer_scripts')
	</body>
</html>