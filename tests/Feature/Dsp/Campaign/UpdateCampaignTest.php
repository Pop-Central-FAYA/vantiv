<?php

namespace Tests\Feature\Dsp\Campaign;

class UpdateCampaignTest extends CampaignMpoTest
{
    protected $route_name = 'update.campaign_mpo';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.campaign']);
        return $user;
    }

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_campaign_mpo_update_route()
    {
        $mpo_id = uniqid();
        $response = $this->patchJson(route($this->route_name, ['id' => $mpo_id]), []);
        $response->assertStatus(401);
    }

    public function test_invalid_data_is_validated_on_update_of_campaign_mpo()
    {
        $mpo_time_belt_data = [
            'program' => '',
            'playout_date' => '',
            'asset_id' => '',
            'unit_rate' => '',
            'time_belt' => '',
            'insertion' => '',
            'volume_discount' => ''
        ];

        $user = $this->setupUserWithPermissions();
        $mpo_id = uniqid();
        
        $response = $this->getResponse($user, $mpo_id, $mpo_time_belt_data);

        $response->assertStatus(422);
    }

    public function test_user_without_the_right_permissions_cannot_access_campaign_mpo_update_route()
    {
        $user = $this->setupAuthUser();
        $mpo_id = uniqid();
        $response = $this->getResponse($user, $mpo_id, []);

        $response->assertStatus(403);
    }

    public function test_it_updates_campaign_mpo()
    {
        $user = $this->setupUserWithPermissions();
        $campaign = $this->setUpCampaign($user);
        $mpo = $campaign->campaign_mpos[0];
        $time_belt = $mpo->campaign_mpo_time_belts[0];
        $response = $this->getResponse($user, $mpo->id, $this->getRequestData($time_belt->id));
        $response->assertStatus(200);
    }

    public function test_attempting_to_update_non_existent_mpo_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $mpo_id = uniqid();
        $response = $this->getResponse($user, $mpo_id, ['volume_discount' => 20]);
        $response->assertStatus(404);
    }

    public function test_it_updates_with_the_right_calculations()
    {
        $user = $this->setupUserWithPermissions();
        $campaign = $this->setUpCampaign($user);
        $mpo = $campaign->campaign_mpos[0];
        $time_belt = $mpo->campaign_mpo_time_belts[0];
        $request_data = $this->getRequestData($time_belt->id);
        $response = $this->getResponse($user, $mpo->id, $request_data);
        $response->assertStatus(200);
        $data = $response->json()['data'];
        $actual = $this->calculateNetTotal($request_data['insertion'], $request_data['unit_rate'], $request_data['volume_discount']);
        $expected = $data['campaign']['budget'];
        $this->assertEquals($actual, $expected);
    }

    public function test_it_updates_discount_for_mass_adslot()
    {
        $user = $this->setupUserWithPermissions();
        $campaign = $this->setUpCampaign($user);
        $mpo = $campaign->campaign_mpos[0];
        $response = $this->getResponse($user, $mpo->id, ['volume_discount' => 20]);
        $response->assertStatus(200);
        $data = $response->json()['data'];
        $this->assertEquals($data['campaign']['campaign_mpos'][0]['campaign_mpo_time_belts'][0]['volume_discount'], 20);
    }

    private function getRequestData($time_belt_id)
    {
        return [
            'id' => $time_belt_id,
            'program' => 'Super',
            'playout_date' => '2019-10-5',
            'asset_id' => uniqid(),
            'unit_rate' => 2500,
            'time_belt' => '01:00:00',
            'insertion' => 4,
            'volume_discount' => 5,
            'duration' => 15
        ];
    }

    private function calculateNetTotal($insertion, $unit_rate, $discount)
    {
        $gross = $insertion * $unit_rate;
        return $gross - (($discount/100)*$gross);
    }
}