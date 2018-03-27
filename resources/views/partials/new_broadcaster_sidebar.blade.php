<div id="sidebar" class="left-area">
    <div class="sidebar-header">
        <div class="logo"><a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt=""></a></div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('/') ? 'active' : ''  }}"> <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
            <li class="{{ Request::is('campaign') ? 'active' : ''  }}"> <a href="{{ route('campaign.all') }}"><i class="fa fa-rocket"></i><span>Campaigns</span></a></li>
            <li class="{{ Request::is('mpos/*') ? 'active' : ''  }}"> <a href="{{ route('all-mpos') }}"><i class="fa fa-file"></i><span>MPOs</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('all-mpos') }}"><i class="fa fa-address-card-o"></i> <span>All<br /> MPOs</span></a></li>
                    <li><a href="{{ route('pending-mpos') }}"><i class="fa fa-address-book-o"></i><span>Pending<br /> MPOs</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('walk-in') ? 'active' : Request::is('walk-in/create') ? 'active' : Request::is('brands/*') ? 'active' : Request::is('brands') ? 'active' : ''  }}"> <a href="{{ route('walkins.all') }}" ><i class="fa fa-user-plus"></i><span>Walk-In</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('walkins.create') }}"><i class="fa fa-address-card-o"></i> <span>Add <br /> Walk-In</span></a></li>
                    <li><a href="{{ route('walkins.all') }}"><i class="fa fa-address-book-o"></i><span>Walk-In<br /> List</span></a></li>
                    <li><a href="{{ route('brand.create') }}"><i class="fa fa-address-card-o"></i> <span>Create<br /> Brand</span></a></li>
                    <li><a href="{{ route('brand.all') }}"><i class="fa fa-address-book-o"></i><span>Brand<br /> List</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('adslot') ? 'active' : Request::is('adslot/*') || Request::is('discount') ? 'active' : ''  }}"> <a href="{{ route('adslot.all')  }}" ><i class="fa fa-volume-down"></i><span>Ads Management</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('adslot.all') }}"><i class="fa fa-address-card-o"></i> <span>Adslots <br /> List</span></a></li>
                    <li><a href="{{ route('adslot.create') }}"><i class="fa fa-address-book-o"></i><span>Add<br /> Adslot</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('discounts') ? 'active' : ''  }}">
                <a href="{{ route('discount.index') }}">
                    <i class="fa fa-signal"></i><span>Discounts</span>
                </a>
            </li>
            <li class="{{ Request::is('reports') ? 'active' : ''  }}">
                <a href="{{ route('reports') }}">
                    <i class="fa fa-desktop"></i><span>Reports</span>
                </a>
            </li>
            <li class="{{ Request::is('') ? 'active' : Request::is('') || Request::is('') ? 'active' : ''  }}"> <a href="" ><i class="fa fa-user"></i><span>User Management</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href=""><i class="fa fa-address-card-o"></i> <span>User <br /> List</span></a></li>
                    <li><a href=""><i class="fa fa-address-book-o"></i><span>Add<br /> User</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('user/profile') ? 'active' : ''  }}">
                <a href="{{ route('user.profile') }}">
                    <i class="fa fa-user-md"></i><span>Profile</span>
                </a>
            </li>
            <li>
                <a href="{{ route('auth.logout') }}">
                    <i class="fa fa-power-off"></i><span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>