<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @yield('title')
    @include('partials._head')
    @yield('style')
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

    @include('partials/header')

        @include('partials.advertiser_sidebar')

        <div class="content-wrapper">

            <div class="container-fluid">
                @include('partials.messages')
                <div class="panel panel-default">
                    <div class="container-fluid">
                        @yield('content')
                    </div>

                </div>

            </div>

        </div>

        @include('agency_partials.footer')

        @yield('modal')

        <div class="control-sidebar-bg"></div>
    </div>

    @include('agency_partials._javascript')

    @yield('scripts')

</body>
</html>