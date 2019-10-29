<?php

namespace Vanguard\Services\Ratings;

use DB;
use Illuminate\Support\Facades\Log;
use Vanguard\Libraries\Query;
use Vanguard\Libraries\TimeBelt;
use Vanguard\Models\MpsProfile;
use Vanguard\Models\TvStation;
use Vanguard\Services\BaseServiceInterface;

class StoreMediaPlanDeliverables implements BaseServiceInterface
{

    public function __construct($media_plan) {
        $this->media_plan = $media_plan;
    }

    public function run() {
        $this->suggestions = $this->getSuggestionsWithInsertions();

        //the usage of max is so we do not have division by zero errors
        $total_insertions = $this->getTotalInsertions();
        $gross_impressions = $this->getGrossImpressions($total_insertions);
        $total_grp = $this->getTotalGrp($gross_impressions);
        $net_reach = $this->getNetReach();
        $avg_frequency = ($gross_impressions/max(1, $net_reach));
        $net_media_cost = $this->getNetMediaCost();
        $cpm = (($net_media_cost/max(1, $gross_impressions)) * 1000);
        $cpp = ($net_media_cost/max(1, $total_grp));

        $this->media_plan->total_insertions = $total_insertions;
        $this->media_plan->gross_impressions = $gross_impressions;
        $this->media_plan->total_grp = $total_grp;
        $this->media_plan->net_reach = $net_reach;
        $this->media_plan->avg_frequency = round($avg_frequency, 2);
        $this->media_plan->net_media_cost = $net_media_cost;
        $this->media_plan->cpm = round($cpm, 2);
        $this->media_plan->cpp = round($cpp, 2);
        
        DB::transaction(function() {
            $this->media_plan->save();
        });
       
        return $this->media_plan;
    }

    /**
     * This method will filter the suggestions list by only suggestions that have 
     * had exposures chosen
     */
    protected function getSuggestionsWithInsertions()
    {
        $collection = $this->media_plan->suggestions->map(function ($item) {
            $item = $item->toArray();
            $item['material_length'] = json_decode($item['material_length'], true);
            return $item;
        });
        //filter out material length that is invalid
        return $collection->filter(function ($item) {
            $material_length = $item['material_length'];
            return (is_array($material_length) && count($material_length) > 0);
        });
    }

    /**
     * Sum up all the insertions
     * @todo A lot of these information should be saved in the suggestions table so we don't need to always do this calculation
     */
    protected function getTotalInsertions()
    {   
        $total_insertions = 0;
        foreach ($this->suggestions as $item) {
            $collection = collect($item['material_length'])->flatten(1);
            $total_insertions += $collection->sum(function($item) {
                return (int) $item['exposure'];
            });
        }
        return $total_insertions;
    }

    /**
     * Gross impressions is gotten by doing the following calculation:
     * 1. Sum up the total audience for each suggestions
     * 2. Multiply by the total insertions
     * @todo A lot of these information should be saved in the suggestions table so we don't need to always do this calculation
     */
    protected function getGrossImpressions($total_insertions)
    {   
        $gross_impressions = 0;
        foreach ($this->suggestions as $item) {
            $collection = collect($item['material_length'])->flatten(1);
            $insertions = $collection->sum(function($item) {
                return (float) $item['exposure'];
            });
            $impressions = $insertions * $item['total_audience'];
            $gross_impressions += $impressions;
        }
        return $gross_impressions;
    }

    /**
     * Net media cost is gotten by doing the following calculation:
     * 1. For each station/program/suggestion that was chosen, get the total net cost which is (unit rate * insertions) (take into account discount)
     * 2. Sum up all those
     * 3. That is the net media cost 
     * @todo A lot of these information should be saved in the suggestions table so we don't need to always do this calculation
     */
    protected function getNetMediaCost()
    {
        $net_media_cost = 0;
        foreach ($this->suggestions as $item) {
            $collection = collect($item['material_length'])->flatten(1);
            $net_media_cost += $collection->sum(function($item) {
                return (float) $item['net_total'];
            });
        }
        return $net_media_cost;
    }

    /**
     * Total grps is basically the summation of all the grps for the suggestions
     * There are many ways to calculate this total grp, the way we are doing it now is by 
     * dividing gross impressions by universe size then multiplying by 100
     * @todo Save total universe on the media plan
     * @todo Save grp per chosen program/timebelt on the suggestions row, so that is just summed up, rather than doing it this way
     */
    protected function getTotalGrp($gross_impressions)
    {
        $universe = round(MpsProfile::sum('pop_weight'));
        if ($universe > 0 && $gross_impressions > 0) {
            $total_grp = ($gross_impressions/$universe) * 100;
            return round($total_grp, 2);
        }
        return 0;
    }

    /**
     * Net reach is the number of UNIQUE individuals reached by the programs 
     * (This removes duplication as there could be the same individuals across programs/timebelts)
     * So, this requires recalculating reach for the unique individuals.
     * This is an example query below:
     * select sum(pop_weight)
     * from
     * (
     * select mp.ext_profile_id, mp.pop_weight
     * from mps_profile_activities mpa
     * join mps_profiles mp on mp.ext_profile_id = mpa.ext_profile_id
     * where (mpa.tv_station_id = '5d7cb4c7516dc' and mpa.day = 'Mon' and mpa.start_time = '06:15') or (mpa.tv_station_id = '5d7cb4c75d4f4' and mpa.day = 'Mon' and mpa.start_time = '08:30') or (mpa.tv_station_id = '5d7cb4c76c9ef' and mpa.day = 'Mon' and mpa.start_time = '07:00') group by ext_profile_id
     * ) as tbl;
     * @todo This replicates rating query generation, so find a way to keep them all together
     */
    protected function getNetReach() {
        $tv_stations = TvStation::all()->groupBy('id');
        $targeting_filters = $this->getTargetingFilters();
        $sub_query = MpsProfile::filter($targeting_filters)
            ->select("mps_profiles.ext_profile_id", "mps_profiles.pop_weight")
            ->join('mps_profile_activities', 'mps_profile_activities.ext_profile_id', '=', 'mps_profiles.ext_profile_id')
            ->join('tv_stations', 'mps_profile_activities.tv_station_key', '=', 'tv_stations.key')
            ->groupBy('mps_profiles.ext_profile_id')
            ->where(function($query) use ($tv_stations) {
                foreach ($this->suggestions as $item) {
                    $query->orWhere(function($sub) use ($item, $tv_stations) {
                        $station_key = $tv_stations[$item['station_id']][0]->key;
                        $sub->where('mps_profile_activities.tv_station_key', $station_key)
                            ->where('mps_profile_activities.day', TimeBelt::shortenDay($item['day']))
                            ->where('mps_profile_activities.start_time', $item['start_time']);
                    });
                }
            });
        $query = DB::query()->fromSub($sub_query, 'tbl');
        $raw_sql = Query::getSql($query);
        Log::debug($raw_sql);
        return $query->sum('pop_weight');
    }

    protected function getTargetingFilters()
    {
        $fields = [
            'gender' => 'gender', 
            'social_class' => 'criteria_social_class',
            'region' => 'criteria_region', 
            'state' => 'criteria_state',
            'age_groups' => 'criteria_age_groups'
        ];
        $filters = [];
        foreach ($fields as $key => $model_field) {
           $decoded = json_decode($this->media_plan[$model_field], true);
           if (is_array($decoded) && count($decoded) > 0) {
                $filters[$key] = $decoded;
           }
        }
        return $filters;
    }

}