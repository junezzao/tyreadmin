@section('header_scripts')
@append

<div class="logo">
  <span class="logo-mini">
    {!! Html::image("images/pro-logo-mini.png", "Logo", array('id'=>'header-logo-mini')),env('HTTPS',false) !!}
  </span>

  <span class="logo-lg">
    {!! Html::image("images/pro-logo-rect.png", "Logo", array('id'=>'header-logo', 'class'=>'img-responsive center-block login-logo')),env('HTTPS',false) !!}
  </span>
</div>

<nav class="navbar navbar-static-top" role="navigation">
  <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </a>
  
</nav>