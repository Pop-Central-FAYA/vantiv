<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('title')
    <link href="{{ asset('new_assets/css/style.css') }}" rel="stylesheet" type="text/css"  />
    <link rel="stylesheet" href="{{ asset('asset/bootstrap/css/bootstrap.min.css') }}">
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">--}}
    <link href="{{ asset('new_assets/css/bootstrap-grid.min.css') }}" rel="stylesheet" type="text/css"  />
    <link href="{{ asset('new_assets/css/font-awesome.css') }}" rel="stylesheet" type="text/css"  />
    <!--Style-navigation-->
    <link href="{{ asset('new_assets/css/navigation.css') }}" rel="stylesheet" type="text/css">
    <!--chart-->
    <link rel="stylesheet" href="{{ asset('new_assets/css/chart.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    @yield('styles')
</head>