<div class="side_nav load_this_div">
    <div class="logo mb4">
    {!! AssetsHelper::logo() !!}
    </div>

    <div class="_nav">
        <ul>
            @if(Auth::user()->hasPermissionTo('view.report'))
            <li class="dash_icon {{ Request::is('/') ? 'active' : ''  }}"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.campaign'))
                <li class="campaign_icon {{ Request::is('campaigns') ? 'active' :  Request::is('campaigns/*') ? 'active' : ''  }}"><a href="{{ route('agency.campaign.all') }}">Campaigns</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.media_plan'))
                <li class="media_icon {{ Request::is('media-plan') ? 'active' :  Request::is('media-plan/*') ? 'active' : ''  }}"><a href="{{ route('agency.media_plans') }}">Media Plans</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.client'))
                <li class="clients_icon {{ Request::is('clients/*') ? 'active' : ''  }}"><a href="{{ route('clients.list') }}">Clients</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('create.asset'))
                <li class="wallet_icon {{ Request::is('media-assets') ? 'active' :  Request::is('media-assets/*') ? 'active' : ''  }}"><a href="{{ route('agency.media_assets') }}">Media Assets</a></li>
            @endif
            @if(Auth::user()->hasPermissionTo('view.invoice'))
                <li class="invoice_icon {{ Request::is('invoices/*') ? 'active' : ''  }}"><a href="{{ route('invoices.all') }}">Invoices</a></li>
            @endif
            {{-- @if(Auth::user()->hasPermissionTo('view.wallet')) --}}
                {{-- <li class="wallet_icon {{ Request::is('wallets/*') ? 'active' :  Request::is('wallets/wallet-statement') ? 'active' : ''  }}"><a href="{{ route('agency_wallet.statement') }}">Wallet</a></li> --}}
            {{-- @endif --}}
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
