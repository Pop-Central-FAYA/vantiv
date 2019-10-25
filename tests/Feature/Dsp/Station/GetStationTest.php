<?php

namespace Tests\Feature\Dsp\Station;

use Illuminate\Support\Arr;

class GetStationTest extends StationTestCase
{
    
    protected $route_name = 'stations.details';

    protected function getResponse($user, $id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['id' => $id]));
    }

    public function test_unauthenticated_user_cannot_access_station_details_route()
    {
        $station_id = uniqid();
        $response = $this->getJson(route($this->route_name, ['id' => $station_id]));
        $response->assertStatus(401);
    }

    public function test_attempting_to_get_non_existent_station_returns_404()
    {
        $user = $this->setupAuthUser();
        $station_id = uniqid();
        $response = $this->getResponse($user, $station_id);
        $response->assertStatus(404);
    }

    public function test_station_with_full_details_is_retrieved()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        
        $response = $this->getResponse($user, $station->id);
        $response->assertStatus(200);

        $actual = Arr::dot($response->json()['data']);
        $expected = Arr::dot($station->toArray());
        $this->assertEquals($actual['id'], $expected['id']);
    }

    public function test_station_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $response = $this->getResponse($user, $station->id);
        $response->assertStatus(200);

        $expected = [
            'links' => [
                'update' => route('stations.update', ['id' => $station->id], false)
            ]
        ];
        $actual = $response->json()['data'];
        $this->assertArraySubset($expected, $actual);
    }
}