<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Reach\GetReachRequest;
use Vanguard\Http\Requests\Reach\GetStationReachRequest;
use Vanguard\Models\MediaPlan;
use Vanguard\Services\MediaPlan\GetStationRatingService;
use Vanguard\Services\MediaPlan\Helpers\Filters;
use Vanguard\Services\Ratings\GetStationReachService;
use Vanguard\Http\Resources\TvStationRatingCollection;

class ReachController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view.media_plan')->only(['getReach', 'getStationReach']);
    }

    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/
    public function getReach(GetReachRequest $request, $plan_id)
    {           
        $media_plan = MediaPlan::findOrFail($plan_id);
        // $this->authorize('get', $media_plan);
        $validated = $request->validated();

        $filters = Filters::prepareTargetingFilters($media_plan, $validated);
        $service = new GetStationReachService($filters, $media_plan);
        $data = $service->run();
        return new TvStationRatingCollection($data);
    }

    public function getStationTimebeltReach(GetStationTimebeltRequest $request, $station_key)
    {
        $validated = $request->validated();
        return [];
    }
}