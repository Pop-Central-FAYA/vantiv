<?php


namespace Vanguard\Helpers;


class AssetsHelper 
{
    static function logo()
    {
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
                return '
                <?xml version="1.0" encoding="UTF-8"?>
                <svg width="119px" height="46px" viewBox="0 0 119 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <!-- Generator: Sketch 49.2 (51160) - http://www.bohemiancoding.com/sketch -->
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
                    </g>
                </svg>';
            break;
            default:
                return '<svg width="130px" height="80px" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 553.34 216.48"><defs><style>.cls-1{fill:#64c4ce;}.cls-2{fill:#575758;}.cls-3{fill:#4eaeaf;}.cls-4{font-size:91.26px;fill:#1d1d1d;font-family:LemonMilk, "Lemon/Milk";}.cls-5{letter-spacing:-0.12em;}.cls-6{letter-spacing:-0.07em;}</style></defs><title>Vantage</title><polygon class="cls-1" points="109.8 126.06 143.69 33.21 182.71 33.21 123.02 154.83 109.8 126.06"/><polygon class="cls-1" points="153.81 33.21 182.71 33.21 109.17 183.1 101.38 140.09 153.81 33.21"/><polygon class="cls-2" points="76.86 33.21 122.76 127.08 168.66 33.21 76.86 33.21"/><polygon class="cls-3" points="123.6 153.64 109.13 183.06 35.66 33.14 64.58 33.21 123.6 153.64"/><text class="cls-4" transform="translate(151.36 154.83)">AN<tspan class="cls-5" x="135.77" y="0">T</tspan><tspan class="cls-6" x="178.02" y="0">A</tspan><tspan x="240.44" y="0">GE</tspan></text></svg>
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
}