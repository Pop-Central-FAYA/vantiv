<?php

namespace Tests\Feature\Dsp\Program;

use Tests\Feature\Dsp\Station\StationTestCase;

class StoreProgramTest extends StationTestCase
{

    protected $route_name = 'programs.store';

    protected function getResponse($user, $station_id, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name, ['station_id' => $station_id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_program_creation_route()
    {
        $station_id = uniqid();
        $response = $this->postJson(route($this->route_name, ['station_id', $station_id]), []);
        $response->assertStatus(401);
    }

    public function test_attempting_to_store_a_program_for_non_existent_station_returns_404()
    {
        $user = $this->setupAuthUser();
        $station_id = uniqid();
        $response = $this->getResponse($user, $station_id, $this->storeData());
        $response->assertStatus(404);
    }

    public function test_program_successfully_created()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        $program_data = $this->storeData();
        $response = $this->getResponse($user, $station->id, $program_data);
        $response->assertStatus(201);

        $expected = $response->json()['data'];
        $this->assertEquals($expected['name'], $program_data['program_name']);
    }

    public function test_invalid_data_for_program_returns_error_messages()
    {
        $station_data = [];
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        $response = $this->getResponse($user, $station->id, $station_data);
        $response->assertStatus(422);

        $keys = ['program_name', 'rates', 'days', 'durations', 'start_time', 'end_time'];
        $response->assertJsonValidationErrors($keys);
    }

    public function test_validation_error_is_thrown_if_program_with_same_name_already_exists()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $program_data = [
            'program_name' => $station->programs->first()->program_name
        ];

        $response = $this->getResponse($user, $station->id, $program_data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['program_name']);
    }

    private function storeData()
    {
        return [
            'program_name' => 'Papa Ajasco',
            'rates' => [
                3000,
                4000,
                6000,
                8000,
            ],
            'durations' => [
                '15', '30', '45', '60'
            ],
            'days' => [
                'monday'
            ],
            'start_time' => [
                '20:00:00'
            ],
            'end_time' => [
                '21:00:00'
            ]
        ];
    }
}