<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vanguard\Libraries\Api;

class AdslotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        api for adslot
        $adslot = Api::get_adslot();
        $a = json_decode($adslot);
        $b = (object)($a->data);

//        api for time in seconds
        $api_seconds = Api::get_time();
        $api_get = json_decode($api_seconds);
        $api = $api_get->data;
//        dd($b);
        return view('adslot.index')->with('adslot', $b)->with('seconds', $api);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $api_get_hourly_range = Api::get_hourly_range();
        $api_get_hour = json_decode($api_get_hourly_range);
        $api_hour = $api_get_hour->data;

        $api_seconds = Api::get_time();
        $api_get_sec = json_decode($api_seconds);
        $api_sec = $api_get_sec->data;

        return view('adslot.create')->with('hour', $api_hour)->with('seconds', $api_sec);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $adslot = Api::store_ad_slot($request);
        if($adslot->status === false)
        {
            return redirect()->back()->with('error', $adslot->message);
        }else{
            return redirect()->back()->with('success', trans('app.adslot_created'));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
