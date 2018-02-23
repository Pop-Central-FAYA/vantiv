<!DOCTYPE html>
<html lang="en">
    @include('partials._new_head')

    <body>
        <!--Container-->
        <div id="container">
            <!--Wrapper-->
            <div id="wrapper" class="wrapper">
                <!--Sidebar-->
                @include('partials.new_broadcaster_sidebar')
                <!--Sidebar-->
                    <!--Content-->
                    <div id="content">
                        <!--Header-->
                        @include('partials.new_header')
                        <!--Header-->
                            <!--Section-->
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
                            <!--Section-->
                        <!--Footer-->
                        @include('partials.new_footer')
                        <!--Footer-->
                    </div>
                    <!--Content-->
                </div>
                <!--Wrapper-End-->
            </div>
        </div>
        @include('partials._new_javascript')
    </body>
</html>