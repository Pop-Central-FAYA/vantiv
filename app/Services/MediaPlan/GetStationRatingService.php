<?php

namespace Vanguard\Services\MediaPlan;

use Illuminate\Support\Arr;
use Vanguard\Libraries\TimeBelt;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Services\Ratings\TvRatingsList;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to get formatted timebelts.
 */
class GetStationRatingService implements BaseServiceInterface
{   
    const DAY_MAP = array(
        "Mon" => "Monday",
        "Tue" => "Tuesday",
        "Wed" => "Wednesday",
        "Thu" => "Thursday",
        "Fri" => "Friday",
        "Sat" => "Saturday",
        "Sun" => "Sunday"
    );

    protected $media_plan;
    protected $data;

    public function __construct($data, $media_plan)
    {
        $this->data = $data;
        $this->media_plan = $media_plan;
    }

    /**
     * Run the query to get station ratings
     */
    public function run()
    {
        $filters = $this->prepareFilters();
        $ratings_service = new TvRatingsList($filters);
        $timebelts = $ratings_service->run();
        if (count($timebelts) > 0) {
            $data = $this->formatTimebelts($timebelts);
            return $this->prepareResponse($data);
        }
        return [];
    }

    protected function prepareFilters()
    {
        $fields = [
            'gender' => 'gender', 'social_class' => 'criteria_social_class',
            'region' => 'criteria_region', 'state' => 'criteria_state',
            'age_groups' => 'criteria_age_groups'
        ];
        $filters = $this->data;
        foreach ($fields as $key => $model_field) {
           $decoded = json_decode($this->media_plan[$model_field], true);
           if (is_array($decoded) && count($decoded) > 0) {
                $filters[$key] = $decoded;
           }
        }
        return collect($filters)->reject(function ($value, $key) {
            return (is_string($value) && strtolower($value) == 'all');
        })->toArray();
    }

    protected function formatTimebelts($timebelt_list)
    {   
        $program_name_map = $this->getProgramNameMap();
        $timebelt_list->transform(function($timebelt) use ($program_name_map){
            $key = TimeBelt::getTimebeltKey($timebelt);
            $program_name = Arr::get($program_name_map, $key, "Unknown Program");

            return [
                'key' => $key,
                'media_type' => $timebelt['media_type'],
                'station' => $timebelt['station'],
                'station_type' => $timebelt['station_type'],
                'state' => $timebelt['station_state'],
                'program' => $program_name,
                'day' => $this->formatDay($timebelt['day']),
                'start_time' => $timebelt['start_time'],
                'end_time' => $timebelt['end_time'],
                'total_audience' => round((float) $timebelt['total_audience']),
                'rating' => $timebelt['rating'],
                'station_id' => $timebelt['station_id'],
            ];
        });
        return $timebelt_list;
    }
                  
    protected function getProgramNameMap()
    {
        return MediaPlanProgram::all()->mapWithKeys(function($item) {
            $key = TimeBelt::getTimebeltKey($item);
            return [$key => $item->program_name];
        })->toArray();
    }

    protected function formatDay($day)
    {
        return Arr::get(static::DAY_MAP, $day, $day);
    }

    /**
     * @todo, there is duplicate information here, look into doing a lot of this work on the frontend
     */
    protected function prepareResponse($timebelt_list)
    {
        return [
            'stations' => $timebelt_list->groupBy(function ($item, $key) {
                return $item['station'];
            })->toArray(),
            'total_graph' => $timebelt_list->groupBy(['day', 'station'])->toArray(),
        ];
    }
}
