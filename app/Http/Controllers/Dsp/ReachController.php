<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\AdVendor\GetStationReachRequest;

class ReachController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view.media_plan')->only(['getReach', 'getStationReach']);
    }

    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/
    public function getReach(GetReachRequest $request)
    {           
        $validated = $request->validated();
        return [];
    }

    public function getStationTimebeltReach(GetStationTimebeltRequest $request, $station_key)
    {
        $validated = $request->validated();
        return [];
    }
}
