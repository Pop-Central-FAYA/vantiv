<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Api;

class SecondsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api_seconds = Api::get_time();
        $api_get = json_decode($api_seconds);
        $api = $api_get->data;

        return view('seconds.index')->with('time', $api);
    }

//    public function discount()
//    {
//        $api_discount = Api::get_discount_type();
//        $api_get = json_decode($api_discount);
//        $api = $api_get->data;
//        dd($api);
//    }

}
