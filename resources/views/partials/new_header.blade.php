<?php
    if(Session::get('agency_id')){
        $agency_id = Session::get('agency_id');
        $profile = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT image_url from agents where id = '$agency_id'");
    } elseif (Session::get('broadcaster_id')){
        $broadcaster_id = Session::get('broadcaster_id');
        $profile = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT image_url from broadcasters where id = '$broadcaster_id'");
    } elseif (Session::get('advertiser_id')) {
        $advertiser_id = Session::get('advertiser_id');
        $profile = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT image_url from advertisers where id = '$advertiser_id'");
    } elseif (Session::get('broadcaster_user_id')){
        $broadcaster_user_id = Session::get('broadcaster_user_id');
        $profile = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT image_url from broadcasterUsers where id = '$broadcaster_user_id'");
    }
?>

<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="col-6">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn"> <i class="fa fa-navicon"></i> </button>
                        </div>
                    </nav>
                </div>
                <div class="col-6">
                    {{--<div class="dropdown">--}}
                        {{--<button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                            {{--Dropdown trigger--}}
                            {{--<span class="caret"></span>--}}
                        {{--</button>--}}
                        {{--<ul class="dropdown-menu" aria-labelledby="dLabel">--}}
                            {{--...--}}
                        {{--</ul>--}}
                    {{--</div>--}}

                    <div class="content">
                        <ul>
                            <li><a href="#"></a></li>
                            <li class="profile"><a href="#"><span>{{ Auth::user()->username }}</span><span class="image img-responsive"><img style="width: 50px; height: 50px;" src="{{ $profile[0]->image_url ? asset(decrypt($profile[0]->image_url)) : asset('new_assets/images/logo.png') }}"><span class="green-online"></span></span></a></li>
                            <li><a href="#"> <span class="online2"></span> </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

