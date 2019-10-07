<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Reach\GetReachRequest;
use Vanguard\Http\Requests\Reach\GetStationTimebeltReachRequest;
use Vanguard\Models\MediaPlan;
use Vanguard\Services\MediaPlan\Helpers\Filters;
use Vanguard\Services\Ratings\GetStationReachService;
use Vanguard\Services\Ratings\GetTimeBeltReachService;
use Vanguard\Http\Resources\TvStationRatingCollection;
use Vanguard\Http\Resources\TvStationTimeBeltRatingCollection;

/**
 * @todo Add authorization on media plan for these actions
 */
class ReachController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view.media_plan')->only(['getReach', 'getStationTimebeltReach']);
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

    public function getStationTimebeltReach(GetStationTimebeltReachRequest $request, $plan_id)
    {
        $media_plan = MediaPlan::findOrFail($plan_id);
        // $this->authorize('get', $media_plan);
        $validated = $request->validated();

        $filters = Filters::prepareTargetingFilters($media_plan, $validated);
        $service = new GetTimeBeltReachService($filters, $media_plan);
        $data = $service->run();
        return new TvStationTimeBeltRatingCollection($data);
    }
}