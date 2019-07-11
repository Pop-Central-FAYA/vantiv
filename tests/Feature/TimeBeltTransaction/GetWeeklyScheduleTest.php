<?php

namespace Tests\Feature\TimeBeltTransaction;

use Tests\TestCase;
use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Models\Company;
use Vanguard\Models\CampaignDetail;
use Vanguard\Models\WalkIns;
use Vanguard\Services\Schedule\GetWeeklySchedule;
use Vanguard\Models\Publisher;

class GetWeeklyScheduleTest extends TestCase
{
    public function test_it_can_fetch_data_to_schedule_for_a_week()
    {
        $company_id = factory(Company::class)->create()->id;
        factory(Publisher::class)->create([
            'company_id' => $company_id
        ]); 
        $this->createTimeBeltTransaction($company_id);
        $weekly_schedule = (new GetWeeklySchedule('2019-06-10', '2019-06-16', $company_id))->run();
        $this->assertArrayHasKey('playout_date', $weekly_schedule[0]);
    }

    protected function createTimeBeltTransaction($company_id)
    {
        $times = ['09:00:00', '09:20:00', '11:00:00'];
        $time_belt_transaction = [];
        foreach($times as $time){
            $time_belt_transaction[] = [
                    factory(TimeBeltTransaction::class)->create([
                        'playout_hour' => $time,
                        'company_id' => $company_id,
                        'playout_date' => '2019-06-14',
                        'campaign_details_id' => factory(CampaignDetail::class)->create([
                            'walkins_id' => factory(Walkins::class)->create()->id,
                        ])
                    ])
                ];
        }
        return $time_belt_transaction;
    }
}