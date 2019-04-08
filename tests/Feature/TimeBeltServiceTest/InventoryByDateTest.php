<?php

namespace Tests\Feature\TimeBeltServiceTest;

use Tests\TestCase;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Services\Inventory\TimeBeltByDate;

class InventoryByDateTest extends TestCase
{
    public function test_it_can_the_get_time_belt_for_the_day()
    {
        $date = '01-04-2019';
        $time_belt = $this->createTimeBelt();
        $time_belt_by_date_service = new TimeBeltByDate($date);
        $inventory_by_date = $time_belt_by_date_service->getTimeBeltForTheDay();
        $this->assertEquals($time_belt['time_belt']->id, $inventory_by_date[0]->id);
    }

    public function test_it_can_get_available_slot_for_time_belt_which_has_slot_sold()
    {
        $date = '2019-04-01';
        $time_belt = $this->createTimeBelt();
        factory(TimeBeltTransaction::class)->create($this->timeBeltTransactionData($time_belt['time_belt']->id,$time_belt['media_program']->id, $date, 70));
        $inventory_by_date_service = new TimeBeltByDate($date);
        $time_belt_inventory = $inventory_by_date_service->timeBeltInventory();
        $this->assertEquals(110, (integer)$time_belt_inventory[0]['total_slot_available']);
    }

    public function test_it_can_get_available_slot_for_programs_which_has_not_sold_any_slot()
    {
        $date = '2019-04-01';
        $this->createTimeBelt();
        $inventory_by_date_service = new TimeBeltByDate($date);
        $program_inventory = $inventory_by_date_service->timeBeltInventory();

        $this->assertEquals(180, (integer)$program_inventory[0]['total_slot_available']);
    }

    public function test_it_can_add_up_inventory_for_same_program_and_date()
    {
        $date = '2019-04-01';
        $time_belt = $this->createTimeBelt();

        factory(TimeBeltTransaction::class)->create($this->timeBeltTransactionData($time_belt['time_belt']->id,$time_belt['media_program']->id, $date, 70));

        factory(TimeBeltTransaction::class)->create($this->timeBeltTransactionData($time_belt['time_belt']->id,$time_belt['media_program']->id, $date, 20));

        $inventory_by_date_service = new TimeBeltByDate($date);
        $program_inventory = $inventory_by_date_service->timeBeltInventory();

        $this->assertEquals(90, (integer)$program_inventory[0]['total_slot_available']);
    }

    public function createTimeBelt()
    {
        $media_program = factory(MediaProgram::class)->create();
        $time_belt =  factory(TimeBelt::class)->create([
                        'day' => 'Monday',
                        'media_program_id' => $media_program->id,
                        'start_time' => '13:30:00',
                        'end_time' => '13:45:00'
                    ]);
        return ['time_belt' => $time_belt, 'media_program' => $media_program];
    }

    public function timeBeltTransactionData($time_belt_id, $media_program_id, $date, $duration)
    {
        return [
            'time_belt_id' => $time_belt_id,
            'media_program_id' => $media_program_id,
            'playout_date' => $date,
            'duration' => $duration,
        ];
    }
}
