<?php

namespace Tests\Feature\Dsp\Mpo;

use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class ListMpoTest extends MpoTestCase
{
    protected $route_name = 'mpos.permitted_users';

    protected function setupUserWithPermissions($company = null)
    {
        $user = $this->setupAuthUser($company, ['approve.mpo', 'decline.mpo']);
        return $user;
    }


    protected function getResponse($user, $mpo_id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['mpo_id' => $mpo_id]));
    }

    public function test_unauthenticated_user_cannot_access_permitted_user_list_route()
    {
        $mpo_id =uniqid(); 
        $response = $this->getJson(route($this->route_name, ['mpo_id' => $mpo_id]));
        $response->assertStatus(401);
    }

    public function test_404_is_returned_when_the_mpo_does_not_exist()
    {
        $user = $this->setupAuthUser();
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

    public function test_empty_array_returned_if_no_user_exist_that_has_the_action_permission()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user);
        $response = $this->getResponse($user, $mpo->id);
        $response->assertStatus(200)
                ->assertExactJson(['data' => []]);
    }

    public function test_permitted_user_list_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupUserWithPermissions();
        $mpo = $this->setUpMpo($user);

        $response = $this->getResponse($user, $mpo->id);
        $response->assertStatus(200);

        $expected = [
            'links' => [
                'index' => route('users.index')
            ]
        ];
        $actual = $response->json()['data'][0];
        $this->assertArraySubset($expected, $actual);
    }

    public function test_it_get_all_users_with_the_action_permission()
    {
        $company = factory(Company::class)->create([
            'parent_company_id' => factory(ParentCompany::class)->create()->id
        ]);
        $this->setupUserWithPermissions($company);
        $this->setupUserWithPermissions($company);
        $user = $this->setupAuthUser($company);
        $mpo = $this->setUpMpo($user);

        $response = $this->getResponse($user, $mpo->id);
        $response->assertStatus(200);

        $actual = $response->json()['data'];
        $this->assertCount(2, $actual);
    }
}