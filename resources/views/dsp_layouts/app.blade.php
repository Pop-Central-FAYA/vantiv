<!DOCTYPE html>
<html>
    <head>

        @include('partials._head')
        @yield('styles')

    </head>
    <body class="hold-transition skin-blue sidebar-mini">

        <div class="wrapper">

            @include('partials/header')

            @include('partials/sidebar')

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

            @include('partials/footer')

            <div class="control-sidebar-bg"></div>
        </div>

        @include('partials._javascript')

        @yield('scripts')

    </body>
</html>
