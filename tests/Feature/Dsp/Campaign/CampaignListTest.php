<?php

namespace Tests\Feature\Dsp\Campaign;

use Illuminate\Support\Arr;
use Vanguard\Models\Company;
use Vanguard\Models\CompanyType;
use Vanguard\Models\ParentCompany;

class CampaignListTest extends CampaignTestCase
{
    protected $route_name = 'campaigns.list';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['view.campaign']);
        return $user;
    }

    protected function getResponse($user)
    {
        return $this->actingAs($user)->getJson(route($this->route_name));
    }

    public function test_unauthenticated_user_cannot_access_campaign_list_route()
    {
        $response = $this->getJson(route($this->route_name));
        $response->assertStatus(401);
    }

    public function test_user_without_read_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(403);
    }

    public function test_empty_array_returned_if_no_campaign_exist_that_the_user_has_access_to()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $response->assertStatus(200)
                ->assertExactJson(['data' => []]);
    }

    public function test_campaign_list_is_always_limited_to_campaigns_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $campaign_one = $this->setUpCampaign($user);
        $campaign_two = $this->setUpCampaign($user);

        //different company and user
        $different_user = $this->setupUserWithPermissions();
        $campaign_three = $this->setUpCampaign($different_user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = Arr::sort([$campaign_one->id, $campaign_two->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_campaign_list_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupUserWithPermissions();
        $campaign = $this->setUpCampaign($user);
        $response = $this->getResponse($user);
        $response->assertStatus(200);
        $expected = [
            'links' => [
                'mpos' => route('mpos.list', ['campaign_id' => $campaign->id], false)
            ]
        ];
        $actual = $response->json()['data'][0];
        $this->assertArraySubset($expected, $actual);
    }

    public function test_only_list_of_campaigns_a_user_is_following_is_returned()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id,
            'company_type_id' => factory(CompanyType::class)->create()->id
        ]);

        $user = $this->setupUserWithPermissions($company);
        $user_two = $this->setupUserWithPermissions($company);
        $first_campaign = $this->setUpCampaign($user);
        $second_campaign = $this->setUpCampaign($user);
        $third_campaign = $this->setUpCampaign($user);
    
        //assign a follower to first and third campaign
        $first_campaign->addFollower($user_two);
        $third_campaign->addFollower($user_two);

        $response = $this->getResponse($user_two);
        $expected = $response->json()['data'];
        $this->assertCount(2, $expected);
    }

    public function test_empty_campaign_list_is_returned_if_user_doea_not_have_access_to_campaigns()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id,
            'company_type_id' => factory(CompanyType::class)->create()->id
        ]);

        $user = $this->setupUserWithPermissions($company);
        $user_two = $this->setupUserWithPermissions($company);
        $first_campaign = $this->setUpCampaign($user);
        $second_campaign = $this->setUpCampaign($user);
        $third_campaign = $this->setUpCampaign($user);

        $response = $this->getResponse($user_two);
        $response->assertStatus(200)
                ->assertExactJson(['data' => []]);
    }

    public function test_it_returns_all_campaign_for_user_with_admin_role()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id,
            'company_type_id' => factory(CompanyType::class)->create()->id
        ]);

        $user = $this->setupUserWithPermissions($company);
        $user_with_admin = $this->setupUserWithPermissions($company);
        $user_with_admin->assignRole(['name' => 'dsp.admin']);
        $first_campaign = $this->setUpCampaign($user);
        $second_campaign = $this->setUpCampaign($user);
        $third_campaign = $this->setUpCampaign($user);

        $response = $this->getResponse($user_with_admin);
        $expected = $response->json()['data'];
        $this->assertCount(3, $expected);
    }

}