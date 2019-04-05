<?php

namespace Vanguard\Http\Controllers\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Criteria;
use Vanguard\Services\MediaPlan\validateCriteriaForm;
use Vanguard\Services\MediaPlan\SuggestPlan;
use Vanguard\Services\MediaPlan\StorePlanningSuggestions;
use Illuminate\Support\Facades\DB;

class MediaPlanController extends Controller
{
    public function index($value='')
    {
    	
	}
	
	public function customisPlan()
    {
		return view('agency.mediaPlan.custom_plan');
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

		return redirect()->action(
			'MediaPlan\MediaPlanController@getSuggestPlanById', ['id' => $newMediaPlan->id]
		);
	
	}
	
	public function getSuggestPlanById($id)
    {
		$plans = DB::table('media_plan_suggestions')->where('media_plan_id', $id)->get();
		
		$suggestions = $this->groupSuggestions($plans);
		$suggestionsByStation = $this-> groupSuggestionsByStation($plans);

		$fayaFound = array(
            'total_tv' => $this->countByMediaType($suggestions, 'Tv'),
			'total_radio' => $this->countByMediaType($suggestions, 'Radio'),
			'programs_stations' => $suggestions,
            'stations' => $suggestionsByStation,
            'total_audiences' => $this->totalAudienceFound($suggestions)
        );
		return view('agency.mediaPlan.display_suggestions')->with('fayaFound', $fayaFound);

		//return $fayaFound;
	
	}
	
	public function groupSuggestions($query)
    {
        $query = $query->groupBy(function ($item, $key) {
            return $item->station.'_'.$item->program.'_'.$item->start_time.'_'.$item->end_time;
        });
        $query = $query->map(function($item, $key) {
                        $count = count($item);
                        $item = $item->first();
                        $item->audience = $count;
                        return $item;
                    });
        $query = $query->flatten();
        $query = $query->sortByDesc('audience');
        return $query;
    }

    public function groupSuggestionsByStation($query)
    {
        return $query->groupBy('station');
	}
	
	public function countByMediaType($collection, $media_type='')
    {
        return $collection->where('media_type', $media_type)->sum('audience');
    }

    public function totalAudienceFound($collection)
    {
        return $collection->sum('audience');
	}
	

	public function SelectPlanPost(Request $request)
    {
		$programs_id = json_decode($request->get('data'));
			$value = "";
			foreach($programs_id as $program_id){
				
			DB::table('media_plan_suggestions')
            ->where('id', $program_id->program_id)
            ->update(['status' => 1]);


				$value = $program_id->program_id;
			}

		return response()->json(['token'=>$value]);
		

	}
	
	public function CreatePlan($id)
    {
		$plans = DB::table('media_plan_suggestions')->where('media_plan_id', $id)->where('status', 1)->get();
		$plans_details = DB::table('media_plans')->where('id', $id)->get();
		
		$suggestions = $this->groupSuggestions($plans);
		$suggestionsByStation = $this-> groupSuggestionsByStation($plans);
		$dates = $this->dates($plans_details[0]->start_date, $plans_details[0]->end_date);
		 $labeldates = $this->labeldates($plans_details[0]->start_date, $plans_details[0]->end_date);
		 $days = $this->days($plans_details[0]->start_date, $plans_details[0]->end_date);
		$fayaFound = array(
            'total_tv' => $this->countByMediaType($suggestions, 'Tv'),
			'total_radio' => $this->countByMediaType($suggestions, 'Radio'),
			'programs_stations' => $suggestions,
            'stations' => $suggestionsByStation,
			'total_audiences' => $this->totalAudienceFound($suggestions),
			'plan_details' => $plans_details,
			'dates' => $dates,
			'labeldates' => $labeldates,
			'days' => $days,
        );
		return view('agency.mediaPlan.complete_plan')->with('fayaFound', $fayaFound);
		//return $fayaFound;
	
	}

	public function days($start, $end)
	{
       date_default_timezone_set('UTC');
       $diff = strtotime($end) - strtotime($start);
       $daysBetween = floor($diff/(60*60*24));
			$formattedDates = array();
			for ($i = 0; $i <= $daysBetween; $i++) {
				$tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
				$formattedDates[] = date('l', strtotime($tmpDate));
			}    
			return $formattedDates;
	}


public function labeldates($start, $end)
 {
	date_default_timezone_set('UTC');

	$diff = strtotime($end) - strtotime($start);

	$daysBetween = floor($diff/(60*60*24));

	$formattedDates = array();
	for ($i = 0; $i <= $daysBetween; $i++) {
		$tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
		$formattedDates[] = date('F d', strtotime($tmpDate));
	}    
	return $formattedDates;
}


public function dates($start, $end) {
	date_default_timezone_set('UTC');

	$diff = strtotime($end) - strtotime($start);

	$daysBetween = floor($diff/(60*60*24));

	$formattedDates = array();
	for ($i = 0; $i <= $daysBetween; $i++) {
		$tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
		$formattedDates[] = date('Y-m-d', strtotime($tmpDate));
	}    
	return $formattedDates;
}




public function CompletePlan(Request $request)
{


return response()->json(['token'=>$value]);
}


}
