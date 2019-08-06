<?php

namespace Tests\Feature\Clients;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Services\Client\StoreClient;
use Vanguard\Services\Client\StoreClientContact;
use Vanguard\Models\Client;


class CreateClient extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'new.client';

    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }

    public function test_invalid_client_data_is_validated_on_update()
    {
       
        \Session::start();
        $faker = Factory::create();
        $data = [
            '_token' => csrf_token(),
            'name' => $faker->word,
            'brand' => $faker->word,
            'image_url' => $faker->url,
            'status' => $faker->word,
            'street_address' => $faker->word,
            'city' => $faker->word,
            'state' => $faker->word,
            'nationality' => $faker->word,
            'contact'=> [
                'first_name' => $faker->word,
                'last_name' => $faker->word,
                'email' => $faker->word,
                'phone_number' => $faker->word,
                'is_primary' => true,
            ],
            'brand_details'=>[
                'name' => $faker->word,
                'image_url' => "",
                'status' => "",
            ]
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(422);
    }

    public function test_authentication_user_can_create_client()
    {
             
        \Session::start();
        $faker = Factory::create();
        $data = [
            '_token' => csrf_token(),
            'name' => "Oluwa captain",
            'brand' => "C and O",
            'image_url' => $faker->url,
            'status' => $faker->word,
            'street_address' => $faker->word,
            'city' => $faker->word,
            'state' => $faker->word,
            'nationality' => $faker->word,
            'contact'=> [
                'first_name' => $faker->word,
                'last_name' => $faker->word,
                'email' => $faker->email,
                'phone_number' => $faker->word,
                'is_primary' => true,
            ],
            'brand_details'=>[
                'name' => $faker->word,
                'image_url' => $faker->url,
                'status' =>$faker->word,
            ]
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => "Oluwa captain",
            'brand' => "C and O",
        ]);
    }
}
