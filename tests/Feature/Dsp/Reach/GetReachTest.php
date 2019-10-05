<?php

namespace Tests\Feature\Dsp\Reach;

use Tests\TestCase;

class GetReachTest extends TestCase
{
    protected $route_name = 'reach.get';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.media-plan']);
        return $user;
    }

    protected function getResponse($user, $params)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $params));
    }

    public function test_unauthenticated_user_cannot_access_reach_get_route()
    {
        $this->markTestSkipped("skipped");
    }

    public function test_403_returned_if_attempting_to_get_reach_for_plan_user_does_not_have_access_to()
    {
        $this->markTestSkipped("skipped");
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $this->markTestSkipped("skipped");
    }

    public function test_attempting_to_get_reach_for_non_existent_plan_returns_404()
    {
        $this->markTestSkipped("skipped");
    }

    public function test_attempting_to_get_reach_with_invalid_parameters_returns_422()
    {
        $this->markTestSkipped("skipped");
    }

    public function test_rated_stations_for_media_plans_successfully_returned()
    {
        $this->markTestSkipped("skipped");
    }
}