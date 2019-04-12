<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\MediaPlanProgram;
use Vanguard\Models\MediaPlanProgramRating;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Models\MediaPlanSuggestionRating;
use Vanguard\Services\Traits\SplitTimeRange;

class StoreMediaPlanProgram
{
    use SplitTimeRange;
    protected $days;
    protected $media_program_name;
    protected $station;
    protected $start_time;
    protected $end_time;
    protected $unit_rate;
    protected $duration;

    public function __construct($days, $media_program_name, $station, $start_time, $end_time, $unit_rate, $duration)
    {
        $this->days = $days;
        $this->media_program_name = $media_program_name;
        $this->station = $station;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->unit_rate = $unit_rate;
        $this->duration = $duration;
    }

    public function storeMediaPlanProgram()
    {
        \DB::transaction(function () {
            for($i = 0; $i < count($this->start_time); $i++){
                if($this->start_time[$i] != '' && ($this->start_time[$i] != '00 : 00' && $this->start_time[$i] != '00 : 00')){
                    $this->breakTimeBelt($this->start_time[$i], $this->end_time[$i], $this->days[$i], $this->media_program_name);
                }
            }
            $this->storeMediaPlanProgramRating();
        });
        $media_programs = MediaPlanProgram::where('station', $this->station)->get();
        $rating = MediaPlanProgramRating::where([
                                                    ['station', $this->station],
                                                    ['program_name', $this->media_program_name]
                                                ])->get();
        return ['media_programs' => $media_programs, 'ratings' => $rating];
    }

    private function updateMediaPlanSuggestion($start_time, $day)
    {
        $start_time = $start_time.':00';
        $media_plan_suggestions = MediaPlanSuggestion::where([
                                                            ['station', $this->station],
                                                            ['day', ucfirst($day)],
                                                            ['status', 1]
                                                        ])
                                                    ->whereTime('start_time', $start_time)
                                                    ->get();
        foreach ($media_plan_suggestions as $media_plan_suggestion){
            $media_plan_suggestion->program = $this->media_program_name;
            $media_plan_suggestion->save();
        }
    }

    private function deleteIfExist($day, $start_time)
    {
        MediaPlanProgram::where([
            ['day', $day],
            ['start_time', $start_time],
            ['station', $this->station]
        ])->delete();
    }

    private function removeProgramFromMediaPlanSuggestion($start_time, $day)
    {
        $start_time = $start_time.':00';
        $media_plan_suggestions = MediaPlanSuggestion::where([
                ['station', $this->station],
                ['day', ucfirst($day)],
                ['status', 1]
            ])
            ->whereTime('start_time', $start_time)
            ->get();
        foreach ($media_plan_suggestions as $media_plan_suggestion){
            $media_plan_suggestion->program = 'Unknown Program';
            $media_plan_suggestion->save();
        }
    }

    private function removeMediaPlanProgramRating($day, $start_time)
    {
        //initial_program_name
        $media_plan_program = MediaPlanProgram::where([
                                ['day', $day],
                                ['start_time', $start_time],
                                ['station', $this->station]
                            ])->first();
        if($media_plan_program){
            MediaPlanProgramRating::where([
                ['program_name', $media_plan_program->program_name],
                ['station', $this->station]
            ])->delete();
        }
    }

    private function breakTimeBelt($start_time, $end_time, $day, $program_name)
    {
        $time_belts = $this->splitTimeRangeByBase($start_time, $end_time, null);
        foreach ($time_belts as $time_belt){
            $this->removeMediaPlanProgramRating($day, $time_belt['start_time']);
            $this->deleteIfExist($day, $time_belt['start_time']);
            $this->removeProgramFromMediaPlanSuggestion($time_belt['start_time'], $day);
            $this->updateMediaPlanSuggestion($time_belt['start_time'], $day);
            $media_plan_program = new MediaPlanProgram();
            $media_plan_program->program_name = $program_name;
            $media_plan_program->start_time = $time_belt['start_time'];
            $media_plan_program->end_time = $time_belt['end_time'];
            $media_plan_program->station = $this->station;
            $media_plan_program->day = ucfirst($day);
            $media_plan_program->actual_time_slot = $start_time.'-'.$end_time;
            $media_plan_program->save();
        }
    }

    private function storeMediaPlanProgramRating()
    {
        for($j = 0; $j < count($this->duration); $j++){
            if($this->unit_rate[$j] != ''){
                $media_plan_program_rating = new MediaPlanProgramRating();
                $media_plan_program_rating->program_name = $this->media_program_name;
                $media_plan_program_rating->duration = $this->duration[$j];
                $media_plan_program_rating->price = $this->unit_rate[$j];
                $media_plan_program_rating->station = $this->station;
                $media_plan_program_rating->save();
            }
        }
    }

}
