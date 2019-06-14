<div class="side_nav load_this_div">
    <div class="logo mb4">
        <img src="{{ asset('new_frontend/img/logo.svg') }}">
    </div>

    <div class="_nav">
        <ul>
            @if(Auth::user()->hasPermissionTo('view.report'))
            <li class="campaign_icon {{ Request::is('/') ? 'active' : ''  }}"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @endif
            <!-- <li class="report_icon"><a href="">Reports</a></li> -->
            @if(Auth::user()->hasPermissionTo('view.client'))
                <li class="clients_icon {{ Request::is('agency/clients/*') ? 'active' : ''  }}"><a href="{{ route('clients.list') }}">Clients</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.invoice'))
                <li class="invoice_icon {{ Request::is('agency/invoices/*') ? 'active' : ''  }}"><a href="{{ route('invoices.all') }}">Invoices</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.wallet'))
                <li class="wallet_icon {{ Request::is('agency/wallets/*') ? 'active' :  Request::is('agency/wallets/wallet-statement') ? 'active' : ''  }}"><a href="{{ route('agency_wallet.statement') }}">Wallet</a></li>
            @endif

            @if(Auth::user()->hasRole('dsp.finance') || Auth::user()->hasRole('dsp.admin'))
                <li class="wallet_icon {{ Request::is('agency/media-assets/*') ? 'active' :  Request::is('agency/media-assets/*') ? 'active' : ''  }}"><a href="{{ route('agency.media_assets') }}">Media Assets</a></li>
            @endif
        <!-- <li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="{{ route('agency.user_management') }}">User Management</a></li> -->
        </ul>
    </div>

    <div class="_nav_button">
         @if(Auth::user()->hasPermissionTo('create.media_plan'))
            <a href="{{ route('agency.media_plan.criteria_form') }}" class="btn full block_disp uppercased align_center mb3"><span class="_plus"></span>New Media Plan</a>
        @endif
        @if(Auth::user()->hasPermissionTo('create.campaign'))
            <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a>
        @endif
    </div>
</div>
