<?php

namespace Tests\Feature\Dsp\Station;

use Illuminate\Support\Arr;

class ListStationTest extends StationTestCase
{
    protected $route_name = 'stations.lists';

    protected function getResponse($user, $query_params=[])
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $query_params));
    }

    public function test_unauthenticated_user_cannot_access_station_list_route()
    {
        $response = $this->getJson(route($this->route_name));
        $response->assertStatus(401);
    }

    public function test_empty_array_returned_if_no_station_exist()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(200)
                ->assertExactJson(['data' => []]);
    }

    public function test_station_list_with_no_filter_params_returns_all_stations_retrievable_by_user()
    {
        $user = $this->setupAuthUser();
        $station_one = $this->setupStation($user);
        $station_two = $this->setupStation($user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = Arr::sort([$station_one->id, $station_two->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_station_list_with_publisher_id_param_returns_relevant_list()
    {
        $user = $this->setupAuthUser();
        $publisher_id = uniqid();
        $station_one = $this->setupStation($user, $publisher_id);
        $station_two = $this->setupStation($user);
        $query_params = ['publisher' => $publisher_id];
        $response = $this->getResponse($user, $query_params);
        $response->assertStatus(200);
        $expected = [$station_one->id];
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);

        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_vendor_list_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = [
            'links' => [
                'details' => route('stations.details', ['id' => $station->id], false)
            ]
        ];
        $actual = $response->json()['data'][0];
        $this->assertArraySubset($expected, $actual);
    }
}