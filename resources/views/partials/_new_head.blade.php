<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('title')
    <link href="{{ asset('new_assets/css/style.css') }}" rel="stylesheet" type="text/css"  />
    <link rel="stylesheet" href="{{ asset('asset/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('new_assets/css/bootstrap-grid.min.css') }}" rel="stylesheet" type="text/css"  />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <!--Style-navigation-->
    <link href="{{ asset('new_assets/css/navigation.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('new_assets/css/jquery.circliful.css') }}" rel="stylesheet" type="text/css">
    <!--chart-->
    <link rel="stylesheet" href="{{ asset('new_assets/css/chart.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />
    @yield('styles')
</head>