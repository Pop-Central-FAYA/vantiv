<?php

namespace Tests\Unit\Models\TimeBelt;

use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;

class TimeBeltTest extends TestCase
{
    public function test_it_can_belongs_to_a_company()
    {
        $program = factory(TimeBelt::class)->create();

        $this->assertInstanceOf(Company::class, $program->company);
    }

    public function test_it_can_have_many_time_belt_transactions()
    {
        factory(TimeBelt::class)->create()->save([
            $inventory_log = factory(TimeBeltTransaction::class, 4)->create()
        ]);

        $this->assertCount(4, $inventory_log);
    }
}
