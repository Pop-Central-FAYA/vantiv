<?php

namespace Vanguard\Services\Mpo;

use Illuminate\Support\Facades\DB;

class DeleteMpoTimeBelt
{
    protected $program;
    protected $duration;
    protected $playout_date;

    public function __construct($program, $duration, $playout_date)
    {
        $this->program = $program;
        $this->duration = $duration;
        $this->playout_date = $playout_date;
    }

    public function run()
    {
        return DB::table('campaign_mpo_time_belts')->where([
            ['program', $this->program],
            ['duration', $this->duration],
            ['playout_date', $this->playout_date]
        ])->delete();
    }
}

