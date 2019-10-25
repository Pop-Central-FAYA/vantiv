<?php

namespace Vanguard\Services\Program;

use Vanguard\Models\MediaPlanProgram;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Services\Traits\SplitTimeRange;

use function GuzzleHttp\json_encode;

class StoreService implements BaseServiceInterface
{
    use SplitTimeRange;

    protected $data;
    protected $station_id;

    public function __construct($data, $station_id)
    {
        $this->data = $data;
        $this->station_id = $station_id;
    }

    public function run()
    {
        $program = new MediaPlanProgram();
        $program->program_name = $this->data['program_name'];
        $program->station_id = $this->station_id;
        $program->attributes = json_encode($this->formatAttribute());
        $program->save();
        return $program;
    }

    protected function formatAttribute()
    {
        $attributes = [];
        foreach ($this->data['days'] as $key => $value) {
            $start_time = $this->data['start_time'][$key];
            $end_time = $this->data['end_time'][$key];
            $attributes[] = [
                'day' => $value,
                'rates' => $this->setUpRatings(),
                'program_time' => $start_time.'-'.$end_time,
                'time_belts' => $this->splitTimeRangeByBase($start_time, $end_time, null)
            ];
        }
        return $attributes;
    }

    protected function setUpRatings()
    {
        return collect($this->data['durations'])->mapWithKeys(function($item, $key) {
            return [$item => $this->data['rates'][$key]]; 
        });
    }
}