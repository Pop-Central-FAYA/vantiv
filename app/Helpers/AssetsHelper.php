<?php


namespace Vanguard\Helpers;
use \Illuminate\Support\Facades\URL;


class AssetsHelper 
{

   static $VANTAGE_LOGO = '
     <!-- Created with SVG-edit - http://svg-edit.googlecode.com/ -->
    <defs>
     <symbol id="svg_2" viewBox="0 0 553.34 216.48" xmlns="http://www.w3.org/2000/svg">
      <title>Vantage+Torch_Logos</title>
      <polygon points="109.8 126.06 143.69 33.21 182.71 33.21 123.02 154.83 109.8 126.06" fill="#64c4ce" id="Fill-1"/>
      <polygon points="153.81 33.21 182.71 33.21 109.17 183.1 101.38 140.09 153.81 33.21" fill="#64c4ce" id="Fill-2"/>
      <polygon class="cls-2" points="76.86 33.21 122.76 127.08 168.66 33.21 76.86 33.21" fill="#575758" id="Fill-3"/>
      <polygon points="123.6 153.64 109.13 183.06 35.66 33.14 64.58 33.21 123.6 153.64" fill="#4eaeaf" id="Fill-4"/>
      <text id="svg_8" font-family="LemonMilk" fill="#1d1d1d" font-size="91.26px" x="151.36" y="154.83">AN
       <tspan id="svg_9" x="287.13" y="154.83">T</tspan>
       <tspan id="svg_10" x="329.38" y="154.83">A</tspan>
       <tspan id="svg_11" x="391.8" y="154.83">GE</tspan></text>
     </symbol>
    </defs>
    <g>
     <title>Layer 1</title>
     <use id="svg_3" xlink:href="#svg_2" transform="matrix(1.1134798870399125,0,0,1.0357963171947533,-11.671895890043723,-23.101256644532413) " y="22.6777" x="2.49327"/>
     <g id="svg_4"/>
    </g>';
    static $TORCH_LOGO = '
    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 478.81 179.82">
        <defs>
            <style>.cls-1{font-size:86.12px;fill:#1d1d1d;font-family:LemonMilk, "Lemon/Milk";}.cls-2{fill:#575758;}.cls-3{fill:#4eaeaf;}.cls-4{fill:#64c4ce;}
            </style>
        </defs>
        <title>Vantage+Torch_Logos</title>
        <text class="cls-1" transform="translate(187.99 129.26)">ORCH</text>
        <polygon class="cls-2" points="162.01 128.12 130.62 147.15 99.16 128.12 99.16 53.16 162.01 53.16 162.01 128.12"/>
        <polygon class="cls-3" points="176.9 58.9 200.77 58.9 218.26 33.66 115.35 33.66 166.81 107.91 176.9 93.35 176.9 58.9"/>
        <polygon class="cls-4" points="42.73 33.66 60.23 58.9 84.33 58.9 84.33 93.59 84.27 93.59 94.19 107.91 145.65 33.66 42.73 33.66"/>
    </svg>';

    static function logo($type='null')
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
            if($type == "error")
                return ' 
                <svg width="400" height="90"  xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                static::$TORCH_LOGO. '</svg>
                ';
            else
                return ' 
                <svg width="119" height="46" style="margin-left: -11px;" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                static::$TORCH_LOGO. '</svg>
                ';
            break;
            default:
                if($type == "error")
                return ' 
                    <svg width="400" height="90"  xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                    static::$VANTAGE_LOGO. '</svg>
                    ';
                else
                return ' 
                    <svg width="119" height="46" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                    static::$VANTAGE_LOGO. '</svg>
                    ';
            break;
        }
       
    }
    static function mailLogo()
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
                return '<img src="https://faya-dev-us-east-1-media.s3.amazonaws.com/email-asset/Torchlogo2.png" />';
            break;
            default:
                return '<img src="https://faya-dev-us-east-1-media.s3.amazonaws.com/email-asset/vantage_logo.png" />';
            break;
        }
    }

    static function brandName()
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
                return 'Torch';
            break;
            default:
                return 'Vantage';
            break;
        }
    }

    static function resetPasswordRoute($token)
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
                return  URL::to('proceed/password-change/' . $token) ;
            break;
            default:
                return route('password.reset', ['token' => $token]);
            break;
        }
    }
}