<?php

namespace Tests\Feature\Dsp\Mpo;

use Vanguard\Libraries\Enum\Dsp\CampaignStatus;
use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class GenerateMpoTest extends MpoTestCase
{
    protected $route_name = 'mpos.store';

    protected $campaign_status = [CampaignStatus::ACTIVE, CampaignStatus::PENDING];

    protected function getResponse($user, $campaign_id, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['mpo_id' => $campaign_id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_mpo_approve_route()
    {
        $campaign_id =uniqid(); 
        $response = $this->postJson(route($this->route_name, ['campaign_id' => $campaign_id]), []);
        $response->assertStatus(401);
    }

    public function test_it_validate_that_ad_vendor_id_is_required()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::PENDING);
        $response = $this->getResponse($user, $mpo->campaign->id, [
            'ad_vendor_id' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ad_vendor_id']);
    }

    public function test_it_validate_that_insertions_is_required()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::PENDING);
        $response = $this->getResponse($user, $mpo->campaign->id, [
            'insertions' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['insertions']);
    }

    public function test_it_validate_that_net_total_is_required()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::PENDING);
        $response = $this->getResponse($user, $mpo->campaign->id, [
            'net_total' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['net_total']);
    }

    public function test_it_validate_that_adslots_is_required()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::PENDING);
        $response = $this->getResponse($user, $mpo->campaign->id, [
            'adslots' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['adslots']);
    }

    public function test_adslots_is_an_array()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::PENDING);
        $response = $this->getResponse($user, $mpo->campaign->id, [
            'adslots' => 'test'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['adslots']);
    }

    public function test_404_is_returned_when_the_mpo_does_not_exist()
    {
        $user = $this->setupAuthUser();
        $campaign_id = uniqid();
        $response = $this->getResponse($user, $campaign_id, $this->mpoData(uniqid()));
        $response->assertStatus(404);
    }

    public function test_it_returns_the_right_status_if_user_does_not_have_the_authorization()
    {
        $user = $this->setupAuthUser();
        $user2 = $this->setupAuthUser();
        $mpo = $this->setupMpo($user);
        $response = $this->getResponse($user2, $mpo->campaign->id, $this->mpoData(uniqid()));
        $response->assertStatus(403);
    }

    public function test_it_authorize_for_campaign_with_statuses_other_than_pending_and_active()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::INVOICED);
        $response = $this->getResponse($user, $mpo->campaign->id, $this->mpoData(uniqid()));
        $response->assertStatus(403);
    }

    public function test_it_creates_an_mpo()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user, CampaignStatus::PENDING);
        $response = $this->getResponse($user, $mpo->campaign->id, $this->mpoData($mpo->vendor->id));
        $response->assertStatus(200);
        $expected = $response->json()['data'][0];
        $this->assertArrayHasKey('links', $expected);
    }

    private function mpoData($vendor_id)
    {
        return [
            'ad_vendor_id' => $vendor_id,
            'insertions' => 5,
            'net_total' => 20000,
            'adslots' => $this->adslotsData()
        ];
    }

    private function adslotsData()
    {
        return [
            [
                'id' => uniqid()
            ]
        ];
    }
}