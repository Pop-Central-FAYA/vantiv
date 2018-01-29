<!DOCTYPE html>
<html>
<head>
    @include('partials._head')
    @yield('styles')
</head>
<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <div class="content-wrapper">

        @include('partials.agency_messages')

        @yield('content')

    </div>

    @include('partials/footer')
    <div class="control-sidebar-bg"></div>
</div>

@include('partials._javascript')

@yield('scripts')

</body>
</html>