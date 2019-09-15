<?php

namespace Vanguard\Services\MediaPlan;

use Illuminate\Support\Arr;
use Vanguard\Libraries\TimeBelt;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Services\Ratings\TvStationTimeBeltRatings;

/**
 * This service is to get formatted timebelts.
 */
class GetStationTimeBeltRatingService extends GetStationRatingService
{   
    /**
     * Run the query to get station ratings
     */
    public function run()
    {
        $filters = $this->prepareFilters();
        $ratings_service = new TvStationTimeBeltRatings($filters);
        $timebelts = $ratings_service->run();
        return $this->formatResponse($timebelts);
    }

    protected function prepareFilters()
    {
        $saved_filters = json_decode($this->media_plan->filters, true);
        if (!$saved_filters) {
            $saved_filters = array();
        }
        $this->data = array_merge($saved_filters, $this->data);
        return parent::prepareFilters();
    }

    protected function formatResponse($timebelt_list)
    {   
        $program_name_map = $this->getProgramNameMap();
        $timebelt_list->transform(function($timebelt) use ($program_name_map){
            $key = TimeBelt::getTimebeltKey($timebelt);
            $program_name = Arr::get($program_name_map, $key, "Unknown Program");

            return [
                'key' => $key,
                'program' => $program_name,
                'day' => TimeBelt::lengthenDay($timebelt['day']),
                'start_time' => $timebelt['start_time'],
                'end_time' => $timebelt['end_time'],
                'total_audience' => round($timebelt['total_audience']),
                'rating' => $timebelt['rating'],
                'tv_station_key' => $timebelt['station_key'],
                'media_type' => $timebelt['media_type'],
                'station_id' => $timebelt['station_id'],
                'station' => $timebelt['station'],
                'state' => $timebelt['state'],
                'station_type' => $timebelt['station_type']
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
}
