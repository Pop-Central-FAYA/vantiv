<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page-title') | {{ settings('app_name') }}</title>

    {!! HTML::style("assets/css/bootstrap.min.css") !!}
    {!! HTML::style("assets/css/font-awesome.min.css") !!}
    {!! HTML::style("assets/css/app.css") !!}
    {!! HTML::style("assets/css/bootstrap-social.css") !!}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />

    @yield('header-scripts')
</head>
<body>

    <div class="container">

        @yield('content')

        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <p>@lang('app.copyright') © - FAYA {{ date('Y') }}</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {!! HTML::script('assets/js/jquery-2.1.4.min.js') !!}
    {!! HTML::script('assets/js/bootstrap.min.js') !!}
    {!! HTML::script('vendor_public/jsvalidation/js/jsvalidation.js') !!}
    {!! HTML::script('assets/js/as/app.js') !!}
    {!! HTML::script('assets/js/as/btn.js') !!}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js"></script>

    @yield('scripts')
    @include('toastr.toastr')
</body>
</html>
