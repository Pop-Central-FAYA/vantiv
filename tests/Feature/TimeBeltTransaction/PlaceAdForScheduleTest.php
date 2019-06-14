<?php

namespace Tests\Feature\TimeBeltTransaction;

use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Services\Schedule\PlaceAdForSchedule;

class PlaceAdForScheduleTest extends TestCase
{
    public function test_it_can_place_the_ads_for_scheduling()
    {
        $program = factory(MediaProgram::class)->create();
        $this->createTimeBelt($program);
        $time_belt_data = (object)$this->preselectedTimeBeltData($program->id);
        $time_belt_transaction = factory(TimeBeltTransaction::class)->create([
            'playout_date' => $time_belt_data->playout_date,
            'company_id' => $time_belt_data->company_id,
            'duration' => $time_belt_data->duration,
            'media_program_id' => $program->id,
            'playout_hour' => '11:15:00'
        ]);

        $place_ad_service = new PlaceAdForSchedule(4, $time_belt_transaction->id, $time_belt_data);
        $this->assertEquals(1, $place_ad_service->run()->order);
    }

    public function test_it_can_schedule_an_ad_with_existing_ad_in_thesame_playout_hour()
    {
        $program = factory(MediaProgram::class)->create();
        $this->createTimeBelt($program);
        $time_belt_data = (object)$this->preselectedTimeBeltData($program->id);
        $time_belt_transaction = factory(TimeBeltTransaction::class)->create([
            'playout_date' => $time_belt_data->playout_date,
            'company_id' => $time_belt_data->company_id,
            'duration' => $time_belt_data->duration,
            'media_program_id' => $program->id,
            'playout_hour' => '11:00:00'
        ]);

        $place_ad_service = new PlaceAdForSchedule(4, $time_belt_transaction->id, $time_belt_data);
        $this->assertEquals(2, $place_ad_service->run()->order);
    }

    public function test_it_can_schedule_ads_in_other_breaks()
    {
        $program = factory(MediaProgram::class)->create();
        $this->createTimeBelt($program);
        $time_belt_data = (object)$this->preselectedTimeBeltData($program->id);
        factory(TimeBeltTransaction::class)->create([
            'playout_date' => $time_belt_data->playout_date,
            'company_id' => $time_belt_data->company_id,
            'duration' => 180,
            'media_program_id' => $program->id,
            'playout_hour' => '11:00:00'
        ]);

        $time_belt_transaction = factory(TimeBeltTransaction::class)->create([
            'playout_date' => $time_belt_data->playout_date,
            'company_id' => $time_belt_data->company_id,
            'duration' => 30,
            'media_program_id' => $program->id,
            'playout_hour' => '11:15:00'
        ]);

        $place_ad_service = new PlaceAdForSchedule(4, $time_belt_transaction->id, $time_belt_data);
        $this->assertEquals('11:15:00', $place_ad_service->run()->playout_hour);
    }

    public function test_it_returns_error_if_it_cannot_fit_the_hours_of_program()
    {
        $program = factory(MediaProgram::class)->create();
        $this->createTimeBelt($program);
        $time_belt_data = (object)$this->preselectedTimeBeltData($program->id);
        foreach ($this->createTimeBelt($program) as $time_belt){
            factory(TimeBeltTransaction::class)->create([
                'time_belt_id' => $time_belt['id'],
                'playout_date' => $time_belt_data->playout_date,
                'company_id' => $time_belt_data->company_id,
                'duration' => 180,
                'media_program_id' => $program->id,
                'playout_hour' => $time_belt['start_time']
            ]);
        }

        $time_belt_transaction = factory(TimeBeltTransaction::class)->create([
            'playout_date' => $time_belt_data->playout_date,
            'company_id' => $time_belt_data->company_id,
            'duration' => 30,
            'media_program_id' => $program->id,
        ]);

        $place_ad_service = new PlaceAdForSchedule(4, $time_belt_transaction->id, $time_belt_data);
        $this->assertNull($place_ad_service->run());
    }

    public function preselectedTimeBeltData($program_id)
    {
        return [
            'playout_date' => '2019-06-03',
            'company_id' => factory(Company::class)->create()->id,
            'duration' => 30,
            'media_program_id' => $program_id
        ];
    }

    public function createTimeBelt($program)
    {
        $time_belt_array = [];
        foreach ($this->timeBeltData($program) as $time_belt){
            $time_belt_array[] = factory(TimeBelt::class)->create([
                'media_program_id' => $program->id,
                'start_time' => $time_belt['start_time'],
                'end_time' => $time_belt['end_time'],
                'day' => 'Monday'
            ]);
        }
        return $time_belt_array;
    }

    public function timeBeltData($program)
    {
        return [
            [
                'start_time' => '11:00:00',
                'end_time' => '11:15:00'
            ],
            [
                'start_time' => '11:15:00',
                'end_time' => '11:30:00'
            ],
            [
                'start_time' => '11:30:00',
                'end_time' => '11:45:00'
            ],
            [
                'start_time' => '11:45:00',
                'end_time' => '12:00:00'
            ],
        ];
    }
}