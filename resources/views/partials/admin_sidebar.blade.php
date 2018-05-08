<div id="sidebar" class="left-area">
    <div class="sidebar-header">
        <div class="logo"><a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt=""></a></div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('/') ? 'active' : ''  }}"> <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
            <li class="{{ Request::is('industry') ? 'active' : Request::is('industry/*') ? 'active' : ''  }}"><a href=""><i class="fa fa-industry"></i><span>Industry Management</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('industry.index') }}"><i class="fa fa-industry"></i><span>All Industries</span></a></li>
                    <li><a href="{{ route('sub_industry.index') }}"><i class="fa fa-industry"></i><span>Sub Industries</span></a></li>
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