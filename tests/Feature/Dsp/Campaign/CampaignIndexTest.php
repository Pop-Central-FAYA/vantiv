<?php

namespace Tests\Feature\Dsp\Campaign;

class CampaignIndexTest extends CampaignTestCase
{
    protected $route_name = 'agency.campaign.all';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['view.campaign']);
        return $user;
    }

    protected function getResponse($user)
    {
        return $this->actingAs($user)->get(route($this->route_name));
    }

    public function test_unauthenticated_user_cannot_access_campaign_index_route()
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
                ->assertViewIs('agency.campaigns.index')
                ->assertSeeText('All Campaigns');
    }
}