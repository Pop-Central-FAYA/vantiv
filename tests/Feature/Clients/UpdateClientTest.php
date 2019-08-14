<?php

namespace Tests\Feature\Client;
use Tests\TestCase;
use Faker\Factory;

use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 * @todo add permission support
 * @todo add support for only people with access rights to update a client model
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */

class UpdateClient extends TestCase 
{
    protected $route_name = 'client.update';
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
        return [
            '_token' => csrf_token(),
            'name' => "Oluwa captain",
            'brand' => "C and O",
            'image_url' =>  'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
            'street_address' => $faker->address,
            'city' => $faker->city,
            'state' => $faker->state,
            'nationality' => $faker->country,
            'contacts'=> $contact,
        ];
    }
    protected function setupClient($user)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
       factory(\Vanguard\Models\ClientContact::class)->create([
            'client_id' => $client->id
        ]);
        return $client->refresh();
    }
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.client']);
        return $user;
    }

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $id]), $data);
    }
    public function test_attempting_to_update_non_existent_client_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $client_id = uniqid();
        $response = $this->getResponse($user, $client_id, [ '_token' => csrf_token()]);
        $response->assertStatus(404);
    }

    public function test_invalid_data_is_validated_on_update()
    {
        $contact= [
            'first_name' => '',
            'last_name' => '',
            'email' => "dino",
            'phone_number' => "",
            'is_primary' => true,
        ];
        $client_data = [
            '_token' => csrf_token(),
            'name' => '',
            'brand' => '',
            'image_url' =>  'quae-quae-error-cum-qui-ducimus',
            'street_address' => '',
            'city' => '',
            'state' => '',
            'nationality' => '',
            'contacts'=> $contact,
        ];

        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);
        
        $response = $this->getResponse($user, $client->id, $client_data);

        $response->assertStatus(422);
    }

    public function test_403_returned_if_attempting_to_update_client_that_user_does_not_have_rights_to_update()
    {
        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);

        $another_user = $this->setupAuthUser();
        $another_client = $this->setupClient($another_user);

        $response = $this->getResponse($user, $another_client->id, ['_token' => csrf_token()]);

        $response->assertStatus(403);
    }

    public function test_user_without_update_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $client = $this->setupClient($user);
        
        $response = $this->getResponse($user, $client->id, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }

    public function test_authentication_user_can_update_client_with_route()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);
        $data = $this->getData();

        $response = $this->getResponse($user, $client->id, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => "Oluwa captain",
         ]);
    }

}