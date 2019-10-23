<?php

namespace Tests\Feature\Dsp\Mpo;

use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class RequestApprovalTest extends MpoTestCase
{
    protected $route_name = 'mpos.request_approval';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['approve.mpo', 'decline.mpo']);
        return $user;
    }

    protected function getResponse($user, $mpo_id, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['mpo_id' => $mpo_id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_mpo_request_approval_route()
    {
        $mpo_id =uniqid(); 
        $response = $this->postJson(route($this->route_name, ['mpo_id' => $mpo_id]));
        $response->assertStatus(401);
    }

    public function test_404_is_returned_when_the_mpo_does_not_exist()
    {
        $user = $this->setupUserWithPermissions();
        $mpo_id = uniqid();
        $response = $this->getResponse($user, $mpo_id, []);
        $response->assertStatus(404);
    }

    public function test_it_returns_the_right_status_if_user_does_not_have_the_authorization()
    {
        $user = $this->setupAuthUser();
        $another_user = $this->setupAuthUser();
        $mpo = $this->setupMpo($another_user);
        $response = $this->getResponse($user, $mpo->id, []);
        $response->assertStatus(403);
    }

    public function test_approval_request_can_only_be_made_for_pending_mpos()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);

        $user1 = $this->setupAuthUser($company);
        $user2 = $this->setupUserWithPermissions($company);
        $mpo = $this->setupMpo($user1, MpoStatus::PENDING);

        $response = $this->getResponse($user1, $mpo->id, ['user_id' => $user2->id]);
        
        $expected = $response->json()['data'];

        $this->assertEquals($mpo->id, $expected['id']);
        $this->assertEquals(MpoStatus::IN_REVIEW, $expected['status']);
    }

    public function test_it_does_not_make_approval_request_for_other_statuses()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);

        $user1 = $this->setupAuthUser($company);
        $user2 = $this->setupUserWithPermissions($company);
        $mpo = $this->setupMpo($user1, MpoStatus::APPROVED);

        $response = $this->getResponse($user1, $mpo->id, ['user_id' => $user2->id]);
        $expected = json_decode($response->getContent(), true);

        $this->assertNull($expected);
    }
}