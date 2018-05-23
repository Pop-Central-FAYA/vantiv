<!DOCTYPE html>
<html lang="en">
@include('partials._new_head')
<body>
<!--Container-->
<div id="container">
    <!--Wrapper-->
    <div id="wrapper" class="wrapper">
        <!--Sidebar-->
        @if(Session::get('agency_id'))
            @include('partials.new_agent_sidebar')
            <?php session()->forget('broadcaster_id'); ?>
            <?php session()->forget('advertiser_id'); ?>
            <?php session()->forget('broadcaster_user_id'); ?>
            <?php session()->forget('admin_id'); ?>
            <?php session()->forget('client_id') ?>
        @elseif(Session::get('advertiser_id'))
            @include('partials.new_advertiser_sidebar')
            <?php session()->forget('broadcaster_id'); ?>
            <?php session()->forget('agency_id'); ?>
            <?php session()->forget('broadcaster_user_id'); ?>
            <?php session()->forget('admin_id'); ?>
            <?php session()->forget('client_id') ?>
        @elseif(Session::get('broadcaster_id'))
            @include('partials.new_broadcaster_sidebar')
            <?php session()->forget('agency_id'); ?>
            <?php session()->forget('advertiser_id'); ?>
            <?php session()->forget('broadcaster_user_id'); ?>
            <?php session()->forget('admin_id'); ?>
            <?php session()->forget('client_id') ?>
        @elseif(Session::get('broadcaster_user_id'))
            @include('partials.broadcaster_user_sidebar')
            <?php session()->forget('agency_id'); ?>
            <?php session()->forget('advertiser_id'); ?>
            <?php session()->forget('broadcaster_id'); ?>
            <?php session()->forget('admin_id'); ?>
            <?php session()->forget('client_id') ?>
        @elseif(Session::get('admin_id'))
            @include('partials.admin_sidebar')
            <?php session()->forget('agency_id'); ?>
            <?php session()->forget('advertiser_id'); ?>
            <?php session()->forget('broadcaster_id'); ?>
            <?php session()->forget('broadcaster_user_id') ?>
            <?php session()->forget('client_id') ?>
        @elseif(Session::get('client_id'))
            @include('partials.client_sidebar')
            <?php session()->forget('agency_id'); ?>
            <?php session()->forget('advertiser_id'); ?>
            <?php session()->forget('broadcaster_id'); ?>
            <?php session()->forget('broadcaster_user_id') ?>
            <?php session()->forget('admin_id') ?>
        @endif
        <!--Sidebar-->
        <!--Content-->
        <div id="content">
            <!--Header-->
                @include('partials.new_header')
            <!--Header-->
            <!--Section-->
                <div class="content-wrapper">
                    <div class="container-fluid">
                        <div class="panel panel-default">
                            <div class="container-fluid">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>

            <!--Section-->
            <!--Footer-->
                @include('partials.new_footer')
            <!--Footer-->
        </div>
        <!--Content-->
    </div>
    <!--Wrapper-End-->
</div>
@include('partials._new_javascript')
@include('toastr.toastr')
</body>
</html>
