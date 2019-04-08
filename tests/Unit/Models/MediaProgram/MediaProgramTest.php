<?php

namespace Tests\Unit\Models\MediaProgram;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;

class MediaProgramTest extends TestCase
{
    public function test_it_can_have_many_time_belts()
    {
        factory(MediaProgram::class)->create()->save([
            $time_belts = factory(TimeBelt::class,2)->create()
        ]);

        $this->assertCount(2, $time_belts);
    }

    public function test_it_can_have_many_time_belt_transactions()
    {
        factory(MediaProgram::class)->create()->save([
            $time_belt_transactions = factory(TimeBeltTransaction::class,2)->create()
        ]);

        $this->assertCount(2, $time_belt_transactions);
    }
}
