<?php

namespace Tests\Feature\TimeBeltTransaction;

use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Services\Schedule\AdPatternSchedule;

class AdPatternScheduleTest extends TestCase
{
    public function test_it_can_shcedule_ads()
    {
        $time_belt_data = (object)$this->timeBeltData();
        $time_belt_transaction = factory(TimeBeltTransaction::class)->create([
            'playout_hour' => $time_belt_data->playout_hour,
            'playout_date' => $time_belt_data->playout_date,
            'company_id' => $time_belt_data->broadcaster_id,
            'duration' => 30
        ]);
        dd($time_belt_transaction);
        $ad_schedule = new AdPatternSchedule($time_belt_data, 4, $time_belt_transaction->id);
        dd($ad_schedule->run());
    }

    public function timeBeltData()
    {
        return [
            'playout_date' => '2019-06-03',
            'playout_hour' => '11:00:00',
            'broadcaster_id' => factory(Company::class)->create()->id
        ];
    }
}