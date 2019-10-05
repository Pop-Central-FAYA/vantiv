<?php

namespace Vanguard\Services\Ratings;

use DB;

// use Vanguard\Services\Ratings\GetNetReachService; $filters = ["state" => ["Abuja"], "social_class" => ["A", "B", "C"], "gender" => ["Male", "Female"]];$service = new GetNetReachService($filters); $service->run()
class GetNetReachService extends AbstractRatingService
{

    protected function modifyQuery($query)
    {
        $query = $query->groupBy("mps_profile_activities.ext_profile_id");

        $columns = [
            DB::raw("SUM(pop_weight) as total_audience")
        ];

        $main_query = DB::query()->fromSub($query, "tbl")
                            ->addSelect($columns);
        return $main_query;
    }
    
    protected function formatResponse($data) 
    {
        $total_audience = round((double)$data->total_audience);
        return ["total_audience" => $total_audience];
    }
}