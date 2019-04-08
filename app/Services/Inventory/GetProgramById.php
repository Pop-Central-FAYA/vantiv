<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;

class GetProgramById
{
    protected $program_id;

    public function __construct($program_id)
    {
        $this->program_id = $program_id;
    }

    public function getProgram()
    {
        return MediaProgram::with(['time_belts', 'rate_card'])->where('id', $this->program_id)->first();
    }

    public function groupTimeBelt()
    {
        return TimeBelt::where('media_program_id', $this->program_id)->groupBy('day', 'actual_time_picked')->get();
    }
}
