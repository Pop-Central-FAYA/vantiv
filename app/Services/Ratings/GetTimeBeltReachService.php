<?php

namespace Vanguard\Services\Ratings;

use DB;

// use Vanguard\Services\Ratings\GetTimeBeltReachService; $filters = ["state" => ["Abuja"], "social_class" => ["A", "B", "C"], "gender" => ["Male", "Female"], "station_key" => "ba041ea4acf277b105a0b8db9764c8a1"];$service = new GetTimeBeltReachService($filters); $service->run()

class GetTimeBeltReachService extends AbstractRatingService
{

    /**
     * This Query is to get actual timebelts that belong to a particular station/list of stations etc
     */
    protected function modifyQuery($query)
    {
        $query = $query->groupBy(
            "tv_stations.key", 
            "mps_profile_activities.day", 
            "mps_profile_activities.start_time", 
            "mps_profile_activities.ext_profile_id"
        );
        $columns = [
            "station_name", "station_state",  "station_type", "station_key", "station_id",
            "day", "start_time", "end_time",
            DB::raw("SUM(pop_weight) as total_audience")
        ];

        $main_query = DB::query()->fromSub($query, "tbl")
                            ->addSelect($columns)
                            ->groupBy("station_key", "day", "start_time")
                            ->orderBy("total_audience", "DESC");
        return $main_query;
    }
    
    protected function formatResponse($data) 
    {
        $data->transform(function($timebelt) {
            $total_audience = (double) $timebelt->total_audience;
            // $rating = ($total_audience / $universe_size) * 100;

            return [
                "day" => $timebelt->day,
                "start_time" => $timebelt->start_time,
                "end_time" => $timebelt->end_time,
                "total_audience" => $total_audience,
                "station_key" => $timebelt->station_key,
                "station_id" => $timebelt->station_id,
                "station" => $timebelt->station_name,
                "state" => $timebelt->station_state,
                "station_type" => $timebelt->station_type,
                "media_type" => 'tv'
            ];
        });
        return $data->toArray();
    }
}