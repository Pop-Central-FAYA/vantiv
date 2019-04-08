<?php

namespace Vanguard\Http\Controllers\MediaPlan;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Criteria;
use Vanguard\Models\MediaPlan;
use Vanguard\Services\MediaPlan\validateCriteriaForm;
use Vanguard\Services\MediaPlan\SuggestPlan;
use Vanguard\Services\MediaPlan\StorePlanningSuggestions;
use Illuminate\Support\Facades\DB;
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
	
	public function customisPlan()
    {
		return view('agency.mediaPlan.custom_plan');
    	
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

		return redirect()->action(
			'MediaPlan\MediaPlanController@getSuggestPlanById', ['id' => $newMediaPlan->id]
		);
	
	}
	
	public function getSuggestPlanById($id)
    {
		$plans = DB::table('media_plan_suggestions')->where('media_plan_id', $id)->get();
		if(count($plans) == 0){
			return redirect()->route("agency.media_plan.criteria_form");
		}
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

    public function summary($media_plan_id)
    {
        $mediaPlan = MediaPlan::with(['client'])->findorfail($media_plan_id);
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1);

        if (count($selectedSuggestions) === 0) {
            // redirect to review suggestions page for user to select suggestions
        }

        $summaryData = $selectedSuggestions->map(function($item, $key) use ($mediaPlan) {
                        $material_lengths = json_decode($item->material_lengths);
                        $item->num_spots = $this->numSpotsPerStationTimebelt($material_lengths);
                        $volume_discount = 0;
                        $agency_commission = $mediaPlan->agency_commission;

                        $cost_summary = json_decode($this->costSummaryPerTimeBelt($material_lengths, $volume_discount, $agency_commission));

                        $item->gross_value = $cost_summary->gross_value;
                        $item->net_value = $cost_summary->net_value;
                        $item->savings = $cost_summary->savings;
                        $item->material_durations = $cost_summary->durations;


                        // $item->volume_discount = 0;
                        // $item->agency_commission = $mediaPlan->agency_commission;
                        // $item->gross_unit_rate = $this->grossUnitRatePerTimebelt($material_lengths, $item->num_spots);
                        // $item->value_less = $item->gross_unit_rate * ((100 - $item->volume_discount) / 100);
                        // $item->net_unit_rate = $item->value_less * ((100 - $item->agency_commission) / 100);
                        // $item->bonus_spots = 0;
                        // $item->cost_bonus_spots = 0;
                        // $item->gross_value = 
                        // $item->net_value = 
                        // $item->net_value_after_bonus_spots = 

                        return $item;
                    });
        $summaryData = json_decode($this->summaryGroupByMedium($summaryData));

        return view('agency.mediaPlan.summary')->with('summary', $summaryData)
                ->with('media_plan', $mediaPlan);
    }

    public function summaryGroupByMedium($summary)
    {
        $summaryByMedium = $summary->groupBy('media_type');

        $groupByResult = [];

        foreach ($summaryByMedium as $key => $value) {

            $groupByResult[] = [
                'medium' => $key,
                'gross_value' => $value->sum('gross_value'),
                'net_value' => $value->sum('net_value'),
                'savings' => $value->sum('savings'),
                'total_spots' => $value->sum('num_spots'),
                'material_durations' => $value->pluck('material_durations')->unique()->collapse()
            ];
        }

        return json_encode($groupByResult);
    }

    public function numSpotsPerStationTimebelt($material_lengths)
    {
        $spots = 0;
        foreach ($material_lengths as $key => $value) {
            foreach ($value->days as $date => $num_spots) {
                $spots += $num_spots;
            }
        }
        return $spots;
    }

    public function costSummaryPerTimeBelt($material_lengths, $vol_disc, $agency_comm)
    {
        $gross_value = 0;
        $net_value = 0;
        $savings = 0;
        $durations = [];

        foreach ($material_lengths as $key => $value) {
            $durations[] = $key;
            $gross_unit_rate = $value->unit_rate;
            $value_less = $gross_unit_rate * ((100 - $vol_disc) / 100);
            $net_unit_rate = $value_less * ((100 - $agency_comm) / 100);
            $spots = 0;

            foreach ($value->days as $date => $num_spots) {
                $spots += $num_spots;
            }

            $gross_value += ($gross_unit_rate * $spots);
            $net_value += ($net_unit_rate * $spots);
        }

        $savings = ($gross_value - $net_value);

        return json_encode(['gross_value' => $gross_value, 'net_value' => $net_value, 'savings' => $savings, 'durations' => $durations]);
    }

    public function approvePlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $mediaPlan->status = 'Approved';
        $mediaPlan->save();
        return redirect()->route('agency.media_plan.summary',['id'=>$mediaPlan->id]);
    }

    public function declinePlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $mediaPlan->status = 'Declined';
        $mediaPlan->save();
        return redirect()->route('agency.media_plan.summary',['id'=>$mediaPlan->id]);
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
			}

		return response()->json(['status'=>"success", 'message'=> "Plan Selected successfully" ]);
		

	}
	
	public function CreatePlan($id)
    {
		$plans = DB::table('media_plan_suggestions')->where('media_plan_id', $id)->where('status', 1)->get();
		$plans_details = DB::table('media_plans')->where('id', $id)->get();

		if(count($plans_details) == 0){
			return redirect()->route("agency.media_plan.criteria_form");
		}
	
		$fayaFound = [];
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
	$programs_id = json_decode($request->get('data'));
	$programs_id = collect($programs_id);
	$programs_id = $this->groupById($programs_id);
	$client_name = $request->get('client_name');
	$product_name = $request->get('product_name');
	$plan_id = $request->get('plan_id');
	foreach($programs_id as $key => $value){
		DB::table('media_plan_suggestions')
		->where('id', $key)
		->update(['material_length' => $value]);
	} 

	DB::table('media_plans')
	->where('id', $plan_id)
	->update(['client_id' => $client_name, 'product_name' => $product_name,]);
	

	return response()->json(['msg'=>"Good to go", 'msgs'=>$programs_id]);


}


public function groupById($query)
{

	$result = $query->groupBy(['id', 'material_length']);



	return $result;


}

public function groupByDuration($query)
{
	return $query->groupBy('material_length');
}


}
