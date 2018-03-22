<div id="sidebar" class="left-area">
    <div class="sidebar-header">
        <div class="logo"><a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt=""></a></div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('/') ? 'active' : ''  }}"> <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('dashboard') }}"><i class="fa fa-address-card-o"></i> <span>Agency <br /> Dashboard</span></a></li>
                    <li><a href="{{ route('agency.dashboard') }}"><i class="fa fa-address-book-o"></i><span>Clients <br /> Dashboard</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('agency/clients/*') ? 'active' : ''  }}"> <a href="{{ route('clients.list') }}"><i class="fa fa-user"></i><span>Clients</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('clients.create') }}"><i class="fa fa-address-card-o"></i> <span>Add<br />  Client</span></a></li>
                    <li><a href="{{ route('clients.list') }}"><i class="fa fa-address-book-o"></i><span>Clients<br />  List</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('client-brands/*') ? 'active' : ''  }}"> <a href="{{ route('agency.brand.all') }}"><i class="fa fa-th-large"></i><span>Brands Management</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('agency.brand.create') }}"><i class="fa fa-address-card-o"></i> <span>Create<br />  Brand</span></a></li>
                    <li><a href="{{ route('agency.brand.all') }}"><i class="fa fa-address-book-o"></i><span>Brand<br />  List</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('agency/campaigns/*') ? 'active' : ''  }}"> <a href="{{ route('agency.campaign.all') }}"><i class="fa fa-rocket"></i><span>Campaign</span> </a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('agency.campaign.create') }}"><i class="fa fa-address-card-o"></i> <span>Create<br />  Campaign</span></a></li>
                    <li><a href="{{ route('agency.campaign.all') }}"><i class="fa fa-address-book-o"></i><span>Campaign <br /> List</span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('agency/invoices/*') ? 'active' : ''  }}"> <a href="{{ route('invoices.all') }}" ><i class="fa fa-volume-down"></i><span>Invoice</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('invoices.all') }}"><i class="fa fa-address-card-o"></i> <span>All <br /> Invoices</span></a></li>
                    <li><a href="{{ route('invoices.pending') }}"><i class="fa fa-address-book-o"></i><span>Pending<br />  Invoices </span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('agency/wallets/*') ? 'active' : ''  }}"> <a href="{{ route('agency_wallet.statement') }}"><i class="fa fa-briefcase"></i><span>Wallet</span></a>
                <ul class="sub-menu dropdown">
                    <li><a href="{{ route('agency_wallet.create') }}"><i class="fa fa-address-card-o"></i> <span>Credit <br /> Wallet</span></a></li>
                    <li><a href="{{ route('agency_wallet.statement') }}"><i class="fa fa-address-book-o"></i><span>Wallet<br />  Statement </span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('agency/reports') ? 'active' : ''  }}">
                <a href="{{ route('reports.index') }}">
                    <i class="fa fa-signal"></i><span>Report</span>
                </a>
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