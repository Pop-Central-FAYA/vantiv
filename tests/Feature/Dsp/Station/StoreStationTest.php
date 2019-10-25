<?php

namespace Tests\Feature\Dsp\Station;

use Vanguard\Models\Publisher;

class StoreStationTest extends StationTestCase
{

    protected $route_name = 'stations.store';

    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }

    public function test_unauthenticated_user_cannot_access_station_creation_route()
    {
        $response = $this->postJson(route($this->route_name), []);
        $response->assertStatus(401);
    }

    public function test_station_successfully_created()
    {
        $station_data = [
            'publisher_id' => factory(Publisher::class)->create()->id,
            'name' => 'AIT',
            'type' => 'Regional',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'region' => 'Lagos',
            'key' => uniqid(),
            'broadcast' => 'Terrestial'
        ];

        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $station_data);
        $response->assertStatus(201);

        $expected = $response->json()['data'];
        $this->assertEquals($expected['name'], $station_data['name']);
    }

    public function test_invalid_data_for_station_returns_error_messages()
    {
        $station_data = [];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $station_data);
        $response->assertStatus(422);

        $keys = ['publisher_id', 'name', 'type', 'state', 'city',
                'region', 'key', 'broadcast'];
        $response->assertJsonValidationErrors($keys);
    }

    public function test_nonexistent_publisher_throws_validation_exception_on_create()
    {
        $random_pub_id = uniqid();

        $station_data = ['publisher_id' => $random_pub_id];

        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $station_data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['publisher_id']);
    }

    public function test_validation_error_is_thrown_if_station_with_same_name_already_exists()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $station_data = [
            'publisher_id' => $station->publisher_id,
            'name' => $station->name,
            'type' => $station->type,
            'state' => $station->state,
            'city' => 'Lagos',
            'region' => 'Lagos',
            'key' => uniqid(),
            'broadcast' => 'Terrestial'
        ];

        $response = $this->getResponse($user, $station_data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}