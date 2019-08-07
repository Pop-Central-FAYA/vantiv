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
    protected $route_name = 'client.create';
    
    protected function getData()
    {
        $faker = Factory::create();
        $contact= [
            'first_name' => "Dino",
            'last_name' => "Melaye",
            'email' => "dino@yahoo.com",
            'phone_number' => "+23466219699",
            'is_primary' => true,
        ];
        $brand=[
            'name' => "Ayo NIG LMT",
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
            'status' =>'active',
        ];
        return [
            '_token' => csrf_token(),
            'name' => "Oluwa captain",
            'brand' => "C and O",
            'image_url' =>  'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
            'street_address' => $faker->address,
            'city' => $faker->city,
            'state' => $faker->state,
            'nationality' => $faker->country,
            'contact'=> $contact,
            'brand_details'=>$brand
        ];
    }

    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }

    public function test_invalid_client_data_is_validated_on_update()
    {
        \Session::start();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(422);
        $keys = ['name', 'image_url', 'street_address', 'city', 'state', 'nationality','contact.first_name', 'contact.last_name', 'contact.email', 'contact.phone_number','contact.is_primary',  'brand_details.name','brand_details.image_url',
         ];
         $response->assertJsonValidationErrors($keys);
    }

    public function test_authenticated_user_can_create_client()
    {
             
        \Session::start();
        $data = $this->getData();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => "Oluwa captain",
            'image_url' =>  'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
        ]);
    }

    public function test_authenticated_user_can_create_client_contact()
    {
             
        \Session::start();
        $data = $this->getData();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
                'first_name' => "Dino",
                'last_name' => "Melaye",
                'email' => "dino@yahoo.com",
                'phone_number' => "+23466219699",
        ]);
    }
    public function test_authenticated_user_can_create_client_brand()
    {
             
        \Session::start();
        $data = $this->getData();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => "Ayo NIG LMT",
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
            'status' =>'active'
        ]);
    }
}
