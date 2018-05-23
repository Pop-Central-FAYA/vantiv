<div id="sidebar" class="left-area">
    <div class="sidebar-header">
        <div class="logo"><a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt=""></a></div>
        <ul class="list-unstyled components">
            <li class="{{ Request::is('/') ? 'active' : ''  }}"> <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>

            <li class="{{ Request::is('clients/campaigns/*') ? 'active' : '' || Request::is('clients/campaigns') ? 'active' : ''  }}"> <a href="{{ route('client.campaign.all') }}"><i class="fa fa-rocket"></i><span>Campaign</span> </a>

            </li>
            {{--<li class="{{ Request::is('advertiser/invoices/*') ? 'active' : ''  }}"> <a href="{{ route('advertisers.invoices.all') }}" ><i class="fa fa-volume-down"></i><span>Invoice</span></a>--}}
                {{--<ul class="sub-menu dropdown">--}}
                    {{--<li><a href="{{ route('advertisers.invoices.all') }}"><i class="fa fa-address-card-o"></i> <span>All <br /> Invoices</span></a></li>--}}
                    {{--<li><a href="{{ route('advertisers.invoices.pending') }}"><i class="fa fa-address-book-o"></i><span>Pending<br />  Invoices </span></a></li>--}}
                {{--</ul>--}}
            {{--</li>--}}
            {{--<li class="{{ Request::is('wallets/*') ? 'active' : ''  }}"> <a href="{{ route('wallet.statement') }}"><i class="fa fa-briefcase"></i><span>Wallet</span></a>--}}
                {{--<ul class="sub-menu dropdown">--}}
                    {{--<li><a href="{{ route('wallet.create') }}"><i class="fa fa-address-card-o"></i> <span>Credit <br /> Wallet</span></a></li>--}}
                    {{--<li><a href="{{ route('wallet.statement') }}"><i class="fa fa-address-book-o"></i><span>Wallet<br />  Statement </span></a></li>--}}
                {{--</ul>--}}
            {{--</li>--}}
            <li class="{{ Request::is('clients/reports') ? 'active' : ''  }}"> <a href="{{ route('client.report.index') }}"><i class="fa fa-signal"></i><span>Report</span></a>

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