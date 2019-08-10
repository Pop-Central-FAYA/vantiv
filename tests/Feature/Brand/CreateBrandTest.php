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
    protected $route_name = 'brand.create';
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.company']);
        return $user;
    }
    
    protected function getData()
    {
        return [
            'name' => "Ayo NIG LMT",
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
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
        $keys = ['name', 'image_url'];
        $response->assertJsonValidationErrors($keys);
    }

    public function test_authenticated_user_can_create_brand_with_route()
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
    public function test_user_without_create_brand_permissions_cannot_access_company_update_route()
    {
        \Session::start();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }
}
