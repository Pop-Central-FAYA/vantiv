<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Libraries\Enum\ProgramStatus;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;
use Vanguard\Services\Traits\SplitTimeRange;

class CreateInventoryService
{
    use SplitTimeRange;
    protected $days;
    protected $media_program_name;
    protected $company_id;
    protected $program_vendor_id;
    protected $rate_card_id;
    protected $start_date;
    protected $end_date;
    protected $start_time;
    protected $end_time;

    public function __construct($days, $media_program_name, $company_id,
                                $program_vendor_id, $rate_card_id, $start_date, $end_date, $start_time, $end_time)
    {
        $this->days = $days;
        $this->media_program_name = $media_program_name;
        $this->company_id = $company_id;
        $this->program_vendor_id = $program_vendor_id;
        $this->rate_card_id = $rate_card_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    private function createMediaProgram()
    {
        $media_program = new MediaProgram();
        $media_program->name = $this->media_program_name;
        $media_program->program_vendor_id = $this->program_vendor_id;
        $media_program->rate_card_id = $this->rate_card_id;
        $media_program->start_date = $this->start_date;
        $media_program->end_date = $this->end_date;
        $media_program->slug = str_slug($this->media_program_name);
        $media_program->status = ProgramStatus::ACTIVE;
        $media_program->company_id = $this->company_id;
        $media_program->save();
        return $media_program;
    }

    public function createTimeBelt()
    {
        \DB::transaction(function () {
            $media_program = $this->createMediaProgram();
            for($i = 0; $i < count($this->start_time); $i++){
                if($this->start_time[$i] != ''){
                    $this->breakTimeBelt($this->start_time[$i], $this->end_time[$i], $this->days[$i], $media_program->id);
                }
            }
        });
        return 'success';
    }

    public function breakTimeBelt($start_time, $end_time, $day, $program_id)
    {
         $time_belts = $this->splitTimeRangeByBase($start_time, $end_time, null);
         foreach ($time_belts as $time_belt){
             $get_time_belt_service = new GetSIngleTimeBelt($time_belt['start_time'], $day, $this->company_id);
             $store_time_belt = $get_time_belt_service->getTimeBelt();
             $store_time_belt->actual_time_picked = $start_time.'-'.$end_time;
             $store_time_belt->media_program_id = $program_id;
             $store_time_belt->save();
         }
    }
}
