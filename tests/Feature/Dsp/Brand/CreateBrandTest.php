<?php

namespace Tests\Feature\Dsp\Brand;

use Tests\TestCase;
use Vanguard\Models\Client;


class CreateBrand extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'brand.create';
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['create.client']);
        return $user;
    }
    
    protected function getData()
    {
      $user = $this->setupUserWithPermissions();
       $client= $this->setupClient($user);
        return [
            'name' => 'Ayo NIG LMT',
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
            'client_id' => $client->id  ];
    }

    protected function setupClient($user)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
        return $client->refresh();
    }
    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }

    public function test_invalid_brand_data_is_validated_on_update()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(422);
        $keys = ['name',  'client_id','image_url'];
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
