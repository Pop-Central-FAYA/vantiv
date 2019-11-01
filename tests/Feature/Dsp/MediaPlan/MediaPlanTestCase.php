<?php

namespace Tests\Feature\Dsp\MediaPlan;

use Tests\TestCase;

class MediaPlanTestCase extends TestCase
{

    protected function setupMediaPlan($user)
    {
        $mediaPlan = factory(\Vanguard\Models\MediaPlan::class)->create([
            'company_id' => $user->companies->first(),
            'planner_id' => $user->id
        ]);
        $mediaPlan->addFollower($user);
        return $mediaPlan->refresh();
    }

}
