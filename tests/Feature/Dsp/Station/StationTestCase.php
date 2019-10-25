<?php

namespace Tests\Feature\Dsp\Station;

use Tests\TestCase;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Models\TvStation;

class StationTestCase extends TestCase
{
    protected function setupStation($user, $publisher_id = 'hgffghvbv')
    {
        $station = factory(TvStation::class)->create([
            'publisher_id' => $publisher_id
        ]);
        $this->setupProgram($station->id);
        return $station->refresh();
    }

    protected function setupProgram($station_id)
    {
        $program = factory(MediaPlanProgram::class)->create([
            'station_id' => $station_id
        ]);
        return $program->refresh();
    }
}
