<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
    @if(strcasecmp($user->status, 'Unverified') != 0)
    <!-- search form -->
    {{--<form action="#" method="get" class="sidebar-form">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
            </span>
        </div>
    </form>--}}
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <?php
        $segments = explode('.', Request::route()->getName());
        if($segments[0] == 'byChannel') {
            unset($segments[0]);
            $segments = array_values($segments);
        }
        if($segments[0] == 'admin') {
            unset($segments[0]);
            $segments = array_values($segments);
        }

        $selectedChannelId = 0;
        $byChannelFlag = false;
        $paths = explode('/', Request::path());
        if($paths[0] == 'byChannel') {
            $selectedChannelId = $paths[1];
            $byChannelFlag = true;
        }

        $isChannelMenu = false;
    ?>
    
    <ul class="sidebar-menu">
        <!--<li class="header"></li>
        <li class="treeview">
            <a href="{{ URL::to('dashboard') }}">
                <i class="fa fa-home fa-lg"></i> <span>Dashboard</span> 
            </a>
        </li>-->

        <!--<li class="header">ADMIN NAVIGATION</li>-->
        @if($user->hasRoleOtherThan('channelmanager'))
            @include('partials.menus.nav-sidebar-item', ['byChannel' => null, 'channel_id' => null])
        @endif
        @if($user->is('channelmanager'))
            @foreach($user->channels() as $channel)
                <li class="treeview{{ $selectedChannelId==$channel->id ? ' active':'' }}">
                    <a href="#">
                        <i class="fa fa-archive"></i> <span>{{ $channel->name }}</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php 
                            $isChannelMenu = true;
                        ?>
                        @include('partials.menus.nav-sidebar-item', ['byChannel' => 'byChannel/'.$channel->id.'/', 'channel_id' => $channel->id])
                    </ul>
                </li>
            @endforeach
        @endif
    </ul>
    @endif
</section>
<!-- /.sidebar -->