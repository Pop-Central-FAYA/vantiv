<?php

namespace Tests\Feature\Dsp\Campaign;

use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class AssignFollowerTest extends CampaignTestCase
{

    protected $route_name = 'campaign.assign_follower';

    protected function setUpUserWithSameCompany()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);
        return $this->setupAuthUser($company);
    }

    protected function getResponse($user, $campaign_id, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['campaign_id' => $campaign_id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_assign_follower_route()
    {
        $campaign_id = uniqid();
        $response = $this->postJson(route($this->route_name, ['campaign_id' => $campaign_id]), []);
        $response->assertStatus(401);
    }

    public function test_it_validate_user_id_is_required()
    {
        $user = $this->setupAuthUser();
        $campaign_id = uniqid();
        $response = $this->getResponse($user, $campaign_id, [
            'user_id' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);
    }

    public function test_it_validate_user_id_is_an_array()
    {
        $user = $this->setupAuthUser();
        $campaign_id = uniqid();
        $response = $this->getResponse($user, $campaign_id, [
            'user_id' => 'jhdfcsjh'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);
    }

    public function test_it_returns_404_when_trying_to_assign_follower_to_campaign_that_doesnt_exist()
    {
        $user = $this->setupAuthUser();
        $campaign_id = uniqid();
        $response = $this->getResponse($user, $campaign_id, $this->requestData());
        $response->assertStatus(404);
    }

    public function test_user_has_the_right_access_to_assign_followers()
    {
        $user = $this->setUpUserWithSameCompany();
        $another_user = $this->setUpUserWithSameCompany();
        $campaign = $this->setUpCampaign($another_user);
        $response = $this->getResponse($user, $campaign->id, $this->requestData());
        $response->assertStatus(403);
    }

    public function test_it_can_assign_assign_follower_to_a_campaign()
    {
        $user = $this->setUpUserWithSameCompany();
        $campaign = $this->setUpCampaign($user);
        $response = $this->getResponse($user, $campaign->id, $this->requestData());
        $response->assertStatus(200);
    }

    public function test_it_persist_the_followers_into_the_database()
    {
        $user = $this->setUpUserWithSameCompany();
        $campaign = $this->setUpCampaign($user);
        $this->getResponse($user, $campaign->id, $this->requestData());
        $this->assertDatabaseHas('followers', [
            'follower_id' => $this->requestData()['user_id'][0]
        ]);
        $this->assertDatabaseHas('followers', [
            'follower_id' => $this->requestData()['user_id'][1]
        ]);
    }

    private function requestData()
    {
        $user1 = $this->setUpUserWithSameCompany();
        $user2 = $this->setUpUserWithSameCompany();
        return [
            'user_id' => [$user1->id, $user2->id]
        ];
    }
}