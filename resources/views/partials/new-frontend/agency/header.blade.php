
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
                    <a href="#profileme" class="modal_click color_dark">Profile</a>
                    <a href="{{ route('auth.logout') }}" class="color_red">Logout</a>
                </div>
            </div>
        </div>


        <p class="right padd">{{ Auth::user()->username ? Auth::user()->username : Auth::user()->firstname }}</p>

    </div>
</div>

{{--<div class="modal_contain profile" style="width: 1000px;" id="profileme">--}}
    {{--<div class="sub_header clearfix mb pt">--}}
        {{--<div class="column col_6">--}}
            {{--<h2 class="sub_header">Edit Profile</h2>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

@include('profile.index')
