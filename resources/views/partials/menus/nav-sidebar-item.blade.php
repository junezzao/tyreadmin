<!-- @if($user->can('view.product') && session('modules')['products']['enabled'])
<li class="treeview {{ ($byChannelFlag==false && in_array($segments[0], ['products', 'reject'])) || ($byChannelFlag==true && $segments[0]=='products' && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-archive"></i> <span>Product Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        @if($user->can('view.restock'))
        @if($isChannelMenu == false)
        <li><a href="{{ URL::to($byChannel.'products/create') }}"><i class="fa fa-circle{{ Request::route()->getName()=='products.create.index' || (Request::route()->getName()=='byChannel.products.create.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> View Created Products</a></li>
        @endif
        <li><a href="{{ URL::to($byChannel.'products/restock') }}"><i class="fa fa-circle{{ Request::route()->getName()=='products.restock.index' || (Request::route()->getName()=='byChannel.products.restock.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> View Restock Products</a></li>
        @endif
        <li><a href="{{ URL::to($byChannel.'products/stock_transfer') }}"><i class="fa fa-circle{{ Request::route()->getName()=='products.stock_transfer.index' || (Request::route()->getName()=='byChannel.products.stock_transfer.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Stock Transfer</a></li>
        @if($isChannelMenu == false)
        <li><a href="{{ URL::to($byChannel.'products/inventory') }}"><i class="fa fa-circle{{ Request::route()->getName()=='products.inventory.index' || (Request::route()->getName()=='byChannel.products.inventory.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Inventory</a></li>
        @endif
        <li><a href="{{ URL::to($byChannel.'admin/reject') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.reject.index' || (Request::route()->getName()=='byChannel.admin.reject.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Reject History</a></li>
        <li><a href="{{ route('products.manifests.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='products.manifests.index'?'':'-o'}}"></i> Goods Take Out Manifest</a></li>
        @if($user->can('view.category'))
        <li><a href="{{ route('products.categories.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='products.categories.index'?'':'-o'}}"></i> Categories</a></li>
        @endif
    </ul>
</li>
@endif

