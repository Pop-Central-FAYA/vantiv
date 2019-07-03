<div class="side_nav load_this_div">
    <div class="logo mb4">
    <svg width="119px" height="36px" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 553.34 216.48"><defs><style>.cls-1{fill:#64c4ce;}.cls-2{fill:#575758;}.cls-3{fill:#4eaeaf;}.cls-4{font-size:91.26px;fill:#1d1d1d;font-family:LemonMilk, "Lemon/Milk";}.cls-5{letter-spacing:-0.12em;}.cls-6{letter-spacing:-0.07em;}</style></defs><title>Vantage+Torch_Logos</title><polygon class="cls-1" points="109.8 126.06 143.69 33.21 182.71 33.21 123.02 154.83 109.8 126.06"/><polygon class="cls-1" points="153.81 33.21 182.71 33.21 109.17 183.1 101.38 140.09 153.81 33.21"/><polygon class="cls-2" points="76.86 33.21 122.76 127.08 168.66 33.21 76.86 33.21"/><polygon class="cls-3" points="123.6 153.64 109.13 183.06 35.66 33.14 64.58 33.21 123.6 153.64"/><text class="cls-4" transform="translate(151.36 154.83)">AN<tspan class="cls-5" x="135.77" y="0">T</tspan><tspan class="cls-6" x="178.02" y="0">A</tspan><tspan x="240.44" y="0">GE</tspan></text></svg>
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
            {{-- @if(Auth::user()->hasPermissionTo('view.wallet')) --}}
                {{-- <li class="wallet_icon {{ Request::is('agency/wallets/*') ? 'active' :  Request::is('wallets/wallet-statement') ? 'active' : ''  }}"><a href="{{ route('agency_wallet.statement') }}">Wallet</a></li> --}}
            {{-- @endif --}}
            @if(Auth::user()->hasPermissionTo('create.asset'))
                <li class="wallet_icon {{ Request::is('agency/media-assets/*') ? 'active' :  Request::is('media-assets/*') ? 'active' : ''  }}"><a href="{{ route('agency.media_assets') }}">Media Assets</a></li>
            @endif
        <!-- <li class="settings_icon {{ Request::is('agency/user/manage') ? 'active' : '' }}"><a href="{{ route('agency.user_management') }}">User Management</a></li> -->
        </ul>
    </div>

    <div class="_nav_button">
         @if(Auth::user()->hasPermissionTo('create.media_plan'))
            <a href="{{ route('agency.media_plan.criteria_form') }}" class="btn full block_disp uppercased align_center mb3"><span class="_plus"></span>New Media Plan</a>
        @endif
        {{-- @if(Auth::user()->hasPermissionTo('create.campaign')) --}}
            {{-- <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn full block_disp uppercased align_center"><span class="_plus"></span>New Campaign</a> --}}
        {{-- @endif --}}
    </div>
</div>
