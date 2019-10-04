<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Arr;
use Vanguard\Libraries\DayPartList;

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

    /** THE BELOW FUNCTIONS REQUIRE THAT IN THE QUERY ABOVE A JOIN IS PERFORMED ON MPS_PROFILE_ACTIVITIES */
    public function day($day)
    {
        return $this->where('mps_profile_activities.day', $day);
    } 
    
    public function broadcastType($broadcast_type_list)
    {
        return $this->whereIn('mps_profile_activities.broadcast', $broadcast_type_list);
    }

    /**
     * Just call broadcastType for now
     * @todo should remove this
     */
    public function stationType($station_type_list)
    {
        return $this->broadcastType($station_type_list);
    }

    public function dayPart($day_part)
    {
        return $this->whereBetween('mps_profile_activities.start_time', DayPartList::DAYPARTS[$day_part]);
    }
}