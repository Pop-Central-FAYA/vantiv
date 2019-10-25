<?php

namespace Tests\Feature\Dsp\Program;

use Illuminate\Support\Arr;
use Tests\Feature\Dsp\Station\StationTestCase;

class UpdateProgramTest extends StationTestCase
{

    protected $route_name = 'programs.update';

    protected function getResponse($user, $station_id, $program_id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, 
                                                ['station_id' => $station_id, 'program_id' => $program_id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_program_creation_route()
    {
        $station_id = uniqid();
        $program_id = uniqid();
        $response = $this->patchJson(route($this->route_name, ['station_id', $station_id, 'program_id' => $program_id]), []);
        $response->assertStatus(401);
    }

    public function test_attempting_to_update_non_existent_station_returns_404()
    {
        $user = $this->setupAuthUser();
        $station_id = uniqid();
        $program_id = uniqid();
        $response = $this->getResponse($user, $station_id, $program_id, $this->updateData());
        $response->assertStatus(404);
    }

    public function test_attempting_to_update_non_existent_program_returns_404()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        $program_id = uniqid();
        $response = $this->getResponse($user, $station->id, $program_id, $this->updateData());
        $response->assertStatus(404);
    }

    public function test_program_fields_are_updated_if_value_sent_in_request()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        $program = $station->programs->first();
        $program_data = $this->updateData();

        $response = $this->getResponse($user, $station->id, $program->id, $program_data);

        $response->assertStatus(200);

        $actual_data = $response->json()['data'];

        $this->assertEquals($program_data['program_name'], $actual_data['name']);
    }

    public function test_invalid_data_for_program_returns_error_messages()
    {
        $program_data = [
            'program_name' => '',
            'rates' => [],
            'durations' => [],
            'days' => [],
            'start_time' => [],
            'end_time' => []
        ];
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        $program = $station->programs->first();
        $response = $this->getResponse($user, $station->id, $program->id, $program_data);
        $response->assertStatus(422);

        $keys = ['program_name', 'rates', 'days', 'durations', 'start_time', 'end_time'];
        $response->assertJsonValidationErrors($keys);
    }

    public function test_attempting_to_update_program_with_existing_name_causes_validation_error()
    {
        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);
        $old_program = $station->programs->first();
        $program = $this->setupProgram($station->id);
        $program_data = [
            'program_name' => $old_program->program_name
        ];

        $response = $this->getResponse($user, $station->id, $program->id, $program_data);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['program_name']);
    }

    private function updateData()
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