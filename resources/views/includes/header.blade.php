@section('header_scripts')
@append

<!-- Logo -->
<a href="/dashboard" class="logo" style="position:fixed">
  <!-- mini logo for sidebar mini 50x50 pixels -->
  <span class="logo-mini">
    <!-- {!! Html::image("images/hubwire-logo-mini.png", "Logo", array('id'=>'header-logo-mini')),env('HTTPS',false) !!} -->
    {!! Html::image("images/pro-logo-mini.png", "Logo", array('id'=>'header-logo-mini')),env('HTTPS',false) !!}
    <!-- http://www.iconarchive.com/show/windows-8-icons-by-icons8/Transport-Wheel-icon.html -->
  </span>
  <!-- logo for regular state and mobile devices -->
  <span class="logo-lg">
    <!--{!! Html::image("images/arc-black.png", "Logo", array('id'=>'header-logo', 'class'=>'img-responsive center-block login-logo')),env('HTTPS',false) !!}-->
    {{ strtoupper(env('APP_NAME')) }}
  </span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top" role="navigation">
  <!-- Sidebar toggle button-->
  <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </a>
  <div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
      <!-- User Account: style can be found in dropdown.less -->
      <li class="dropdown user user-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <span class="hidden-xs capitalize"> {{ $user->first_name }} </span>
        </a>
        <ul class="dropdown-menu">
          <!-- Menu Footer-->
          <li class="user-footer">
            <ul class="nav">
              <li class="treeview"><a href="{{ route('users.edit', [$userId])}}">Edit Profile</a></li>
              @if(strcasecmp( $user->status, 'Unverified') != 0)
              <li class="treeview"><a href="#">My Subcription</a></li>
              <li class="treeview"><a href="{!! route('logout') !!}">Sign Out</a></li>
              @endif
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>