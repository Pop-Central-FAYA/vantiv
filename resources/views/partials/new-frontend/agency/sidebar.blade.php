<div class="side_nav">
    <div class="logo mb4">
        <img src="{{ asset('new_frontend/img/logo.svg') }}">
    </div>


    <div class="_nav">
        <ul>
            <li class="campaign_icon {{ Request::is('/') ? 'active' : ''  }}"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <!-- <li class="report_icon"><a href="">Reports</a></li> -->
            @if(!Auth::user()->hasRole('compliance'))
                <li class="clients_icon {{ Request::is('agency/clients/*') ? 'active' : ''  }}"><a href="{{ route('clients.list') }}">Clients</a></li>
            @endif
            @if(Auth::user()->hasRole('finance'))
                <li class="invoice_icon {{ Request::is('agency/invoices/*') ? 'active' : ''  }}"><a href="{{ route('invoices.all') }}">Invoices</a></li>
                <li class="wallet_icon {{ Request::is('agency/wallets/*') ? 'active' :  Request::is('agency/wallets/wallet-statement') ? 'active' : ''  }}"><a href="{{ route('agency_wallet.statement') }}">Wallet</a></li>
        @endif
        <!-- <li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="{{ route('agency.user_management') }}">User Management</a></li> -->
        </ul>
    </div>
    @if(!Auth::user()->hasRole('compliance'))
        <div class="_nav_button">
            <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a>
        </div>
        @endif
</div>
