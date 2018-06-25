<div id="sidebar" class="left-area">
    <div class="sidebar-header">
        <div class="logo"><a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt=""></a></div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('/') ? 'active' : ''  }}"> <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
            <li class="{{ Request::is('admin-broadcaster') ? 'active' : Request::is('admin-broadcaster/*') ? 'active' : ''  }}"><a href="{{ route('admin.broadcaster.index') }}"><i class="fa fa-bold"></i><span>Broadcaster Management</span></a>
            </li>
            <li class="{{ Request::is('regions') ? 'active' : Request::is('regions/*') ? 'active' : ''  }}"><a href="{{ route('admin.region.index') }}"><i class="fa fa-location-arrow"></i><span>Region Management</span></a>
            </li>
            <li class="{{ Request::is('target-audiences') ? 'active' : Request::is('target-audiences/*') ? 'active' : ''  }}"><a href="{{ route('target_audience.index') }}"><i class="fa fa-audio-description"></i><span>Target Audience Management</span></a>
            </li>
            <li class="{{ Request::is('day-parts') ? 'active' : Request::is('day-parts/*') ? 'active' : ''  }}"><a href="{{ route('admin.dayparts') }}"><i class="fa fa-paragraph"></i><span>Day Parts Management</span></a>
            </li>
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