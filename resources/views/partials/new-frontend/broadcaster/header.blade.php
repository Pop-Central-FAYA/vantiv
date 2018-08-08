
<div class="header mb3 clearfix">
    <div class="col_4 header_search column">
        @if(Request::is('agency/clients/*'))
            <form method="get" action="#">
                @endif
                <input type="text" placeholder="Search">
            </form>
    </div>

    <div class="user_dets p-t col_8 column align_right">

        <div class="user_acct right align_left">

            <span class="avatar_icon"></span>

            <div class="inner_nav">
                <p class="border_bottom">
                    <span class="weight_bold block_disp eliptic">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    <span class="block_disp small_faint">{{ Auth::user()->username }}</span>
                </p>

                <div class="">
                    <a href="{{ route('user.profile') }}" class="color_dark">Profile</a>
                    <a href="{{ route('auth.logout') }}" class="color_red">Logout</a>
                </div>
            </div>
        </div>


        <span class="notif_icon"></span>
        <p class="right padd">{{ Auth::user()->username }}</p>

    </div>
</div>