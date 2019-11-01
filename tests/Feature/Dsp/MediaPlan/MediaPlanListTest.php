<?php

namespace Tests\Feature\Dsp\MediaPlan;

use Illuminate\Support\Arr;
use Vanguard\Models\Company;
use Vanguard\Models\CompanyType;
use Vanguard\Models\ParentCompany;

class MediaPlanListTest extends MediaPlanTestCase
{
    protected $route_name = 'media_plans.list';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['view.media_plan']);
        return $user;
    }

    protected function getResponse($user)
    {
        return $this->actingAs($user)->getJson(route($this->route_name));
    }

    public function test_unauthenticated_user_cannot_access_media_plan_list_route()
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

    public function test_empty_array_returned_if_no_media_plan_exist_that_the_user_has_access_to()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $expected = json_decode($response->getContent(), true);
        $response->assertStatus(200);
        $this->assertCount(0, $expected['mediaPlans']);
    }

    public function test_media_plan_list_is_always_limited_to_media_plans_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $plan_one = $this->setupMediaPlan($user);
        $plan_two = $this->setupMediaPlan($user);

        //different company and user
        $different_user = $this->setupUserWithPermissions();
        $campaign_three = $this->setupMediaPlan($different_user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);
        $actual = json_decode($response->getContent(), true);
        $expected = Arr::sort([$plan_one->id, $plan_two->id]);
        $actual = Arr::pluck($actual['mediaPlans'], 'id');
        $actual = Arr::sort($actual);

        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_media_plan_list_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupUserWithPermissions();
        $plan = $this->setupMediaPlan($user);
        $response = $this->getResponse($user);
        $response->assertStatus(200);
        $expected = [
            'routes' => [
                'summary' => [
                    'index' => route('agency.media_plan.summary', ['id' => $plan->id], false)
                ]
            ]
        ];
        $actual = json_decode($response->getContent(), true);
        $this->assertArraySubset($expected, $actual['mediaPlans'][0]);
    }

    public function test_only_list_of_media_plans_user_is_following_is_returned()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id,
            'company_type_id' => factory(CompanyType::class)->create()->id
        ]);

        $user = $this->setupUserWithPermissions($company);
        $user_two = $this->setupUserWithPermissions($company);
        $first_plan = $this->setupMediaPlan($user);
        $second_plan = $this->setupMediaPlan($user);
        $third_plan = $this->setupMediaPlan($user);
    
        //assign a follower to first and third campaign
        $first_plan->addFollower($user_two);
        $second_plan->addFollower($user_two);

        $response = $this->getResponse($user_two);
        $actual = json_decode($response->getContent(), true);
        $this->assertCount(2, $actual['mediaPlans']);
    }

    public function test_empty_media_plan_list_is_returned_if_user_doea_not_have_access_to_media_plans()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id,
            'company_type_id' => factory(CompanyType::class)->create()->id
        ]);

        $user = $this->setupUserWithPermissions($company);
        $user_two = $this->setupUserWithPermissions($company);
        $first_plan= $this->setupMediaPlan($user);
        $second_plan = $this->setupMediaPlan($user);
        $third_plan= $this->setupMediaPlan($user);

        $response = $this->getResponse($user_two);
        $response->assertStatus(200);
        $actual = json_decode($response->getContent(), true);
        $this->assertCount(0, $actual['mediaPlans']);
    }

    public function test_it_returns_all_media_plans_for_user_with_admin_role()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id,
            'company_type_id' => factory(CompanyType::class)->create()->id
        ]);

        $user = $this->setupUserWithPermissions($company);
        $user_with_admin = $this->setupUserWithPermissions($company);
        $user_with_admin->assignRole(['name' => 'dsp.admin']);
        $first_plan = $this->setupMediaPlan($user);
        $second_plan = $this->setupMediaPlan($user);
        $third_plan = $this->setupMediaPlan($user);

        $response = $this->getResponse($user_with_admin);
        $actual = json_decode($response->getContent(), true);
        $this->assertCount(3, $actual['mediaPlans']);
    }

}