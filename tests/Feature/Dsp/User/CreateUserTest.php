<?php

namespace Tests\Feature\Dsp\User;

use Tests\TestCase;


class CreateUser extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'users.invite';
    
    protected function getData()
    {
        return [
            '_token' => csrf_token(),
             "roles" => array(
                1 => array(
                  "id" => 3,
                  "role" => "dsp.admin",
                  "label" => "Admin",
                )),
            "email" => "ayobami@yahoo.com"
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

    public function test_user_without_update_permissions_cannot_access_route()
    {
        \Session::start();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }

    public function test_invalid_user_data_is_validated_on_create()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(422);
        $keys = ['roles', 'email'];
         $response->assertJsonValidationErrors($keys);
    }

    public function test_authenticated_user_can_create_user()
    {   
        \Session::start();
        $data = $this->getData();
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'email' => "ayobami@yahoo.com"
        ]);
    }

}
