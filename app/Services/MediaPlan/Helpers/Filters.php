<?php

namespace Vanguard\Services\MediaPlan\Helpers;

class Filters
{
    public static function prepareTargetingFilters($media_plan, $filters)
    {
        $fields = [
            'gender' => 'gender', 'social_class' => 'criteria_social_class',
            'region' => 'criteria_region', 'state' => 'criteria_state',
            'age_groups' => 'criteria_age_groups'
        ];
        foreach ($fields as $key => $model_field) {
           $decoded = json_decode($media_plan[$model_field], true);
           if (is_array($decoded) && count($decoded) > 0) {
                $filters[$key] = $decoded;
           }
        }
        return collect($filters)->reject(function ($value, $key) {
            return (is_string($value) && strtolower($value) == 'all');
        })->toArray();
    }
}