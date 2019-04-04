<?php

namespace Vanguard\Http\Controllers\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Criteria;
use Vanguard\Services\MediaPlan\validateCriteriaForm;
use Vanguard\Services\MediaPlan\SuggestPlan;
use Vanguard\Services\MediaPlan\StorePlanningSuggestions;
use Vanguard\Services\MediaPlan\GetMediaPlans;
use Session;

class MediaPlanController extends Controller
{
    public function index($value='')
    {
    	//Broadcaster Dashboard module
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        if ($broadcaster_id) {
            //redirect user to the new landing page of the broadcaster.
            return view('broadcaster_module.landing_page');

        } else if ($agency_id) {
            $media_plan_service = new GetMediaPlans();
            //count pending media plans
            $count_pending_media_plans = $media_plan_service->pendingPlans();

            //count approved media plans
            $count_approved_media_plans = $media_plan_service->approvedPlans();

            //count declined media plans
            $count_declined_media_plans = $media_plan_service->declinedPlans();

            return view('agency.mediaPlan.dashboard')
                    ->with([
	                    'count_pending_media_plans' => $count_pending_media_plans,
	                    'count_approved_media_plans' => $count_approved_media_plans,
	                    'count_declined_media_plans' => $count_declined_media_plans
	                ]);
        }
    }

    public function getSuggestionsByPlanId($id='')
    {
    	return "got here";
    }

    public function dashboardMediaPlans(Request $request)
    {
        $media_plan_service = new GetMediaPlans();
        return $media_plan_service->run();
    }

    public function criteriaForm(Request $request)
    {
    	$criterias = Criteria::with(['subCriterias'])->groupBy('name')->get();
    	// return criterias array with the frontend view, in order to populate criteria inputs
    	return view('agency.mediaPlan.create_plan')->with('criterias', $criterias);
    }

    public function suggestPlan(Request $request)
    {
    	// validate criteria form request
    	$validateCriteriaFormService = new validateCriteriaForm($request->all());
    	$validation = $validateCriteriaFormService->validateCriteria();

    	if ($validation->fails()) {
    		// var_dump($validation->errors()); return;
            return back()->withErrors($validation)->withInput();
    	}
    	// Fetch mps audiences, programs, stations, time duration, based on criteria
    	$suggestPlanService = new suggestPlan($request);
    	$suggestions = $suggestPlanService->suggestPlan();

    	if (count($suggestions['stations']) > 0) {
    		// store planning criteria and suggestions
    		$storeSuggestionsService = new StorePlanningSuggestions($request, $suggestions['programs_stations']);
    		$newMediaPlan = $storeSuggestionsService->storePlanningSuggestions();
    	}

    	return view('agency.mediaPlan.display_suggestions')->with('fayaFound', $suggestions);
    }
}
