<div class="side_nav load_this_div">
    <div class="logo mb4">
        <img src="{{ asset('new_frontend/img/logo.svg') }}">
    </div>

    <div class="_nav">
        <ul>
            @if(Auth::user()->hasRole('dsp.admin') || Auth::user()->hasRole('dsp.finance') || Auth::user()->hasRole('compliance') || Auth::user()->hasRole('media_planner'))
            <li class="campaign_icon {{ Request::is('/') ? 'active' : ''  }}"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @endif
            <!-- <li class="report_icon"><a href="">Reports</a></li> -->
            @if(Auth::user()->hasRole('dsp.admin') || Auth::user()->hasRole('media_planner'))
                <li class="clients_icon {{ Request::is('agency/clients/*') ? 'active' : ''  }}"><a href="{{ route('clients.list') }}">Clients</a></li>
            @endif
            @if(Auth::user()->hasRole('dsp.finance') || Auth::user()->hasRole('dsp.admin') || Auth::user()->hasRole('dsp.compliance'))
                <li class="invoice_icon {{ Request::is('agency/invoices/*') ? 'active' : ''  }}"><a href="{{ route('invoices.all') }}">Invoices</a></li>
            @endif
            @if(Auth::user()->hasRole('dsp.finance') || Auth::user()->hasRole('dsp.admin'))
                <li class="wallet_icon {{ Request::is('agency/wallets/*') ? 'active' :  Request::is('agency/wallets/wallet-statement') ? 'active' : ''  }}"><a href="{{ route('agency_wallet.statement') }}">Wallet</a></li>
            @endif
        <!-- <li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="{{ route('agency.user_management') }}">User Management</a></li> -->
        </ul>
    </div>

    <div class="_nav_button">
        @if(Auth::user()->hasRole('dsp.admin') || Auth::user()->hasRole('dsp.media_planner'))
            <a href="{{ route('agency.media_plan.criteria_form') }}" class="btn full block_disp uppercased align_center mb3"><span class="_plus"></span>New Media Plan</a>
        @endif
        @if(Auth::user()->hasRole('dsp.admin'))
            <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a>
        @endif
    </div>
</div>
