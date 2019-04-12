<div class="side_nav">
    <div class="logo mb4">
        <img src="{{ asset('new_frontend/img/logo.svg') }}">
    </div>


    <div class="_nav">
        <ul>
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <li class="dash_icon {{ Request::is('inventory-management/dashboard') ? 'active' : ''  }}"><a href="{{ route('broadcaster.inventory_management') }}">Dashboard</a></li>
@endif
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <li class="campaign_icon {{ Request::is('adslot') ? 'active' : Request::is('adslot/*') || Request::is('positions/position-create') ? 'active' : ''  }}"><a href="{{ route('adslot.all') }}">Ad Slot Mgt</a></li>
@endif
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <li class="discount_icon {{ Request::is('discount') ? 'active' : Request::is('discount/create') ? 'active' : Request::is('discount/edit/*') ? 'active' : ''  }}"><a href="{{ route('discount.index') }}">Discounts</a></li>
@endif
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <li class="settings_icon {{ Request::is('program-management') ? 'active' : Request::is('program-management/create') ? 'active' : Request::is('program-management/edit/*') ? 'active' : ''  }}"><a href="{{ route('program.management.index') }}">Program Management</a></li>
@endif
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <li class="discount_icon {{ Request::is('rate-card-management') ? 'active' : Request::is('rate-card-management/create') ? 'active' : Request::is('rate-card-management/edit/*') ? 'active' : ''  }}"><a href="{{ route('rate_card.management.index') }}">Rate Card Management</a></li>
@endif
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <li class="discount_icon {{ Request::is('time-belt-management') ? 'active' : ''  }}"><a href="{{ route('time.belt.management.index') }}">Time Belt Management</a></li>
@endif
<!-- <li class="report_icon"><a href="">Reports</a></li> -->
<!-- <li class="settings_icon"><a href="">User Management</a></li> -->
</ul>
</div>

<div class="_nav_button">
@if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin'))
    <a href="{{ route('broadcaster.campaign_management') }}" class="btn full block_disp uppercased align_center _campaign_mgt">Campaign Management</a>
@endif
</div>

</div>
