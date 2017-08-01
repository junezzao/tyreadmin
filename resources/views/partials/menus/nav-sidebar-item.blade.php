<li class="treeview {{ $segments[0] == 'data' ? 'active' : '' }}">
    <a href="{{ route('data.index') }}">
        <i class="fa fa-upload"></i> <span>@lang('terms.upload_data')</span>
    </a>
</li>

<li class="treeview {{ $segments[0] == 'history' ? 'active' : '' }}">
    <a href="{{ route('history.index') }}">
        <i class="fa fa-history"></i> <span>@lang('terms.tyre_history')</span>
    </a>
</li>

<li class="treeview {{ $segments[0] == 'reports' ? 'active' : '' }}">
    <a href="{{ route('reports.index') }}">
        <i class="fa fa-file-text-o"></i> <span>@lang('terms.reporting')</span>
    </a>
</li>

@if($user->can('view.user'))
<li class="treeview {{ $segments[0] == 'users' ? 'active' : '' }}">
    <a href="{{ URL::to('admin/users') }}">
        <i class="fa fa-users"></i> <span>Manage Users</span>
    </a>
</li>
@endif