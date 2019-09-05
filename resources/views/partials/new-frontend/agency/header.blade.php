<div>
    @php
        $menu = [
            'Profile' => [
                'url' => route('user.profile', [], false),
                'permission' => 'view.profile',
                'color' => 'color_dark'
            ],
            'Users' => [
                'url' => route('agency.user.index', [], false),
                'permission' => 'view.user',
                'color' => 'color_dark'
            ],
            'Company' => [
                'url' => route('company.index', [], false),
                'permission' => 'update.company',
                'color' => 'color_dark'
            ],
            'Logout' => [
                'url' => route('auth.logout', [], false),
                'permission' => 'view.profile',
                'color' => 'color_red'
            ]
        ];

        $allowed_menu = [];

        $current_user_permissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();

        foreach($menu as $key=>$submenu) {
            if(in_array($submenu['permission'], $current_user_permissions)) {
                $allowed_menu[$key] = $submenu;
            }
        }

        $current_user = [
            'fullname' => Auth::user()->firstname.' '.Auth::user()->lastname,
            'username' => Auth::user()->firstname
        ];
    @endphp

    <div class="header mb3 clearfix" style="margin-right: 30px;">
        <div class="user_dets p-t col_12 column align_right">
            <vantage-header
                :authenticated-user="{{ json_encode($current_user) }}"
                :menu="{{ json_encode($allowed_menu) }}"
            ></vantage-header>
        </div>
    </div>
    
</div>