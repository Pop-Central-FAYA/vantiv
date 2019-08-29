<?php

namespace Tests\Feature\Feature\Dsp\MpoShareLink;

use Tests\TestCase;

class SubmitToVendor extends TestCase
{
    protected $route_name = 'mpo_share_link.submit';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['create.campaign']);
        return $user;
    }

    protected function getResponse($user, $data, $mpo_id)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['id' => $mpo_id]), $data);
    }

    public function test_only_authenticated_user_can_submit_link_to_vendor()
    {
        $response = $this->postJson(route($this->route_name, ['id' => 'fdsh']), []);
        $response->assertStatus(401);
    }

    public function test_it_validates_the_request_and_return_proper_status()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, [], 'ghgdgdf');
        $response->assertStatus(422);
    }

    public function test_user_with_wrong_permission_gets_necessary_status()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, [], 'gdghfa');
        $response->assertStatus(403);
    }
}
