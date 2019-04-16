<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MpsAudience;
use Vanguard\Models\MpsAudienceProgramActivity;
use Illuminate\Support\Collection;
use Vanguard\Services\AgencyRating\GetStationRatings;

class SuggestPlan
{
    const SUPPORTED_CRITERIA = array("age", "state", "social_class", "gender", "lsm", "region");

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * This service just GetsStationRatings, will probably do more later
     * Such as caching the query that requested this plan, so the work is 
     * Not done again in the future
     * @todo Fix this field getting (age_groups should be sent as age etc)
     */
    public function suggestPlan()
    {   
        $criteria_fields = array(
            "lsm" => $this->request->lsm,
            "social_class" => $this->request->social_class,
            "gender" => $this->request->gender,
            "region" => $this->request->region,
            "state" => $this->request->state,
            "age" => $this->request->age_groups
        );
        $criteria = $this->formatCriteria($criteria_fields);
        $ratings = new GetStationRatings($criteria, $this->request); 
        $res = $ratings->getRatings();
        return $res;
    }

    /**
     * Only send back criteria that are supported by this get ratings service
     * @todo Log as a warning whenever we encounter criteria that is not supported, but do not throw an exception 
     * @todo Let the requester send all the criteria they want (instead of using all or both)
     */
    private function formatCriteria($input_criteria) {
        $formatted_criteria = array();
        foreach (static::SUPPORTED_CRITERIA as $supported_criteria) {
            if (isset($input_criteria[$supported_criteria]) && $input_criteria[$supported_criteria] != null) {
                $value = $input_criteria[$supported_criteria];
                /**
                 * If the user selected all or both, then they want all the values, so just unset
                 * so the query gets all the values.
                 * This assumes that there are no blank values in the audience db (which according to the data there should not be)
                 */
                if (!in_array($value, ['All', 'Both'])) {
                    $formatted_criteria[$supported_criteria] = $value;
                }
            }
        }
        return $formatted_criteria;
    }


    // public function suggestPlan()
    // {
    //     $criteria = $this->request;
    //     $media_type = $criteria->media_type;
    //     // Fetch mps audiences, programs, stations, time duration, based on criteria
    //     $query = MpsAudienceProgramActivity::when($media_type, function ($query, $media_type)
    //                 {
    //                     if ($media_type === "All") {
    //                         return $query->whereNotNull('media_type');
    //                     }
    //                     return $query->where('media_type', $media_type);
    //                 })
    //                 ->whereHas('audience', function ($query) use ($criteria)
    //                 {
    //                     $lsm = $criteria->lsm;
    //                     $social_class = $criteria->social_class;
    //                     $gender = $criteria->gender;
    //                     $region = $criteria->region;
    //                     $state = $criteria->state;
    //                     $age_groups = $criteria->age_groups;

    //                     $query->when($lsm, function ($query, $lsm)  {
    //                         $query->whereIn('lsm', $lsm);
    //                     });

    //                     $query->when($social_class, function ($query, $social_class)    {
    //                         $query->whereIn('social_class', $social_class);
    //                     });

    //                     $query->when($gender, function ($query, $gender)    {
    //                         if ($gender === "Both") {
    //                             $query->whereNotNull('gender');
    //                         }
    //                         $query->where('gender', $gender);
    //                     });

    //                     $query->when($region, function ($query, $region)    {
    //                         if ($region === "All") {
    //                             $query->whereNotNull('region');
    //                         }
    //                         $query->whereIn('region', $region);
    //                     });

    //                     $query->when($state, function ($query, $state)  {
    //                         if ($state === "All") {
    //                             $query->whereNotNull('state');
    //                         }
    //                         $query->whereIn('state', $state);
    //                     });

    //                     $query->when($age_groups, function ($query, $age_groups)    {
    //                         foreach ($age_groups as $range) {
    //                             $query->orWhere(function ($query) use ($range) {
    //                                 $query->where('age', '>=', $range['min'])
    //                                       ->Where('age', '<=', $range['max']);
    //                             });
    //                         }
    //                     });
    //                 })
    //                 ->get();

    //     // group suggestions by station, program & time belt. Count total audience for each group
    //     $suggestions = $this->groupSuggestions($query);
    //     $suggestionsByStation = $this->groupSuggestionsByStation($query);

    //     $fayaFound = array(
    //         'total_tv' => $this->countByMediaType($suggestions, 'Tv'),
    //         'total_radio' => $this->countByMediaType($suggestions, 'Radio'),
    //         'programs_stations' => $suggestions,
    //         'stations' => $suggestionsByStation,
    //         'total_audiences' => $this->totalAudienceFound($suggestions)
    //     );

    //     return $fayaFound;
    // }

    // public function groupByProgramStationTimeBelt($input) 
    // {
    //     $output = Array();
    //     foreach($input as $value) {
    //         $output_element = &$output[$value['station'] . "_" . $value['program'] . "_" . $value['start_time'] . "_" . $value['end_time']];
    //         $output_element['media_type'] = $value['media_type'];
    //         $output_element['station'] = $value['station'];
    //         $output_element['program'] = $value['program'];
    //         $output_element['start_time'] = $value['start_time'];
    //         $output_element['end_time'] = $value['end_time'];
    //         $output_element['duration'] = $this->getDurationFromTimeBelt($value['start_time'], $value['end_time']);
    //         !isset($output_element['total_audience']) && $output_element['total_audience'] = 0;
    //         $output_element['total_audience'] += 1;
    //     }

    //     // sort by target audience from highest to the lowest
    //     $output = array_values($output);
    //     usort($output, function($a, $b) {
    //         if($a['total_audience']==$b['total_audience']) return 0;
    //         return $a['total_audience'] < $b['total_audience']?1:-1;
    //     });

    //     return $output;
    // }

    // public function groupSuggestions($query)
    // {
    //     $query = $query->groupBy(function ($item, $key) {
    //         return $item->station.'_'.$item->program.'_'.$item->start_time.'_'.$item->end_time.'_'.$item->day;
    //     });
    //     $query = $query->map(function($item, $key) {
    //                     $count = count($item);
    //                     $item = $item->first();
    //                     $item->audience = $count;
    //                     return $item;
    //                 });
    //     $query = $query->flatten();
    //     $query = $query->sortByDesc('audience');
    //     return $query;
    // }

    // public function groupSuggestionsByStation($query)
    // {
    //     return $query->groupBy('station');
    // }

    // public function countByMediaType($collection, $media_type='')
    // {
    //     return $collection->where('media_type', $media_type)->sum('audience');
    // }

    // public function totalAudienceFound($collection)
    // {
    //     return $collection->sum('audience');
    // }
}