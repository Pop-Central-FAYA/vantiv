<?php

namespace Tests\Feature\Dsp\MediaPlan;

use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class AssignFollowerTest extends MediaPlanTestCase
{

    protected $route_name = 'media_plan.assign_followers';

    protected function setUpUserWithSameCompany()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);
        return $this->setupAuthUser($company);
    }

    protected function getResponse($user, $plan_id, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['id' => $plan_id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_assign_follower_route()
    {
        $plan_id = uniqid();
        $response = $this->postJson(route($this->route_name, ['id' => $plan_id]), []);
        $response->assertStatus(401);
    }

    public function test_it_validate_user_id_is_required()
    {
        $user = $this->setupAuthUser();
        $plan_id = uniqid();
        $response = $this->getResponse($user, $plan_id, [
            'user_id' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);
    }

    public function test_it_validate_user_id_is_an_array()
    {
        $user = $this->setupAuthUser();
        $plan_id = uniqid();
        $response = $this->getResponse($user, $plan_id, [
            'user_id' => 'jhdfcsjh'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);
    }

    public function test_it_returns_404_when_trying_to_assign_follower_to_media_plan_that_doesnt_exist()
    {
        $user = $this->setupAuthUser();
        $plan_id = uniqid();
        $response = $this->getResponse($user, $plan_id, $this->requestData());
        $response->assertStatus(404);
    }

    public function test_user_has_the_right_access_to_assign_followers()
    {
        $user = $this->setUpUserWithSameCompany();
        $another_user = $this->setUpUserWithSameCompany();
        $mediaPlan = $this->setupMediaPlan($another_user);
        $response = $this->getResponse($user, $mediaPlan->id, $this->requestData());
        $response->assertStatus(403);
    }

    public function test_it_can_assign_assign_follower_to_a_media_plan()
    {
        $user = $this->setUpUserWithSameCompany();
        $mediaPlan = $this->setupMediaPlan($user);
        $response = $this->getResponse($user, $mediaPlan->id, $this->requestData());
        $response->assertStatus(200);
    }

    public function test_the_creator_of_the_media_plan_is_also_a_follower()
    {
        $user = $this->setUpUserWithSameCompany();
        $mediaPlan = $this->setupMediaPlan($user);
        $response = $this->getResponse($user, $mediaPlan->id, $this->requestData());
        $response->assertStatus(200);

        $this->assertDatabaseHas('followers', [
            'follower_id' => $user->id
        ]);
    }

    public function test_it_persist_the_followers_into_the_database()
    {
        $user = $this->setUpUserWithSameCompany();
        $mediaPlan = $this->setupMediaPlan($user);
        $this->getResponse($user, $mediaPlan->id, $this->requestData());
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