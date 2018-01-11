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
    <!-- Left side column. contains the logo and sidebar -->
    @include('partials.agent_sidebar')

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
    <!-- /.content-wrapper -->
    @include('agency_partials.footer')

    @yield('modal')


    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
@include('agency_partials._javascript')
<!-- page script -->
@yield('scripts')

</body>
</html>
