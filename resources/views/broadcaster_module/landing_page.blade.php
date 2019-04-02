
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FAYA | Welcome</title>
    <meta property="og:url" content="http://www.faya.com" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="FAYA" />
    <meta property="og:description" content="Advertising" />

    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <link href="{{ asset('new_frontend/css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('new_frontend/css/style.css') }}" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>


<div class="login_logo">
    <img src="{{ asset('new_frontend/img/logo.svg') }}">
</div>


<div class="auth_contain col_4 margin_center">

    <div class="align_center m-b">
        <h2 class="m-b">Welcome Back
            @if(Auth::user()->companies()->count() > 1)
                {{ Auth::user()->firstname }}
            @else
                {{ Auth::user()->companies->first()->name }}</h2>
            @endif
        <p class="mb4">What would you like to do?</p>
        @if(Auth::user()->companies()->count() > 1)
            <a href="{{ route('broadcaster.campaign_management') }}" class="m-b block_disp btn full ghost">Continue to Dashboard</a>
        @else
            @if(Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.media_buyer') || Auth::user()->hasRole('ssp.scheduler'))
                <a href="{{ route('broadcaster.campaign_management') }}" class="m-b block_disp btn full ghost">Campaign Management</a>
            @endif
            @if(Auth::user()->hasRole('ssp.admin'))
                <a href="{{ route('broadcaster.inventory_management') }}" class="m-b block_disp btn full ghost">Inventory Management</a>
            @endif
        @endif
    </div>

</div>


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('new_frontend/js/modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_frontend/js/script.js') }}"></script>

</body>

</html>
