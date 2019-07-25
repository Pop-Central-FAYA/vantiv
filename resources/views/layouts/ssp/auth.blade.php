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

    <link rel="shortcut icon" type="image/x-icon" href="https://faya-dev-us-east-1-media.s3.amazonaws.com/email-asset/Torchlogo2.png" />
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
    <svg id="Layer_1" width="160" height="76" style="margin-left: -30px;" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 478.81 179.82">
        <defs>
            <style>.cls-1{font-size:86.12px;fill:#1d1d1d;font-family:LemonMilk, "Lemon/Milk";}.cls-2{fill:#575758;}.cls-3{fill:#4eaeaf;}.cls-4{fill:#64c4ce;}
            </style>
        </defs>
        <title>Vantage+Torch_Logos</title>
        <text class="cls-1" transform="translate(187.99 129.26)">ORCH</text>
        <polygon class="cls-2" points="162.01 128.12 130.62 147.15 99.16 128.12 99.16 53.16 162.01 53.16 162.01 128.12"/>
        <polygon class="cls-3" points="176.9 58.9 200.77 58.9 218.26 33.66 115.35 33.66 166.81 107.91 176.9 93.35 176.9 58.9"/>
        <polygon class="cls-4" points="42.73 33.66 60.23 58.9 84.33 58.9 84.33 93.59 84.27 93.59 94.19 107.91 145.65 33.66 42.73 33.66"/>
    </svg>
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
