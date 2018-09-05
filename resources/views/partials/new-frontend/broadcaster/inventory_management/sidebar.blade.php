<div class="side_nav">
    <div class="logo mb4">
        <img src="{{ asset('new_frontend/img/logo.svg') }}">
    </div>


    <div class="_nav">
        <ul>
            <li class="dash_icon {{ Request::is('inventory-management/dashboard') ? 'active' : ''  }}"><a href="{{ route('broadcaster.inventory_management') }}">Dashboard</a></li>
            <li class="campaign_icon"><a href="">Ad Slot Mgt</a></li>
            <li class="discount_icon"><a href="">Discounts</a></li>
            <li class="report_icon"><a href="">Reports</a></li>
            <li class="settings_icon"><a href="">User Management</a></li>
        </ul>
    </div>

    <div class="_nav_button">
        <a href="{{ route('bradcaster.campaign_management') }}" class="btn full block_disp uppercased align_center _campaign_mgt">Campaign Management</a>
    </div>

</div>



{{--<div class="side_nav">--}}
    {{--<div class="logo mb4">--}}
        {{--<a href="{{ route('dashboard') }}">--}}
            {{--<img src="{{ asset('new_frontend/img/logo.svg') }}"></a>--}}
    {{--</div>--}}


    {{--<div class="_nav">--}}
        {{--<ul>--}}
            {{--<li class="campaign_icon {{ Request::is('campaign-management/dashboard') ? 'active' : ''  }}"><a href="{{ route('bradcaster.campaign_management') }}">Dashboard</a></li>--}}
            {{--<li class="report_icon"><a href="">Reports</a></li>--}}

            {{--<span class="small_faint"></span>--}}
            {{--<li class="clients_icon {{ Request::is('walk-in') ? 'active' : '' || Request::is('walk-in/walk-in/details/*') ? 'active' : ''  }}"><a href="{{ route('walkins.all') }}">Walk-Ins</a></li>--}}
            {{--<li class="invoice_icon {{ Request::is('mpos/*') ? 'active' : ''  }}"><a href="{{ route('all-mpos') }}">MPO's</a></li>--}}
            {{--<li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="">User Management</a></li>--}}
        {{--</ul>--}}
    {{--</div>--}}

    {{--<div class="_nav_button">--}}
        {{--<a href="" class="btn full block_disp uppercased align_center">Inventory Mgnt.</a><p><br></p>--}}
        {{--<a href="{{ route('campaign.create') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a>--}}
    {{--</div>--}}

{{--</div>--}}