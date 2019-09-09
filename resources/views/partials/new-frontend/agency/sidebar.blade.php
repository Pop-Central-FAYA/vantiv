<div>
    @php
        $side_menu_body = [
            'Dashboard' => [
                'url' => route('dashboard', [], false),
                'permission' => 'view.report',
                'icon_class' => 'dash_icon',
                'active_class' => Request::is('/') ? 'active' : ''
            ],
            'Campaigns' => [
                'url' => route('agency.campaign.all', [], false),
                'permission' => 'view.campaign',
                'icon_class' => 'campaign_icon',
                'active_class' => Request::is('campaigns') ? 'active' :  Request::is('campaigns/*') ? 'active' : ''
            ],
            'Media Plans' => [
                'url' => route('agency.media_plans', [], false),
                'permission' => 'view.media_plan',
                'icon_class' => 'media_icon',
                'active_class' => Request::is('media-plan') ? 'active' :  Request::is('media-plan/*') ? 'active' : ''
            ],
            'Clients' => [
                'url' => route('client.index', [], false),
                'permission' => 'view.client',
                'icon_class' => 'clients_icon',
                'active_class' => Request::is('clients/*') ? 'active' : ''
            ],
            'Vendors' => [
                'url' => route('ad-vendor.index', [], false),
                'permission' => 'view.ad_vendor',
                'icon_class' => 'ad_vendor_icon',
                'active_class' => Request::is('ad-vendors') ? 'active' : ''
            ],
            'Media Assets' => [
                'url' => route('agency.media_assets', [], false),
                'permission' => 'create.asset',
                'icon_class' => 'wallet_icon',
                'active_class' => Request::is('media-assets') ? 'active' :  Request::is('media-assets/*') ? 'active' : ''
            ],
            'Invoices' => [
                'url' => route('invoices.all', [], false),
                'permission' => 'view.invoice',
                'icon_class' => 'invoice_icon',
                'active_class' => Request::is('invoices/*') ? 'active' : ''
            ]
        ];

        $allowed_side_menu_body = [];

        $current_user_permissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();

        foreach($side_menu_body as $key=>$submenu) {
            if(in_array($submenu['permission'], $current_user_permissions)) {
                $allowed_side_menu_body[$key] = $submenu;
            }
        }

        $side_menu_footer = [
            'New Media Plan' => [
                'url' => route('agency.media_plan.criteria_form'),
                'permission' => 'create.media_plan',
                'icon_class' => '_plus'
            ]
        ];

        $allowed_side_menu_footer = [];

        foreach($side_menu_footer as $key=>$submenu) {
            if(in_array($submenu['permission'], $current_user_permissions)) {
                $allowed_side_menu_footer[$key] = $submenu;
            }
        }
    @endphp

    <div class="side_nav load_this_div">
        <div class="logo mb4">{!! AssetsHelper::logo() !!}</div>
        <vantage-side-menu 
            :side-menu-body="{{ json_encode($allowed_side_menu_body) }}"
            :side-menu-footer="{{ json_encode($allowed_side_menu_footer) }}"
        ></vantage-side-menu>
    </div>
    
</div>
