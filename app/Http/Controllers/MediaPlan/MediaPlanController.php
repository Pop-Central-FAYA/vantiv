<?php

namespace Vanguard\Http\Controllers\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\WalkIns;
use Vanguard\Models\MpsAudience;
use Vanguard\Models\MpsAudienceProgramActivity;
use Auth; use Validator;
use Illuminate\Support\Collection;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Models\Criteria;
use Vanguard\Models\SubCriteria;


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

    /**
     * Get a validator for an incoming criteria request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateCriteriaForm(array $data)
    {
        $rules = [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:today',
            'region' => 'required|array',
            'state' => 'required|array',
            'gender' => 'required|string',
            'lsm' => 'required|array',
            'social_class' => 'required|array',
            'age_groups' => 'required|array',
            'agency_commission' => 'required|numeric',
            'media_type' => 'required|string'
        ];
        return Validator::make($data, $rules,
            [
                'required' => ':attribute is required',
                'unique' => ':attribute already exists',
            ]
        );
    }

    public function suggestPlan(Request $request)
    {
    	// validate request
    	$validation = $this->validateCriteriaForm($request->all());
    	if ($validation->fails()) {
    		// var_dump($validation->errors()); return;
            return back()->withErrors($validation)->withInput();
    	}

    	$media_type = $request->media_type;

    	// Fetch mps audiences, programs, stations, time duration, based on criteria
	    $query = MpsAudienceProgramActivity::when($media_type, function ($query, $media_type)
					{
						if ($media_type === "Both") {
							return $query->whereNotNull('media_type');
						}
						return $query->where('media_type', $media_type);
					})
	     			->whereHas('audience', function ($query) use ($request)
	     			{
	     				$lsm = $request->lsm;
	     				$social_class = $request->social_class;
	     				$gender = $request->gender;
	     				$region = $request->region;
	     				$state = $request->state;
	     				$age_groups = $request->age_groups;

	     				$query->when($lsm, function ($query, $lsm)	{
	     					$query->whereIn('lsm', $lsm);
	     				});

	     				$query->when($social_class, function ($query, $social_class)	{
	     					$query->whereIn('social_class', $social_class);
	     				});

	     				$query->when($gender, function ($query, $gender)	{
	     					if ($gender === "Both") {
								$query->whereNotNull('gender');
							}
	     					$query->where('gender', $gender);
	     				});

	     				$query->when($region, function ($query, $region)	{
	     					if ($region === "All") {
								$query->whereNotNull('region');
							}
	     					$query->whereIn('region', $region);
	     				});

	     				$query->when($state, function ($query, $state)	{
	     					if ($state === "All") {
								$query->whereNotNull('state');
							}
	     					$query->whereIn('state', $state);
	     				});

	     				$query->when($age_groups, function ($query, $age_groups)	{
	     					foreach ($age_groups as $range) {
	     						$query->orWhere(function ($query) use ($range) {
					                $query->where('age', '>=', $range['min'])
					                      ->Where('age', '<=', $range['max']);
					            });
	     					}
	     				});
	     			})
	     			->get();

	    if (!$query) {
	    	// faya did not find any result that matched filter criteria
    		return view('agency.mediaPlan.display_suggestions')->with('criterias', $criterias);
	    }

	    // group suggestions by station, program & time belt. Count total audience for each group
	    $suggestions = $this->groupSuggestions($query);
	    $suggestionsByStation = $this->groupSuggestionsByStation($query);
	    // echo json_encode($suggestions); return;

	    // store planning criteria and suggestions
	    $this->storeMediaPlantoDB($request, $suggestions);

	    $fayaFound = [
	    	'total_tv' => $this->countByMediaType($suggestions, 'Tv'),
	    	'total_radio' => $this->countByMediaType($suggestions, 'Radio'),
	    	'programs_stations' => $suggestions,
	    	'stations' => $suggestionsByStation,
	    	'total_audiences' => $this->totalAudienceFound($suggestions)
	    ];

	    return view('agency.mediaPlan.display_suggestions')->with('fayaFound', $fayaFound);
    }

    public function storeMediaPlantoDB($criteriaForm, $suggestions)
    {
    	$newMediaPlan = MediaPlan::create([
    		'criteria_gender' => $criteriaForm->gender,
    		'criteria_lsm' => serialize($criteriaForm->lsm),
    		'criteria_social_class' => serialize($criteriaForm->social_class),
    		'criteria_region' => serialize($criteriaForm->region),
    		'criteria_state' => serialize($criteriaForm->state),
    		'criteria_age_groups' => serialize($criteriaForm->age_groups),
    		'agency_commission' => $criteriaForm->agency_commission,
    		'start_date' => $criteriaForm->start_date,
    		'end_date' => $criteriaForm->end_date,
    		'planner_id' => Auth::id(),
    		'status' => 'Pending'
    	]);

    	foreach ($suggestions as $key => $suggestion) {
    		MediaPlanSuggestion::create([
    			'media_plan_id' => $newMediaPlan->id,
    			'media_type' => $suggestion->media_type,
    			'station' => $suggestion->station,
    			'program' => $suggestion->program,
    			'day' => $suggestion->day,
    			'start_time' => $suggestion->start_time,
    			'end_time' => $suggestion->end_time,
    			'total_audience' => $suggestion->audience
    		]);
    	}
    }

    public function getDurationFromTimeBelt($start_time, $end_time)
    {
    	return round((strtotime($end_time) - strtotime($start_time)));
    }

    public function groupByProgramStationTimeBelt($input) 
    {
		$output = Array();
		foreach($input as $value) {
			$output_element = &$output[$value['station'] . "_" . $value['program'] . "_" . $value['start_time'] . "_" . $value['end_time']];
			$output_element['media_type'] = $value['media_type'];
			$output_element['station'] = $value['station'];
			$output_element['program'] = $value['program'];
			$output_element['start_time'] = $value['start_time'];
			$output_element['end_time'] = $value['end_time'];
			$output_element['duration'] = $this->getDurationFromTimeBelt($value['start_time'], $value['end_time']);
			!isset($output_element['total_audience']) && $output_element['total_audience'] = 0;
			$output_element['total_audience'] += 1;
		}

		// sort by target audience from highest to the lowest
		$output = array_values($output);
		usort($output, function($a, $b) {
			if($a['total_audience']==$b['total_audience']) return 0;
    		return $a['total_audience'] < $b['total_audience']?1:-1;
		});

		return $output;
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
		$filtered = $collection->where('media_type', $media_type);
		$filtered->all();
		return $filtered->count();
	}

	public function totalAudienceFound($collection)
	{
		return $collection->sum('audience');
	}

}
