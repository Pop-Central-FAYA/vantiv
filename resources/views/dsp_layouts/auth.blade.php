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


<div class="dsp_login_logo">
<svg width="160" height="76" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
 <!-- Created with SVG-edit - http://svg-edit.googlecode.com/ -->
 <defs>
  <symbol viewBox="0 0 553.34 216.48" id="svg_2" xmlns="http://www.w3.org/2000/svg">
   <title>Vantage+Torch_Logos</title>
   <polygon id="Fill-1" fill="#64c4ce" points="109.8 126.06 143.69 33.21 182.71 33.21 123.02 154.83 109.8 126.06"/>
   <polygon id="Fill-2" fill="#64c4ce" points="153.81 33.21 182.71 33.21 109.17 183.1 101.38 140.09 153.81 33.21"/>
   <polygon id="Fill-3" fill="#575758" points="76.86 33.21 122.76 127.08 168.66 33.21 76.86 33.21" class="cls-2"/>
   <polygon id="Fill-4" fill="#4eaeaf" points="123.6 153.64 109.13 183.06 35.66 33.14 64.58 33.21 123.6 153.64"/>
   <text y="154.83" x="151.36" font-size="91.26px" fill="#1d1d1d" font-family="LemonMilk" id="svg_8">AN
    <tspan y="154.83" x="287.13" id="svg_9">T</tspan>
    <tspan y="154.83" x="329.38" id="svg_10">A</tspan>
    <tspan y="154.83" x="391.8" id="svg_11">GE</tspan></text>
  </symbol>
 </defs>
 <g>
  <title>Layer 1</title>
  <use x="2.49327" y="22.6777" transform="matrix(1.1134798870399125,0,0,1.0357963171947533,-11.671895890043723,-23.101256644532413) " xlink:href="#svg_2" id="svg_3"/>
  <g id="svg_4"/>
 </g>
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