@if(session('modules')['fulfillment']['enabled'])
<li class="treeview {{ ($byChannelFlag==false && ($segments[0]=='fulfillment' || $segments[0]=='orders')) || ($byChannelFlag==true && ($segments[0]=='fulfillment' || $segments[0]=='orders') && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-truck"></i> <span>Fulfillment</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{ URL::to($byChannel.'orders') }}"><i class="fa fa-circle{{ Request::route()->getName()=='orders.index' || (Request::route()->getName()=='byChannel.orders.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Live Transactions</a></li>
        <?php ?>
        @if($user->can('view.failedorders'))
        <li><a href="{{ URL::to($byChannel.'admin/fulfillment/failed_orders') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.fulfillment.failed_orders.index' || (Request::route()->getName()=='byChannel.admin.fulfillment.failed_orders.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Failed Orders</a></li>
        @endif
        <li><a href="{{ URL::to($byChannel.'orders/create') }}"><i class="fa fa-circle{{ Request::route()->getName()=='orders.create' || (Request::route()->getName()=='byChannel.orders.create' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Create Manual Order</a></li>
        <?php //@endif ?>
        @if($user->can('view.return'))
        <li><a href="{{ URL::to($byChannel.'admin/fulfillment/return') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.fulfillment.return.index' || (Request::route()->getName()=='byChannel.admin.fulfillment.return.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Returns List</a></li>
        @endif
        @if($isChannelMenu == false)
        <li><a href="{{ route('admin.fulfillment.manifests.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.fulfillment.manifests.index'?'':'-o'}}"></i> Picking Manifests</a></li>
        @endif
    </ul>
</li>
@endif

@if($user->can('view.channel') && session('modules')['channels']['enabled'])
<li class="treeview {{ ($byChannelFlag==false && ($segments[0]=='channels' || $segments[0]=='channel-type')) || ($byChannelFlag==true && ($segments[0]=='channels' || $segments[0]=='channel-type') && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-television"></i> <span>Channel Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{ URL::to($byChannel.'admin/channels') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.channels.index' || (Request::route()->getName()=='byChannel.admin.channels.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> View Channels</a></li>
        @if(!$user->is('channelmanager') && $user->can('view.channeltype'))
        <li><a href="{{ URL::to('admin/channel-type') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.channel-type.index'?'':'-o'}}"></i> View Channel Types</a></li>
        @endif
        <li><a href="{{ URL::to($byChannel.'channels/inventory') }}"><i class="fa fa-circle{{ Request::route()->getName()=='channels.inventory.index' || (Request::route()->getName()=='byChannel.channels.inventory.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Inventory</a></li>
        <li><a href="{{ URL::to($byChannel.'admin/channels/sync_history') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.channels.sync_history.index' || (Request::route()->getName()=='byChannel.admin.channels.sync_history.index' && $selectedChannelId==$channel->id) ? '' : '-o' }}"></i> Sync History</a></li>
    </ul>
</li>
@endif-->


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
    <!--<ul class="treeview-menu">
        <li>
            <a href="{{ URL::to('admin/users') }}">
                <i class="fa fa-circle{{ Request::route()->getName()=='admin.users.index' || (Request::route()->getName()=='byChannel.admin.users.index' && $selectedChannelId == $channel->id) ? '' : '-o' }}"></i> View Users
            </a>
        </li>
        @if (Auth::user()->can('create.user'))
            <li>
                <a href="{{ URL::to($byChannel.'admin/users/create') }}">
                    <i class="fa fa-circle{{ Request::route()->getName() == 'admin.users.create' || (Request::route()->getName() == 'byChannel.admin.users.create' && $selectedChannelId == $channel->id) ? '' : '-o' }}"></i> Create New User
                </a>
            </li>
        @endif
    </ul>-->
</li>
@endif

<!--@if($user->can('view.roles'))
<li class="treeview {{ ($byChannelFlag == false && $segments[0] == 'roles') || ($byChannelFlag == true && $segments[0]=='roles' && $selectedChannelId == $channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-globe"></i> <span>Access Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ URL::to($byChannel.'admin/roles') }}">
                <i class="fa fa-circle{{ Request::route()->getName() == 'admin.roles.index' || (Request::route()->getName() == 'byChannel.admin.roles.index' && $selectedChannelId == $channel->id) ? '' : '-o' }}"></i> View Roles
            </a>
        </li>
        @if (Auth::user()->can('create.roles'))
            <li>
                <a href="{{ URL::to($byChannel . 'admin/roles/create') }}">
                    <i class="fa fa-circle{{ Request::route()->getName() == 'admin.roles.create' || (Request::route()->getName() == 'byChannel.admin.roles.create' && $selectedChannelId == $channel->id) ? '' : '-o' }}"></i> Create New Role
                </a>
            </li>
        @endif
    </ul>
</li>
@endif

@if($user->can('view.merchant'))
<li class="treeview {{ ($byChannelFlag==false && $segments[0]=='merchants') || ($byChannelFlag==true && $segments[0]=='merchants' && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-handshake-o"></i> <span>Merchant Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{ URL::to($byChannel.'admin/merchants') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.merchants.index' || (Request::route()->getName()=='byChannel.admin.merchants.index' && $selectedChannelId==$channel->id)  ? '' : '-o' }}"></i> View Merchants</a></li>
        @if($user->can('create.merchant'))
        <li><a href="{{ URL::to('admin/merchants/create') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.merchants.create'?'':'-o'}}"></i> Create New Merchant</a></li>
        @endif
    </ul>
</li>
@endif

@if($user->can('view.supplier'))
<li class="treeview {{ ($byChannelFlag==false && $segments[0]=='suppliers') || ($byChannelFlag==true && $segments[0]=='suppliers' && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-clipboard"></i> <span>Suppliers Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{ URL::to($byChannel.'admin/suppliers') }}"><i class="fa fa-circle{{ Request::route()->getName()=='admin.suppliers.index' || (Request::route()->getName()=='byChannel.admin.suppliers.index' && $selectedChannelId==$channel->id)  ? '' : '-o' }}"></i> View Suppliers</a></li>
        @if($user->can('create.supplier'))
        <li><a href="{{ URL::route('admin.suppliers.create') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.suppliers.create'?'':'-o'}}"></i> Create New Supplier</a></li>
        @endif
    </ul>
</li>
@endif
@permission('view.brand')
<li class="treeview {{ ($byChannelFlag==false && $segments[0]=='brands') || ($byChannelFlag==true && $segments[0]=='brands' && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-tags"></i> <span>Brands Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{ URL::to($byChannel.'brands') }}"><i class="fa fa-circle{{ Request::route()->getName()=='brands.index' || (Request::route()->getName()=='byChannel.brands.index' && $selectedChannelId==$channel->id)  ? '' : '-o' }}"></i> View Brands</a></li>
        @permission('create.brand')
        <li><a href="{{ route('brands.create') }}"><i class="fa fa-circle{{Request::route()->getName()=='brands.create'?'':'-o'}}"></i> Create New Brand</a></li>
        @endpermission
    </ul>
</li>
@endpermission

@permission('view.contract|view.channelcontract|create.contract|create.channelcontract')
<li class="treeview {{ ($byChannelFlag==false && $segments[0]=='contracts') || ($byChannelFlag==true && $segments[0]=='contracts' && $selectedChannelId==$channel->id) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-gavel"></i> <span>Contracts Management</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        @permission('view.contract|create.contract')
        <li {{ (strpos(Request::route()->getName(), 'contracts') !== false && strpos(Request::route()->getName(), 'contracts.channel') === false) ? 'class=active' : '' }}>
            <a href="#"><i class="fa fa-circle-o"></i> Contracts <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                 @permission('view.contract')
                <li><a href="{{ URL::to($byChannel.'contracts') }}"><i class="fa fa-circle{{ Request::route()->getName()=='contracts.index' || (Request::route()->getName()=='byChannel.contracts.index' && $selectedChannelId==$channel->id)  ? '' : '-o' }}"></i> View Contracts</a></li>
                @endpermission
                @permission('create.contract')
                <li><a href="{{ route('contracts.create') }}"><i class="fa fa-circle{{Request::route()->getName()=='contracts.create'?'':'-o'}}"></i> Create New Contract</a></li>
                @endpermission
            </ul>
        </li>
        @endpermission

        @permission('view.channelcontract|create.channelcontract')
        <li {{ strpos(Request::route()->getName(), 'contracts.channel') !== false ? 'class=active' : '' }}>
            <a href="#"><i class="fa fa-circle-o"></i> Channel Contracts <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                @permission('view.channelcontract')
                <li><a href="{{ URL::to($byChannel.'contracts/channels') }}"><i class="fa fa-circle{{Request::route()->getName()=='contracts.channels.index'?'':'-o'}}"></i> View Contracts</a></li>
                @endpermission
                @permission('create.channelcontract')
                <li><a href="{{ URL::to($byChannel.'contracts/channels/create') }}"><i class="fa fa-circle{{Request::route()->getName()=='contracts.channels.create'?'':'-o'}}"></i> Create Channel Contract</a></li>
                @endpermission
            </ul>
        </li>
        @endpermission
        @permission('view.contractcalculator')
        <li><a href="{{ route('contracts.contract_calculator') }}"><i class="fa fa-circle{{Request::route()->getName()=='contracts.contract_calculator'?'':'-o'}}"></i> Contracts Calculator </a></li>
        @endpermission
    </ul>
</li>
@endpermission

@permission('view.financereport')
<li class="treeview {{ in_array($segments[0], ['reports', 'generate-report', 'tp_reports', 'issuing_companies']) ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-file-text-o"></i> <span>Finance & Reporting</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        @permission('view.reports')
        <li><a href="{{ route('admin.reports.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.reports.index'?'':'-o'}}"></i> View Reports</a></li>
        @endpermission

        @permission('view.generatereport')
        <li><a href="{{ route('admin.generate-report.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.generate-report.index'?'':'-o'}}"></i> Generate Reports</a></li>
        @endpermission

        <li><a href="{{ route('admin.tp_reports.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.tp_reports.index'?'':'-o'}}"></i> Third Party Reports</a></li>

        @permission('view.issuingcompany')
        <li><a href="{{ route('admin.issuing_companies.index') }}"><i class="fa fa-circle{{Request::route()->getName()=='admin.issuing_companies.index'?'':'-o'}}"></i> Manage Issuing Company</a></li>
        @endpermission
    </ul>
</li>
@endpermission

@if($user->is('clientadmin'))
<li class="treeview {{ (in_array('account_details', $segments) ? 'active' : '' ) }}">
    <a href="{{ route('user.account_details') }}"><i class="fa fa-cog"></i> Account Details</a>
</li>
@endif

@if($user->is('superadministrator'))
<li class="treeview">
    <a href="{{ route('admin.configurations.index') }}">
        <i class="fa fa-cog"></i> <span>Modules Configurations</span>
    </a>
</li>
@endif

@if($user->is('superadministrator'))
<li class="treeview">
    <a href="{{ route('admin.testing.index') }}">
        <i class="fa fa-user-secret"></i> <span>Testing</span>
    </a>
</li>
@endif-->