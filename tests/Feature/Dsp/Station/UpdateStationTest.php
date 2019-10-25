<?php

namespace Tests\Feature\Dsp\Station;

use Illuminate\Support\Arr;

/**
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */
class UpdateStationTest extends StationTestCase
{
    protected $route_name = 'stations.update';

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_station_update_route()
    {
        $station_id = uniqid();
        $response = $this->patchJson(route($this->route_name, ['id' => $station_id]), []);
        $response->assertStatus(401);
    }

    public function test_attempting_to_update_non_existent_station_returns_404()
    {
        $user = $this->setupAuthUser();
        $station_id = uniqid();
        $response = $this->getResponse($user, $station_id, $this->updateData());
        $response->assertStatus(404);
    }

    public function test_station_fields_are_updated_if_value_sent_in_request()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $station_data = $this->updateData();

        $station_array = Arr::dot($station->with('programs', 'publisher')->get()->first()->toArray());
        
        $response = $this->getResponse($user, $station->id, $station_data);

        $response->assertStatus(200);
        
        $actual_data = Arr::dot($station->with('programs', 'publisher')->get()->first()->toArray());
        
        $expected_data = array_merge($station_array, Arr::dot($station_data));
        
        Arr::forget($expected_data, ['updated_at']);

        $this->assertArraySubset($expected_data, $actual_data);
    }

    private function updateData()
    {
        return [
            'name' => 'AIT',
            'type' => 'Regional',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'region' => 'Lagos',
            'broadcast' => 'Satellite'
        ];
    }

    public function test_invalid_data_is_validated_on_update()
    {
        $station_data = [
            'name' => '',
            'type' => '',
            'state' => '',
            'city' => '',
            'region' => '',
            'broadcast' => ''
        ];

        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        
        $response = $this->getResponse($user, $station->id, $station_data);

        $response->assertStatus(422);
    }

    public function test_attempting_to_update_station_with_existing_name_causes_validation_error()
    {
        $user = $this->setupAuthUser();

        $old_station = $this->setupStation($user);
        $station = $this->setupStation($user);

        $station_data = [
            'name' => $old_station->name,
            'type' => $old_station->type,
            'state' => $old_station->state
        ];

        $response = $this->getResponse($user, $station->id, $station_data);

        $response->assertStatus(422);
    }

    public function test_can_update_same_station_with_same_name()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $station_data = [
            'name' => $station->name,
            'state' => 'Abuja',
            'type' => 'International'
        ];

        $response = $this->getResponse($user, $station->id, $station_data);
        $response->assertStatus(200);
        $this->assertEquals($station_data['name'], $response->json()['data']['name']);
    }
}