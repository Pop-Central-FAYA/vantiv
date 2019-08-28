<?php

namespace Tests\Feature\Feature\Dsp\MpoShareLink;

use Tests\TestCase;
use Vanguard\Models\CampaignMpo;

class StoreShareLinkTest extends TestCase
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

    public function test_user_with_wrong_permission_gets_necessary_status()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, [], 'gdghfa');
        $response->assertStatus(403);
    }

    public function test_it_can_create_a_shared_link()
    {
        $share_link_data = [
            'mpo_id' => $mpo_id = factory(CampaignMpo::class)->create()->id,
            'email' => 'test@gmail.com'
        ];
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $share_link_data, $mpo_id);
        $response->assertStatus(201);
        return $response;
    }

    public function test_it_persists_the_data_into_the_database()
    {
        $share_link_data = [
            'mpo_id' => $mpo_id = factory(CampaignMpo::class)->create()->id,
            'email' => 'test@gmail.com'
        ];
        $user = $this->setupUserWithPermissions();
        $this->getResponse($user, $share_link_data, $mpo_id);
        $this->assertDatabaseHas('mpo_share_links', [
            'mpo_id' => $mpo_id
        ]);
    }
}
