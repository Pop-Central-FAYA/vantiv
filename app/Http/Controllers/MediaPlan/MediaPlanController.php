<?php

namespace Vanguard\Http\Controllers\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\WalkIns;
use Vanguard\Models\MpsAudience;
use Auth; use Validator;
use Illuminate\Support\Collection;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanChannel;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Models\Criteria;
use Vanguard\Models\SubCriteria;


class MediaPlanController extends Controller
{
    public function index($value='')
    {
    	$criterias = [
        	'regions' => [
        		'NW', 'NE', 'NC', 'SW', 'SE', 'SS', 'Lagos'
        	],
        	'states' => [
        		'Lagos', 'Kano', 'Kaduna', 'Kano', 'Abia', 'Anambra', 'Delta', 'Adamawa'
        	],
        	'genders' => [
        		'Male', 'Female', 'Both'
        	],
        	'living_standard_measures' => [
        		'LSM 1', 'LSM 2', 'LSM 3', 'LSM 4', 'LSM 5', 'LSM 6', 'LSM 7', 'LSM 8', 'LSM 9', 'LSM 10', 'LSM 11', 'LSM 12'
        	],
        	'social_classes' => [
        		'A', 'B', 'C', 'D', 'E', 'F'
        	]
        ];

        foreach ($criterias as $key => $criteria) {
        	$newCriteria = Criteria::create(['name' => $key]);
        	foreach ($criteria as $value) {
        		SubCriteria::create([
        			'criteria_id' => $newCriteria->id,
        			'name' => $value
        		]);
        	}
        }


    	// print_r(WalkIns::get());

    	// foreach ($criterias as $key => $value) {
    	// 	print_r($value);
    	// }

    	// 'mps_audience_id', 'media_channel', 'station', 'program', 'start_time', 'end_time'
    	// $bigArr = [
    	// 	[
    	// 		'mps_audience_id'=>'1', 'media_channel'=>'Tv', 'station'=>'super story', 'program' => 'NTA 2', 'start_time'=>'10:10', 'end_time'=>'10:40'
	    // 	],
	    // 	[
    	// 		'mps_audience_id'=>'1', 'media_channel'=>'Tv', 'station'=>'super story', 'program' => 'NTA 2', 'start_time'=>'10:10', 'end_time'=>'10:50'
	    // 	],
	    // 	[
    	// 		'mps_audience_id'=>'1', 'media_channel'=>'Tv', 'station'=>'super story', 'program' => 'NTA 2', 'start_time'=>'10:10', 'end_time'=>'10:40'
	    // 	],
	    // 	[
    	// 		'mps_audience_id'=>'1', 'media_channel'=>'Radio', 'station'=>'Hubme', 'program' => 'NTA 4', 'start_time'=>'10:10', 'end_time'=>'10:40'
	    // 	],
	    // 	[
    	// 		'mps_audience_id'=>'1', 'media_channel'=>'Radio', 'station'=>'Laugh Matters', 'program' => 'NTA 10', 'start_time'=>'10:10', 'end_time'=>'10:40'
	    // 	]
	    // ];

	    // $result = $this->groupByProgramStationTimeBelt($bigArr, 4);
	    // var_dump($result);
	    // $count = $this->countByMediaChannel($result, 'Tv');
	    // var_dump($count);
	    // $totalAud = $this->totalAudienceFound($result);
	    // var_dump($totalAud);
	    // var_dump(Auth::id());
    }

    public function criteriaForm(Request $request)
    {
    	$criterias = Criteria::with(['subCriterias'])->get();
    	// return criterias array with the frontend view, in order to populate criteria inputs
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
            'plan_name'=>'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:today',
            'region' => 'required|array',
            'state' => 'required|array',
            'gender' => 'required|string',
            'lsm' => 'required|array',
            'social_class' => 'required|array',
            'target_age_groups' => 'required|array',
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
    		// var_dump($validator->errors());
            return back()->withErrors($validation)->withInput();
    	}

    	$gender = $request->gender;
    	$region = $request->regions;
    	$lsm = $request->lsm;
    	$social_class = $request->social_class;
    	$target_age_groups = $request->target_age_groups;
    	$media_details = $request->media_details;

