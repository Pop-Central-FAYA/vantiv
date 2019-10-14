<?php

namespace Tests\Feature\Dsp\Reach;

use Tests\TestCase;

class ReachTestCase extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->seed('CriteriasTableSeeder');
        $this->seed('PublisherTableSeeder');
        $this->seed('TvStationTableSeeder');
        $this->seed('MpsTestDataSeeder');
    }

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.media_plan']);
        return $user;
    }

    protected function getResponse($user, $params)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $params));
    }


    protected function setupMediaPlan($user, $override_params=[])
    {
        $override_params['company_id'] = $user->companies->first();
        $override_params['planner_id'] = $user->id;

        $media_plan = factory(\Vanguard\Models\MediaPlan::class)->create($override_params);
        return $media_plan->refresh();
    }

}
