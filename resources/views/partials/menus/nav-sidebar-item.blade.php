<li class="treeview {{ $segments[0] == 'data' ? 'active' : '' }}">
    <a href="{{ route('data.index') }}">
        <i class="fa fa-upload fa-lg"></i> <span>@lang('terms.upload_data')</span>
    </a>
</li>

<li class="treeview {{ $segments[0] == 'history' ? 'active' : '' }}">
    <a href="{{ route('history.index') }}">
        <i class="fa fa-history fa-lg"></i> <span>@lang('terms.tyre_history')</span>
    </a>
</li>

<li class="treeview {{ $segments[0] == 'reports' ? 'active' : '' }}">
    <a href="{{ route('reports.index') }}">
        <i class="fa fa-file-text-o fa-lg"></i> <span>@lang('terms.reporting')</span>
    </a>
</li>

@if($user->can('view.user'))
<li class="treeview {{ $segments[0] == 'users' ? 'active' : '' }}">
    <a href="{{ URL::to('admin/users') }}">
        <i class="fa fa-users fa-lg"></i> <span>Manage Users</span>
    </a>
</li>
@endif

<li class="treeview {{ $segments[0] == 'user' ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-user-o fa-lg"></i> <span>My Profile</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('user.editUser') }}">Edit Profile</a>
        </li>
        @if(strcasecmp( $user->category, 'Super Administrator') != 0)
        <li>
            <a href="{{ route('user.subscription') }}">Manage Subscription</a>
        </li>
        @endif
        <li>
            <a href="{{ route('user.changePassword') }}">Change Password</a>
        </li>
        <li><a href="{!! route('logout') !!}">Sign Out</a></li>
    </ul>
</li>