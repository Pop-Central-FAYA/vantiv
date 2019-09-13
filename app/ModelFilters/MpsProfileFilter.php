<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Arr;

class MpsProfileFilter extends ModelFilter
{

    /**
     * This method is there to be backwards compatible with how the ages are sent from the frontend
     */
    public function ageGroups($age_range_list)
    {
        return $this->age($age_range_list);
    }

    public function age($age_range_list)
    {   
        if (is_array($age_range_list) && count($age_range_list) == 0) {
            return $this;
        }

        $age_qry = $this->where(function($query) use ($age_range_list) {
            foreach($age_range_list as $age_range) {
                $query->orWhereBetween('mps_profiles.age', array($age_range['min'], $age_range['max']));
            }
        });
        return $age_qry;
    }

    public function gender($gender_list)
    {
        if (is_array($gender_list) && count($gender_list) == 0) {
            return $this;
        }
        return $this->whereIn('mps_profiles.gender', $gender_list);
    }

    public function state($state_list)
    {
        if (is_array($state_list) && count($state_list) == 0) {
            return $this;
        }

        if (!is_array($state_list)) {
            $state_list = [$state_list];
        }
        return $this->whereIn('mps_profiles.state', $state_list);
    }

    public function socialClass($social_class_list)
    {
        if (is_array($social_class_list) && count($social_class_list) == 0) {
            return $this;
        }

        return $this->whereIn('mps_profiles.social_class', $social_class_list);
    }

    public function region($region_list)
    {
        if (is_array($region_list) && count($region_list) == 0) {
            return $this;
        }

        $region_map = [
            'North-West' => 'North West',
            'North-East (Jos)' => 'North East',
            'North Central (Abuja)' => 'North Central',
            'South-West (Ibadan, Benin)' => 'South West',
            'South-East (Onitsha, Aba)' => 'South East',
            'South-South (Rivers)' => 'South South'
        ];
        $mapped_regions = [];
        foreach ($region_list as $value) {
            $mapped_regions[] = Arr::get($region_map, $value, $value);
        }
        return $this->whereIn('mps_profiles.region', $mapped_regions);
    }
    
}
