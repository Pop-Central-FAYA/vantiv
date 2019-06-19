
<div class="header mb3 clearfix">

    <div class="user_dets p-t col_12 column align_right">

        <div class="user_acct right align_left">

            <span class="avatar_icon"></span>

            <div class="inner_nav">
                <p class="border_bottom">
                    <span class="weight_bold block_disp eliptic">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
                    <span class="block_disp small_faint">{{ Auth::user()->username ? Auth::user()->username : Auth::user()->firstname }}</span>
                </p>

                <div class="">
                  @if(Auth::user()->hasPermissionTo('view.profile'))
                    <a href="{{ route('user.profile') }}" class="color_dark">Profile</a>
                    @endif
                    @if(Auth::user()->hasPermissionTo('view.user'))
                    <a href="{{ route('agency.user.index') }}" class="color_dark">Users</a>
                    @endif

                    <a href="{{ route('auth.logout') }}" class="color_red">Logout</a>
                </div>
            </div>
        </div>


        <p class="right padd">{{ Auth::user()->username ? Auth::user()->username : Auth::user()->firstname }}</p>

    </div>
</div>

