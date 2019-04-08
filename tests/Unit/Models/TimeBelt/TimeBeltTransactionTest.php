<?php

namespace Tests\Unit\Models\TimeBelt;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;

class TimeBeltTransactionTest extends TestCase
{
    public function test_it_can_belong_to_a_time_belt()
    {
        $time_belt_transaction = factory(TimeBeltTransaction::class)->create();

        $this->assertInstanceOf(TimeBelt::class, $time_belt_transaction->time_belt);
    }
}
