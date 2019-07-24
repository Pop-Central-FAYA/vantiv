<div class="side_nav">
    <div class="logo mb4">
        {!! AssetsHelper::logo() !!}
    </div>


    <div class="_nav">
        <ul>
            @if(Auth::user()->hasPermissionTo('view.campaign'))
                <li class="campaign_icon {{ Request::is('campaign-management/dashboard') ? 'active' : ''  }}"><a href="{{ route('broadcaster.campaign_management') }}">Dashboard</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.campaign'))
                <li class="campaign_icon {{ Request::is('campaign/campaigns-list') ? 'active' : ''  }}"><a href="{{ route('campaign.list') }}">Campaigns</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.client'))
                <li class="clients_icon {{ Request::is('walk-in') ? 'active' : '' || Request::is('walk-in/walk-in/details/*') ? 'active' : ''  }}"><a href="{{ route('walkins.all') }}">Walk-Ins</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.mpo'))
                <li class="invoice_icon {{ Request::is('mpos/*') ? 'active' : ''  }}"><a href="{{ route('all-mpos') }}">MPO</a></li>
            @endif
            <!-- <li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="">User Management</a></li> -->
        </ul>
    </div>

    <div class="_nav_button">
        @if(Auth::user()->hasPermissionTo('view.inventory'))
            <a href="{{ route('broadcaster.inventory_management') }}" class="btn full block_disp uppercased align_center _campaign_mgt">Inventory Management</a><p><br></p>
        @endif
        @if(Auth::user()->companies->count() == 1)
            @if(Auth::user()->hasPermissionTo('create.campaign'))
                <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a>
            @endif
        @endif
    </div>

</div>
