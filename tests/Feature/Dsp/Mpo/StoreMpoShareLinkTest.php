<?php

namespace Tests\Feature\Feature\Dsp\MpoShareLink;

use Tests\Feature\Dsp\Mpo\MpoTestCase;

class StoreMpoShareLinkTest extends MpoTestCase
{
    protected $route_name = 'mpo_share_link.store';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['create.campaign']);
        return $user;
    }

    protected function getResponse($user, $data, $mpo_id)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['id' => $mpo_id]), $data);
    }

    public function test_only_authenticated_user_can_generate_the_link()
    {
        $response = $this->postJson(route($this->route_name, ['id' => 'fdsh']), []);
        $response->assertStatus(401);
    }

    public function test_it_returns_404_if_mpo_is_not_found()
    {
        $user = $this->setupAuthUser();
        $mpo_id = uniqid();
        $response = $this->getResponse($user, [], $mpo_id);
        $response->assertStatus(404);
    }

    public function test_user_who_does_not_have_the_right_access_to_store_share_link_get_the_right_status()
    {
        $user = $this->setupAuthUser();
        $another_user = $this->setupAuthUser();
        $share_link_data = [
            'mpo_id' => $mpo_id = $this->setupMpo($user)->id,
            'email' => 'test@gmail.com'
        ];
        $response = $this->getResponse($another_user, [], $mpo_id);
        $response->assertStatus(403);
    }

    public function test_it_can_create_a_shared_link()
    {
        $user = $this->setupAuthUser();
        $share_link_data = [
            'mpo_id' => $mpo_id = $this->setupMpo($user)->id,
            'email' => 'test@gmail.com'
        ];
        $response = $this->getResponse($user, $share_link_data, $mpo_id);
        $response->assertStatus(201);
        return $response;
    }
}
