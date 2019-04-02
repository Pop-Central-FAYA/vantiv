<div class="side_nav">
    <div class="logo mb4">
        <a href="{{ route('dashboard') }}">
        <img src="{{ asset('new_frontend/img/logo.svg') }}"></a>
    </div>


    <div class="_nav">
        <ul>
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.scheduler') || Auth::user()->hasRole('ssp.media_buyer'))
                <li class="campaign_icon {{ Request::is('campaign-management/dashboard') ? 'active' : ''  }}"><a href="{{ route('broadcaster.campaign_management') }}">Dashboard</a></li>
            @endif
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.media_buyer'))
                <li class="clients_icon {{ Request::is('walk-in') ? 'active' : '' || Request::is('walk-in/walk-in/details/*') ? 'active' : ''  }}"><a href="{{ route('walkins.all') }}">Walk-Ins</a></li>
            @endif
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.scheduler'))
                <li class="invoice_icon {{ Request::is('mpos/*') ? 'active' : ''  }}"><a href="{{ route('all-mpos') }}">MPO</a></li>
            @endif
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
                @if(Auth::user()->companies->count() > 1)
                    <li class="discount_icon {{ Request::is('discounts') ? 'active' : ''  }}"><a href="{{ route('discount.index') }}">Discounts</a></li>
                @endif
            @endif
            <!-- <li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="">User Management</a></li> -->
        </ul>
    </div>

    <div class="_nav_button">
        @if(Auth::user()->companies->count() == 1)
            @if(Auth::user()->hasRole('ssp.admin'))
                <a href="{{ route('broadcaster.inventory_management') }}" class="btn full block_disp uppercased align_center _campaign_mgt">Inventory Management</a><p><br></p>
            @endif
            @if(Auth::user()->hasRole('ssp.media_buyer'))
                <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a>
            @endif
        @endif
    </div>

</div>
