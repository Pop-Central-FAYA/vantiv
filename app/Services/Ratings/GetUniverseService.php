<?php

namespace Vanguard\Services\Ratings;

use DB;
use Vanguard\Models\MpsProfile;

// use Vanguard\Services\Ratings\GetUniverseService; $filters = ["state" => ["Abuja"], "social_class" => ["A", "B", "C"], "gender" => ["Male", "Female"]];$service = new GetUniverseService($filters); $service->run()

class GetUniverseService extends AbstractRatingService
{

    /**
     * This Query is to get the actual summation of the universe
     */
    protected function modifyQuery($query)
    {
        $query = $query->groupBy("mps_profile_activities.ext_profile_id");

        $columns = [
            DB::raw("NULL as population, SUM(pop_weight) as target_population")
        ];

        $population_query = MpsProfile::selectRaw("SUM(pop_weight) as population, NULL as target_population");

        $main_query = DB::query()->fromSub($query, "tbl")
                            ->addSelect($columns)
                            ->union($population_query);
        return $main_query;
    }
    
    protected function formatResponse($data) 
    {
        $target_population = 0;
        $population = 0;
        
        foreach ($data as $row) {
            if (is_null($row->population) === false) {
                $population = (double) $row->population;
            }
            if (is_null($row->target_population) === false) {
                $target_population = (double) $row->target_population;
            }
        }
        return [
            "population" => round($population), 
            "target_population" => round($target_population)
        ];
    }
}