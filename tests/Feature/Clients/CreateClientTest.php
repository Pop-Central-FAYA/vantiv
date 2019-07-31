<?php

namespace Tests\Feature\Clients;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Services\Client\StoreClient;


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
        $this->assertInstanceOf(StoreClient::class, $response);
    }

    public function create($faker)
    {
        $data = [
            'id' => $faker->word,
            'name' => $faker->word,
            'image_url' => $faker->url,
            'status' => $faker->word,
            'created_by' => $faker->word,
            'company_id' => $faker->word,
            'street_address' => $faker->word,
            'city' => $faker->word,
            'state' => $faker->word,
            'nationality' => $faker->word,
        ];
        $new_client = new StoreClient($data);
        return $new_client->store();
    }


   
}
