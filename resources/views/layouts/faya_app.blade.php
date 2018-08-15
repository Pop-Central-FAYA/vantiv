<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('title')
    <meta property="og:url" content="http://www.fayamedia.com" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="FAYA" />
    <meta property="og:description" content="Advertising" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('new_frontend/img/favicon.ico') }}" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <link href="{{ asset('new_frontend/css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('new_frontend/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />
    @yield('styles')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<!-- side navigation -->
    @if(Session::has('agency_id'))
        @include('partials.new-frontend.agency.sidebar')
    @endif

    @yield('content')

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('new_frontend/js/jquery.simplemodal.1.4.4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new_frontend/js/script.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js"></script>
    @yield('scripts')
    @include('toastr.toastr')

    <script type="text/javascript">
        (function() { var s = document.createElement("script"); s.type = "text/javascript"; s.async = true; s.src = '//api.usersnap.com/load/ec075f05-c488-417b-ba4e-beb5366a9c15.js';
            var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x); })();
    </script>

</body>

</html>