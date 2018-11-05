<?php

namespace Vanguard\Http\Controllers\Api;

use Vanguard\Http\Controllers\Controller;

class BaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }

}
