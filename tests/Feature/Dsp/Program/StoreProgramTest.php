<?php

namespace Tests\Feature\Dsp\Program;

use Tests\Feature\Dsp\Station\StationTestCase;
use Vanguard\Models\AdVendor;

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

    public function test_programs_successfully_created_with_ad_vendor_association()
    {
        $vendor_one = factory(AdVendor::class)->create();
        $vendor_two = factory(AdVendor::class)->create();
        
        $ad_vendors = [
            ['id' => $vendor_one->id, 'name' => $vendor_one->name],
            ['id' => $vendor_two->id, 'name' => $vendor_two->name]
        ];
        
        $program_data = array_merge($this->storeData(), ['ad_vendors' => $ad_vendors]);

        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $response = $this->getResponse($user, $station->id, $program_data);

        $response->assertStatus(201);
        $response->assertJson(['data' => ['ad_vendors' => $ad_vendors]]);
    }

    public function test_empty_ad_vendor_array_throws_validation_exception_on_store()
    {
        $program_data = ['ad_vendors' => []];

        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $response = $this->getResponse($user, $station->id, $program_data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ad_vendors']);
    }

    public function test_nonexistent_vendors_throws_validation_exception_on_create()
    {
        $random_vendor_id = uniqid();
        $vendor_one = factory(AdVendor::class)->create();

        $ad_vendors = [
            ['id' => $vendor_one->id, 'name' => $vendor_one->name],
            ['id' => $random_vendor_id, 'name' => 'random nonexistent vendor']
        ];
        $vendor_data = ['ad_vendors' => $ad_vendors];

        $user = $this->setupAuthUser();
        $station = $this->setupStation($user);

        $response = $this->getResponse($user, $station, $vendor_data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ad_vendors.1.id']);
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
            ],
        ];
    }
}