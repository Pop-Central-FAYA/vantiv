<?php

namespace Vanguard\Services\MediaPlan;

use Illuminate\Support\Arr;
use DB;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Libraries\TimeBelt;
/**
 * This service is to create a Media Plan (This is only created if there is a rating available).
 */
class StoreMediaPlanSuggestionService implements BaseServiceInterface
{   

    protected $media_plan;
    protected $data;

    public function __construct($data, $media_plan)
    {
        $this->data = $data;
        $this->media_plan = $media_plan;
    }

    /**
     * The suggestions should be an array of objects, save each one individually if it is not saved 
     * already, then delete the ones that are not present in the current list
     */
    public function run()
    {
        DB::transaction(function() {
            $id_list = [];
            foreach ($this->data as $item) {
                $model = $this->storeSuggestion($item);
                $id_list[] = $model->id;
            }
            DB::table('media_plan_suggestions')
                ->where('media_plan_id', $this->media_plan->id)
                ->whereNotIn('id', $id_list)->delete();
        });
        $this->media_plan->refresh();
        return $this->media_plan->suggestions;
    }

    /**
     * @todo there should be no need for certain fields i.e station state, region, station_type, state_counts etc
     */
    protected function storeSuggestion($item)
    {
        $data = [
            'media_plan_id' => $this->media_plan->id,
            'media_type' => $item['media_type'],
            'program' => $item['program'],
            'day' => TimeBelt::lengthenDay($item['day']),
            'start_time' => $item['start_time'],
            'end_time' => $item['end_time'],
            'total_audience' => $item['total_audience'],
            'station_id' => $item['station_id'],
            'rating' => $item['rating'],
            'material_length' => '',
            //these are probably the fields that do not need to be present
            'status' => 1,
            'exposure_calculation' => '',
            'station' => $item['station'],
            'state' => $item['state'],
            'station_type' => $item['station_type'],
            'region' => '',
            'state_counts' => ''
        ];

        if (Arr::has($item, 'id')) {
            return MediaPlanSuggestion::firstOrCreate(['id' => $item['id']], $data);
        }
        return MediaPlanSuggestion::create($data);
    }
}