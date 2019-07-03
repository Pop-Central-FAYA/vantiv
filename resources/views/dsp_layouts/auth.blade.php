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

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('new_frontend/img/fav_icon.ico') }}" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <link href="{{ asset('new_frontend/css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('new_frontend/css/style.css') }}" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />

    @yield('styles')
</head>

<body>


<div class="login_logo">
<svg width="119px" height="36px" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 553.34 216.48"><defs><style>.cls-1{fill:#64c4ce;}.cls-2{fill:#575758;}.cls-3{fill:#4eaeaf;}.cls-4{font-size:91.26px;fill:#1d1d1d;font-family:LemonMilk, "Lemon/Milk";}.cls-5{letter-spacing:-0.12em;}.cls-6{letter-spacing:-0.07em;}</style></defs><title>Vantage+Torch_Logos</title><polygon class="cls-1" points="109.8 126.06 143.69 33.21 182.71 33.21 123.02 154.83 109.8 126.06"/><polygon class="cls-1" points="153.81 33.21 182.71 33.21 109.17 183.1 101.38 140.09 153.81 33.21"/><polygon class="cls-2" points="76.86 33.21 122.76 127.08 168.66 33.21 76.86 33.21"/><polygon class="cls-3" points="123.6 153.64 109.13 183.06 35.66 33.14 64.58 33.21 123.6 153.64"/><text class="cls-4" transform="translate(151.36 154.83)">AN<tspan class="cls-5" x="135.77" y="0">T</tspan><tspan class="cls-6" x="178.02" y="0">A</tspan><tspan x="240.44" y="0">GE</tspan></text></svg>
</div>


<div class="auth_contain col_4 margin_center">

    @yield('content')

</div>



<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
{{--<script type="text/javascript" src="{{ asset('jquery.simplemodjquery.simplemodal.1.4.4.min.js.min.js') }}"></script>--}}
<script type="text/javascript" src="{{ asset('new_frontend/js/script.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/as/login.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/as/login.js') }}"></script>
<script src="{{ asset('vendor_public/jsvalidation/js/jsvalidation.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js"></script>
@yield('scripts')
@include('toastr.toastr')
</body>

</html>