    	// Fetch mps audiences, programs, stations, time duration, based on criteria
    	$query = MpsAudience::with(['programActivities'])
					->when($gender, function ($query, $gender) {
						if ($gender === "Both") {
							return $query->whereNotNull('gender');
						}
	                    return $query->where('gender', $gender);
	                })
	                ->when($region, function ($query, $region) {
						if ($region === "All") {
							return $query->whereNotNull('region');
						}
	                    return $query->whereIn('region', $region);
	                })
	                ->when($media_details, function ($query, $media_details) {
						$query->whereHas('programActivities', function ($query, $media_details) {
		                	foreach ($media_details as $key => $value) {
								$query->where('media_type', $value['media_type']);
							}
	                    });
	                })
	                ->when($target_age_groups, function ($query, $target_age_groups) {
						foreach ($target_age_groups as $key => $value) {
							$query->orWhere(function ($query, $value) {
				                $query->where('age', '>=', $value['min'])
				                      ->Where('age', '<=', $value['max']);
				            });
						}
	                })
	                ->whereIn('lsm', $lsm)
	                ->whereIn('social_class', $social_class)
	                ->get()
	                ->pluck('programActivities');

	    if (!$query) {
	    	// faya did not find any result that matched filter criteria
	    }

	    /*
	     * Group by stations, program and time belt
	     * Count total audience for each grouped data
	     * Sort data by total audience from highest to lowest
	     * limit output by result limit set by planner
	     */
	    $result = $this->groupByProgramStationTimeBelt($query->toArray(), $request->result_limit);

	    // store criteria with fayafound programs/stations details
	    $this->storeMediaPlantoDB($request->all(), $result);

	    $fayaFound = [
	    	'total_tv' => $this->countByMediaChannel($result, 'Tv'),
	    	'total_radio' => $this->countByMediaChannel($result, 'Radio'),
	    	'programs_stations' => $result,
	    	'total_audiences' => $this->totalAudienceFound($result)
	    ];

    }

    public function storeMediaPlantoDB($criteriaForm, $suggestions)
    {
    	$media_details = $criteriaForm->media_details;
    	$total_budget = 0; $total_target_reach = 0;

    	foreach ($media_details as $key => $value) {
    		$total_budget += $value['budget'];
    		$total_target_reach += $value['reach'];
    	}

    	$newMediaPlan = MediaPlan::create([
    		'plan_id' => uniqid(),
    		'campaign_name' => $criteriaForm->campaign_name,
    		'product_name' => $criteriaForm->product,
    		'gender' => $criteriaForm->gender,
    		'client_id' => $criteriaForm->client,
    		'brand_id' => $criteriaForm->brand,
    		'start_date' => $criteriaForm->start_date,
    		'end_date' => $criteriaForm->end_date,
    		'target_age_groups' => serialize($criteriaForm->target_age_groups),
    		'lsms' => serialize($criteriaForm->lsm),
    		'regions' => serialize($criteriaForm->regions),
    		'result_limit' => $criteriaForm->result_limit,
    		'planner_id' => Auth::id(),
    		'status' => 0,
    		'total_budget' => $total_budget,
    		'actual_spend' => 0,
    		'total_target_reach' => $total_target_reach,
    		'actual_reach' => 0
    	]);

    	foreach ($media_details as $key => $value) {
    		$total_budget += $value['budget'];
    		$total_target_reach += $value['reach'];
    		MediaPlanChannel::create([
    			'media_plan_id' => $newMediaPlan->id,
    			'channel' => $value['channel'],
    			'budget' => $value['budget'],
    			'target_reach' => $value['target_reach'],
    			'material_length' => serialize($value['material_length'])
	    	]);
    	}

    	foreach ($suggestions as $key => $suggestion) {
    		MediaPlanSuggestion::create([
    			'media_plan_id' => $newMediaPlan->id,
    			'channel' => $suggestion['channel'],
    			'station' => $suggestion['station'],
    			'program' => $suggestion['program'],
    			'start_time' => $suggestion['start_time'],
    			'end_time' => $suggestion['end_time'],
    			'total_audience' => $suggestion['total_audience'],
    			'total_spots' => 0,
    			'status' => 0,
    		]);
    	}
    }

    public function getDurationFromTimeBelt($start_time, $end_time)
    {
    	return round((strtotime($end_time) - strtotime($start_time)));
    }

    public function groupByProgramStationTimeBelt($input, $limit=0) 
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

		// limit output by result limit set by planner
		if ($limit > 0) {
			$output = array_slice($output, 0, $limit); 
		}

		return $output;
	}

	public function countByMediaChannel($input, $media_type='')
	{
		$collection = collect($input);
		$filtered = $collection->where('media_type', $media_type);
		$filtered->all();
		return $filtered->count();
	}

	public function totalAudienceFound($input)
	{
		$collection = collect($input);
		return $collection->sum('total_audience');
	}

}
