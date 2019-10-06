<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Arr;
use Vanguard\Libraries\DayPartList;
use Vanguard\Libraries\TimeBelt;

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

    public function socialClass($social_class_list)
    {
        if (is_array($social_class_list) && count($social_class_list) == 0) {
            return $this;
        }

        return $this->whereIn('mps_profiles.social_class', $social_class_list);
    }

    /**
     * When filtering mps audiences by state, we should also limit the result set by tv stations that actually belong to that state
     * or are network/international.
     * For instance, we get cases where the targeting criteria is Abuja state, but for some reason in the data, someone who's state is Abuja
     * is also marked as watching a regional channel in Osun state. So we show Osun based stations in the result set, which is not really
     * relevant. So just add the filtering here once and for all
     */
    public function state($state_list)
    {
        if (is_array($state_list) && count($state_list) == 0) {
            return $this;
        }

        if (!is_array($state_list)) {
            $state_list = [$state_list];
        }

        $tv_station_state = $state_list;
        $tv_station_state[] = "";

        $query = $this->whereIn('mps_profiles.state', $state_list)
                        ->whereIn('tv_stations.state', $tv_station_state);
        return $query;
    }

    /**
     * See the explanation for the state method for why we also limit by tv stations that belong to the same state
     */
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

        $tv_station_region = $mapped_regions;
        $tv_station_region[] = "";

        $query = $this->whereIn('mps_profiles.region', $mapped_regions)
                        ->whereIn('tv_stations.region', $tv_station_region);
        return $query;
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

    public function stationType($station_type_list)
    {
        if (is_array($station_type_list) && count($station_type_list) == 0) {
            return $this;
        }

        if (!is_array($station_type_list)) {
            $station_type_list = [$station_type_list];
        }
        return $this->whereIn('tv_stations.type', $station_type_list);
    }

    public function dayPart($day_part)
    {
        return $this->whereBetween('mps_profile_activities.start_time', DayPartList::DAYPARTS[$day_part]);
    }

    public function stationKey($station_key)
    {
        if (is_array($station_key) && count($station_key) == 0) {
            return $this;
        }

        if (!is_array($station_key)) {
            $station_key = [$station_key];
        }
        return $this->whereIn('mps_profile_activities.tv_station_key', $station_key);
    }

    /**
     * This method will filter by a combination of tv station key, day, and start_time.
     * This will be used to calculate the netreach of a media plan
     */
    public function adSpots($spots_list)
    {
        $spots_qry = $this->where(function($query) use ($spots_list) {
            foreach ($spots_list as $item) {
                $query->orWhere(function($sub) use ($item) {
                    $sub->where('mps_profile_activities.tv_station_key', $item['station_key'])
                        ->where('mps_profile_activities.day', TimeBelt::shortenDay($item['day']))
                        ->where('mps_profile_activities.start_time', $item['start_time']);
                });
            }
        });
        return $spots_qry;
    }
}
