<?php


namespace Vanguard\Helpers;


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
    static $FAYA_LOGO =' <!-- Generator: Sketch 49.2 (51160) - http://www.bohemiancoding.com/sketch -->
    <title>Page 1</title>
    <desc>Created with Sketch.</desc>
    <defs></defs>
    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g id="Login" transform="translate(-580.000000, -81.000000)">
            <g id="Page-1" transform="translate(580.000000, 81.000000)">
                <polygon id="Fill-1" fill="#44C1C9" points="0 6 24 6 24 0 0 0"></polygon>
                <polygon id="Fill-2" fill="#222222" points="0 15 0 36 5.99114925 36 5.99114925 21.1044174 22 21.1044174 22 15"></polygon>
                <polygon id="Fill-3" fill="#44C1C9" points="23 36 30.3379468 36 41 13.8387817 37.7457945 7"></polygon>
                <polygon id="Fill-4" fill="#009FA0" points="41.2783896 0 38 6.67742466 41.1771528 13.5920247 41.2783896 13.3742576 51.8358002 36 59 36"></polygon>
                <polygon id="Fill-5" fill="#44C1C9" points="80.9945174 0 71.0238493 13.3442398 60.3337537 0 53 0 67.6684942 19.3440751 67.6684942 36 73.6604348 36 73.6604348 19.3440751 89 0"></polygon>
                <polygon id="Fill-6" fill="#009FA0" points="100.806629 0 83 36 90.2176706 36 100.806629 13.3742576 100.808258 13.3775471 104 6.43860451"></polygon>
                <polygon id="Fill-7" fill="#44C1C9" points="101 12.8071663 111.722726 35 119 35 104.218099 6"></polygon>
            </g>
        </g>
    </g>';

    static function logo()
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
            return ' 
            <svg width="119" height="46" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
            static::$FAYA_LOGO. '</svg>
            ';
            break;
            default:
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
                return '<img src="http://res.cloudinary.com/drwrickhm/image/upload/v1522495083/logo_mail.png" />';
            break;
            default:
                return '<img src="https://faya-dev-us-east-1-media.s3.amazonaws.com/email-asset/vantage_logo.png" />';
            break;
        }
       
    }

    static function errorPageLogo()
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
                return ' 
                <svg width="400" height="90" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                static::$FAYA_LOGO. 
                '</svg>';
            break;
            default:
                return ' 
                <svg width="400" height="90" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                static::$VANTAGE_LOGO. 
                '</svg>';
            break;
        }
       
    }
}