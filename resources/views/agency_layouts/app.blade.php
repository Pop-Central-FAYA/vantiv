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
    @include('agency_partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        @yield('content')

        <!-- /.content -->
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
