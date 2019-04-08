<?php

namespace Tests\Feature\TimeBeltServiceTest;

use Carbon\Carbon;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Services\Inventory\TimeBeltByDateRange;

class TimeBeltServiceByDateRangeTest extends TestCase
{
    public function test_it_can_get_list_of_dates_when_given_a_start_and_end_date()
    {
        $start_date = '2019-03-26';
        $end_date = '2019-04-05';
        $inventory_by_date_range_service = new TimeBeltByDateRange($start_date, $end_date);
        $date_list = $inventory_by_date_range_service->getListOfDates();

        $this->assertEquals(Carbon::parse('2019-03-28'), $date_list[2]);
    }

    public function test_it_can_return_time_belt_inventories_when_supplied_with_date_range()
    {
        $start_date = '2019-03-26';
        $end_date = '2019-04-05';
        $time_belt = $this->createTimeBelt();
        $inventory_by_date_range_service = new TimeBeltByDateRange($start_date, $end_date);
        $time_belt_inventory = $inventory_by_date_range_service->getProgramInventory();
        $this->assertContains($time_belt[0]->program_name, $time_belt_inventory);
    }

    public function test_it_can_return_program_inventory_for_some_sold_program_slot()
    {
        $start_date = '2019-03-24';
        $end_date = '2019-04-05';
        $this->createSingleTimeBelt();
        $inventory_by_date_range_service = new TimeBeltByDateRange($start_date, $end_date);
        $program_inventory = $inventory_by_date_range_service->getProgramInventory();
        $this->assertContains(110, $program_inventory);

    }

    public function createTimeBelt()
    {
        return factory(TimeBelt::class, 2)->create([
            'day' => 'Friday',
            'start_time' => '02:00:00',
            'end_time' => '02:15:00'
        ]);

    }

    public function createSingleTimeBelt()
    {
        $time_belt_id = 'hnfmcbdxhgg';
        $media_program = factory(MediaProgram::class)->create();
        return factory(TimeBelt::class)->create([
            'id' => $time_belt_id,
            'media_program_id' => $media_program->id,
            'station_id' => factory(Company::class)->create()->id,
            'day' => 'Friday',
            'start_time' => '02:00:00',
            'end_time' => '02:15:00'
            ])->save([
                 factory(TimeBeltTransaction::class)->create([
                     'time_belt_id' => $time_belt_id,
                    'media_program_id' => $media_program->id,
                    'duration' => 70,
                    'playout_date' => '2019-03-29',
                ])
            ]);
    }

}
