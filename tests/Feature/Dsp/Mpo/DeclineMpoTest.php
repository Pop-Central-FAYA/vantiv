<?php

namespace Tests\Feature\Dsp\Mpo;

use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class DeclineMpoTest extends MpoTestCase
{
    protected $route_name = 'mpos.decline_mpo';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['approve.mpo', 'decline.mpo']);
        return $user;
    }


    protected function getResponse($user, $mpo_id)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['mpo_id' => $mpo_id]));
    }

    public function test_unauthenticated_user_cannot_access_mpo_decline_route()
    {
        $mpo_id =uniqid(); 
        $response = $this->postJson(route($this->route_name, ['mpo_id' => $mpo_id]));
        $response->assertStatus(401);
    }

    public function test_user_without_the_right_permission_cannot_decline_an_mpo()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user);
        $response = $this->getResponse($user, $mpo->id);
        $response->assertStatus(403);
    }

    public function test_404_is_returned_when_the_mpo_does_not_exist()
    {
        $user = $this->setupUserWithPermissions();
        $mpo_id = uniqid();
        $response = $this->getResponse($user, $mpo_id);
        $response->assertStatus(404);
    }

    public function test_it_returns_the_right_status_if_user_does_not_have_the_authorization()
    {
        $user = $this->setupAuthUser();
        $another_user = $this->setupAuthUser();
        $mpo = $this->setupMpo($another_user);
        $response = $this->getResponse($user, $mpo->id);
        $response->assertStatus(403);
    }

    public function test_it_can_only_decline_mpo_whoose_status_is_in_review()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);

        $user1 = $this->setupAuthUser($company);
        $user2 = $this->setupUserWithPermissions($company);
        $mpo = $this->setupMpo($user1, MpoStatus::IN_REVIEW, $user1->id);

        $response = $this->getResponse($user2, $mpo->id);
        
        $expected = json_decode($response->getContent(), true);
        $this->assertEquals($mpo->id, $expected['mpo_details']['id']);
        $this->assertEquals(MpoStatus::PENDING, $expected['mpo_details']['status']);
    }

    public function test_it_does_not_decline_mpo_with_status_other_than_in_review()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);

        $user1 = $this->setupAuthUser($company);
        $user2 = $this->setupUserWithPermissions($company);
        $mpo = $this->setupMpo($user1);

        $response = $this->getResponse($user2, $mpo->id);
        $expected = json_decode($response->getContent(), true);

        $this->assertNull($expected);
    }
}