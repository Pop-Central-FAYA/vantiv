<?php

namespace Vanguard\Services\MediaPlan;

use Vanguard\Services\BaseServiceInterface;
use Vanguard\Services\Ratings\TvStationRatings;

/**
 * This service is to get formatted stations.
 */
class GetStationRatingService implements BaseServiceInterface
{   
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
        $ratings_service = new TvStationRatings($filters);
        return $ratings_service->run();
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

}
