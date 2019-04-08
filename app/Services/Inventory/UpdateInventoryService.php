<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;

class UpdateInventoryService extends CreateInventoryService
{
    protected $program_id;

    public function __construct($days, $media_program_name, $company_id, $program_vendor_id, $rate_card_id, $start_date,
                                $end_date, $start_time, $end_time, $program_id)
    {
        parent::__construct($days, $media_program_name, $company_id, $program_vendor_id, $rate_card_id, $start_date,
            $end_date, $start_time, $end_time);
        $this->program_id = $program_id;
    }

    private function deleteTimeBelts()
    {
        $time_belts = TimeBelt::where('media_program_id', $this->program_id)->get();
        foreach ($time_belts as $time_belt){
            $time_belt->actual_time_picked = '';
            $time_belt->media_program_id = '';
            $time_belt->save();
        }
        return $time_belts;
    }

    private function updateMediaProgram()
    {
        $media_program = MediaProgram::find($this->program_id);
        $media_program->name = $this->media_program_name;
        $media_program->rate_card_id = $this->rate_card_id;
        $media_program->start_date = $this->start_date;
        $media_program->end_date = $this->end_date;
        $media_program->save();
        return $media_program;
    }

    public function updateInventory()
    {
        \DB::transaction(function () {
            $this->deleteTimeBelts();
            $this->updateMediaProgram();
            for($i = 0; $i < count($this->start_time); $i++){
                if($this->start_time[$i] != ''){
                    $this->breakTimeBelt($this->start_time[$i], $this->end_time[$i], $this->days[$i], $this->program_id);
                }
            }
        });
        return 'success';
    }
}
