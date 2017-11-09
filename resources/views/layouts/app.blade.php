<!DOCTYPE html>
<html>
    <head>
        @include('partials._head')
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

        <div class="wrapper">

            @include('partials/header')
            <!-- Left side column. contains the logo and sidebar -->
            @include('partials/sidebar')

                <div class="content-wrapper">

                    <div class="container-fluid">

                        <div class="panel panel-default">
                            <div class="container-fluid">
                                @yield('content')
                            </div>

                        </div>

                    </div>

                </div>
                <!-- /.content-wrapper -->

            <!-- /.content-wrapper -->
            @include('partials/footer')

        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
             immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->

        @include('partials._javascript')

        @yield('scripts')

    </body>
</html>
