<!-- FastClick -->
<script src="{{ asset('plugins/fastclick/fastclick.min.js',env('HTTPS',false)) }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/app.min.js',env('HTTPS',false)) }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js',env('HTTPS',false)) }}"></script>
<script src="{{ asset('js/common.js',env('HTTPS',false)) }}"></script>
@yield('footer_scripts')