<?php

namespace Tests\Feature\Dsp\Mpo;

use Illuminate\Support\Arr;

class MpoDetailsTest extends MpoTestCase
{
    protected $route_name = 'mpos.details';

    protected function getResponse($user, $mpo_id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['mpo_id' => $mpo_id]));
    }

    public function test_unauthenticated_user_cannot_access_mpo_list_route()
    {
        $mpo_id =uniqid(); 
        $response = $this->getJson(route($this->route_name, ['mpo_id' => $mpo_id]));
        $response->assertStatus(401);
    }

    public function test_it_returns_the_right_status_if_user_does_not_have_the_authorization()
    {
        $user = $this->setupAuthUser();
        $user2 = $this->setupAuthUser();
        $mpo = $this->setupMpo($user);
        $response = $this->getResponse($user2, $mpo->id);
        $response->assertStatus(403);
    }

    public function test_it_returns_details_data_for_an_mpo()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user);
        $response = $this->getResponse($user, $mpo->id);
        $response->assertViewIs('agency.mpo.details')
                ->assertViewHas(['mpo']);
    }
}