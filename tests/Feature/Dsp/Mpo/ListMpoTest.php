<?php

namespace Tests\Feature\Dsp\Mpo;

use Illuminate\Support\Arr;

class ListMpoTest extends MpoTestCase
{
    protected $route_name = 'mpos.list';

    protected function getResponse($user, $campaign_id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['campaign_id' => $campaign_id]));
    }

    public function test_unauthenticated_user_cannot_access_mpo_list_route()
    {
        $campaign_id =uniqid(); 
        $response = $this->getJson(route($this->route_name, ['campaign_id' => $campaign_id]));
        $response->assertStatus(401);
    }

    public function test_empty_array_returned_if_no_mpo_exist_that_the_user_has_access_to()
    {
        $user = $this->setupAuthUser();
        $campaign_id = uniqid();
        $response = $this->getResponse($user, $campaign_id);
        $response->assertStatus(200)
                ->assertExactJson(['data' => []]);
    }

    public function test_mpo_list_is_always_limited_to_mpos_that_belongs_to_thesame_campaign()
    {
        $user = $this->setupAuthUser();
        $mpo_one = $this->setUpMpo($user);
        $mpo_two = $this->setUpMpo($user);

        $response = $this->getResponse($user, $mpo_one->campaign_id);
        $response->assertStatus(200);

        $expected = $mpo_one->id;
        $actual = Arr::pluck($response->json()['data'], 'id')[0];

        $this->assertEquals($expected, $actual);
    }

    public function test_mpo_list_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setUpMpo($user);

        $response = $this->getResponse($user, $mpo->campaign_id);
        $response->assertStatus(200);

        $expected = [
            'links' => [
                'details' => route('mpos.details', ['id' => $mpo->id], false)
            ]
        ];
        $actual = $response->json()['data'][0];
        $this->assertArraySubset($expected, $actual);
    }
}