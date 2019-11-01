<?php

namespace Tests\Feature\Dsp\MediaPlan;


class MediaPlanIndexTest extends MediaPlanTestCase
{
    protected $route_name = 'agency.media_plans';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['view.media_plan']);
        return $user;
    }

    protected function getResponse($user)
    {
        return $this->actingAs($user)->get(route($this->route_name));
    }

    public function test_unauthenticated_user_cannot_access_media_plan_index_route()
    {
        $response = $this->get(route($this->route_name));
        //it should redirect to login
        $response->assertStatus(302)->assertLocation('login');
    }

    public function test_user_without_read_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(403);
    }

    public function test_user_with_read_permissions_loads_up_index_page()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $response->assertStatus(200)
                ->assertViewIs('agency.mediaPlan.index')
                ->assertSeeText('Media Plans');
    }
}