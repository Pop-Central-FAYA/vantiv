<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Maths;
use League\Flysystem\Exception;


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
        $ratecard = Api::get_adslot();
        $a = (json_decode($ratecard)->data);
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        $seconds = [60, 45, 39, 15];
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        return view('adslot.index')->with('ratecard', $a)->with('seconds', $seconds)->with('preload', $load);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        $target_audience = Api::getTargetAudience();
        $tar = (json_decode($target_audience)->data);
        $api_get_hourly_range = Api::get_hourly_range();
        $api_get_hour = json_decode($api_get_hourly_range);
        $api_hour = $api_get_hour->data;
        $api_seconds = Api::get_time();
        $api_get_sec = json_decode($api_seconds);
        $api_sec = $api_get_sec->data;
        return view('adslot.create')->with('hour', $api_hour)->with('seconds', $api_sec)->with('target_audience', $tar)->with('preload', $load);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ratecard_id)
    {
        $adslot_update = Api::update_adslot($request, $ratecard_id);
        if($adslot_update->status === false)
        {
            return redirect()->back()->with('error', $adslot_update->message);
        }else{
            return redirect()->back()->with('success', trans('app.adslot_updated'));
        }
    }

    public function getAdslotByRegion($region_d)
    {
        //        api for adslot
        $ratecard = Api::get_adslot_by_region($region_d);
        $a = (json_decode($ratecard)->data);
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        $seconds = [60, 45, 39, 15];
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        return view('adslot.index')->with('ratecard', $a)->with('seconds', $seconds)->with('preload', $load);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
