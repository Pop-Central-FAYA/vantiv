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

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />

    @yield('styles')
</head>

<body>


<div class="login_logo">
    <img src="{{ asset('new_frontend/img/logo.svg') }}">
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
