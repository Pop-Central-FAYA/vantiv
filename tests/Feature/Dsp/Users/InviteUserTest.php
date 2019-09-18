<?php

namespace Tests\Feature\Dsp\Clients;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\User;


class InviteUser extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'user.invite';
    
    protected function getData()
    {
        $faker = Factory::create();
      
        return [
            '_token' => csrf_token(),
            'email' => "dino@yahoo.com",
        ];
    }
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['create.user']);
        return $user;
    }


    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }

   
     // Permission Tests
    

    public function test_unauthenticated_user_cannot_access_user_invitation_route()
    {
        $response = $this->postJson(route($this->route_name), []);
        $response->assertStatus(401);
    }

    public function test_user_without_create_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, []);
        $response->assertStatus(403);
    }

   
    public function test_invalid_client_data_is_validated_on_update()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(422);
        $keys = ['roles',  'email'];
         $response->assertJsonValidationErrors($keys);
    }
 /*
    public function test_authenticated_user_can_create_client()
    {
             
        \Session::start();
        $data = $this->getData();
        $user = $this->setupUserWithPermissions();
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
        $user = $this->setupUserWithPermissions();
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
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => "Ayo NIG LMT",
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
        ]);
    }

    */
}

