<script src="http://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>

<script src="{{ asset('js/app.min.js',env('HTTPS',false)) }}?version={{ env('APP_VERSION') }}"></script>
@yield('footer_scripts')