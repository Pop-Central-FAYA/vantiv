<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('title')
    @yield('extra-meta')
    <meta property="og:url" content="http://www.fayamedia.com" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="FAYA" />
    <meta property="og:description" content="Advertising" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('new_frontend/img/fav_icon.ico') }}" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('new_frontend/css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('new_frontend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('new_frontend/css/custom.css') }}" rel="stylesheet">

    <!-- fontawesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    
    <style>
        body {
            overflow: auto;
        }
    </style>
    @yield('styles')

</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    <!-- App.js -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>