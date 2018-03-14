<div id="sidebar" class="left-area">
    <div class="sidebar-header">
        <div class="logo"><a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt=""></a></div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('/') ? 'active' : ''  }}"> <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
            <li class="{{ Request::is('campaign') ? 'active' : ''  }}"> <a href="{{ route('campaign.all') }}"><i class="fa fa-user"></i><span>Campaigns</span></a></li>
            <li class="{{ Request::is('mpos/*') ? 'active' : ''  }}"> <a href="{{ route('all-mpos') }}"><i class="fa fa-th-large"></i><span>MPOs</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('all-mpos') }}"><i class="fa fa-address-card-o"></i> <span>All<br /> MPOs</span></a></li>
                    <li><a href="{{ route('pending-mpos') }}"><i class="fa fa-address-book-o"></i><span>Pending<br /> MPOs</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('brands/*') ? 'active' : Request::is('brands') ? 'active' : ''  }}"> <a href="{{ route('brand.all') }}"><i class="fa fa-th-large"></i><span>Brands Management</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('brand.create') }}"><i class="fa fa-address-card-o"></i> <span>Create<br /> Brand</span></a></li>
                    <li><a href="{{ route('brand.all') }}"><i class="fa fa-address-book-o"></i><span>Brands<br /> List</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('walkins/*') ? 'active' : Request::is('walkins') ? 'active' : ''  }}"> <a href="{{ route('walkins.all') }}" ><i class="fa fa-volume-down"></i><span>Walkins</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('walkins.create') }}"><i class="fa fa-address-card-o"></i> <span>Add <br /> Walkins</span></a></li>
                    <li><a href="{{ route('walkins.all') }}"><i class="fa fa-address-book-o"></i><span>Walkins<br /> List</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('adslot') ? 'active' : Request::is('adslot/*') || Request::is('discount') ? 'active' : ''  }}"> <a href="{{ route('adslot.all')  }}" ><i class="fa fa-volume-down"></i><span>Ads Management</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('adslot.all') }}"><i class="fa fa-address-card-o"></i> <span>Adslots <br /> List</span></a></li>
                    <li><a href="{{ route('adslot.create') }}"><i class="fa fa-address-book-o"></i><span>Add<br /> Adslot</span></a></li>
                    <li><a href="{{ route('discount.index') }}"><i class="fa fa-address-book-o"></i><span>Discounts</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('reports') ? 'active' : ''  }}"> <a href="{{ route('reports') }}"><i class="fa fa-signal"></i><span>Reports</span></a>
            </li>
            <li class="{{ Request::is('user/profile') ? 'active' : ''  }}">
                <a href="{{ route('user.profile') }}">
                    <i class="fa fa-user"></i><span>Profile</span>
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