<div class="side_nav">
    <div class="logo mb4">
        <img src="{{ asset('new_frontend/img/logo.svg') }}">
    </div>


    <div class="_nav">
        <ul>
            <li class="dash_icon {{ Request::is('inventory-management/dashboard') ? 'active' : ''  }}"><a href="{{ route('broadcaster.inventory_management') }}">Dashboard</a></li>
            <li class="campaign_icon {{ Request::is('adslot') ? 'active' : Request::is('adslot/*') || Request::is('discount') ? 'active' : Request::is('positions/position-create') ? 'active' : ''  }}"><a href="{{ route('adslot.all') }}">Ad Slot Mgt</a></li>
            <li class="discount_icon {{ Request::is('discounts') ? 'active' : ''  }}"><a href="{{ route('discount.index') }}">Discounts</a></li>
            <!-- <li class="report_icon"><a href="">Reports</a></li> -->
            <!-- <li class="settings_icon"><a href="">User Management</a></li> -->
        </ul>
    </div>

    <div class="_nav_button">
        <a href="{{ route('broadcaster.campaign_management') }}" class="btn full block_disp uppercased align_center _campaign_mgt">Campaign Management</a>
    </div>

</div>
