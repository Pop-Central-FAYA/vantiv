<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vanguard\ApiLog;
use Vanguard\Hourlyrange;
use Vanguard\Libraries\Api;

class HourlyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $api_get_hourly_range = Api::get_hourly_range();
        $api_get = json_decode($api_get_hourly_range);
        $api = $api_get->data;

        return view('hourly.index')->with('hourly_range', $api);
    }

}
