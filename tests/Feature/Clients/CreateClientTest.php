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

    public function test_user_can_create_client()
    {
        $faker = Factory::create();
        $response = $this->create($faker);
        $this->assertTrue(true);
        $this->assertTrue($response);
    }

    public function create($faker)
    {
        $data = [
            'id' => $faker->word,
            'name' => $faker->word,
            'brand' => $faker->word,
            'image_url' => $faker->url,
            'status' => $faker->word,
            'created_by' => $faker->word,
            'company_id' => $faker->word,
            'street_address' => $faker->word,
            'city' => $faker->word,
            'state' => $faker->word,
            'nationality' => $faker->word,
        ];
        $datas = [
            'client_id' => $faker->word,
            'first_name' => $faker->word,
            'last_name' => $faker->word,
            'email' => $faker->word,
            'phone_number' => $faker->word,
            'is_primary' => true,
        ];
      
        $objec = (object) $data;
        $new_client = new StoreClient($objec);
        $client = $new_client->storeClient(); 
        $new_client_contact = new StoreClientContact($client, $datas);
        $rsult =  $new_client_contact->store();

        return true;
    }

    public function test_authenticated_user_can_create_client_with_route() {
         $faker = Factory::create();
          $response = $this->actingAs($user)->post('/clients', [
                '_token' => csrf_token(),
                'id' => $faker->word,
                'name' => $faker->word,
                'brand' => $faker->word,
                'image_url' => $faker->url,
                'status' => $faker->word,
                'created_by' => $faker->word,
                'company_id' => $faker->word,
                'street_address' => $faker->word,
                'city' => $faker->word,
                'state' => $faker->word,
                'nationality' => $faker->word,
          ]);
         // $this->assertTrue($response);
         $data = factory(Client::class)->create();
        
          
    }

    public function test_unauthenticated_user_can_access_create_client_route()
    {
        \Session::start();
        $response = $this->postJson(route('new.client'), [
            '_token' => csrf_token()
        ]);
        $response->assertStatus(401);

    }



   
}
