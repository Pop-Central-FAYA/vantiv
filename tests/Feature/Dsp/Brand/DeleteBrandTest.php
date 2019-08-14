<?php

namespace Tests\Feature\Dsp\Clients;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Services\Client\StoreClient;
use Vanguard\Services\Client\StoreClientContact;
use Vanguard\Models\Client;


class DeleteBrand extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'brand.destroy';
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.client']);
        return $user;
    }
    protected function setupBrand($user_id)
    {
        $brand = factory(\Vanguard\Models\Brand::class)->create([
            'created_by' => $user_id
        ]);
        return $brand->refresh();
    }

    protected function setupClient($user)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
      $brand = factory(\Vanguard\Models\Brand::class)->create([
            'created_by' => $user->id,
            'client_id' => $client->id,
        ]);
        return $brand->refresh();
    }

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->deleteJson(route($this->route_name, ['id' => $id]), $data);
    }



    public function test_authentication_user_can_delete_with_route()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupClient($user);

        $response = $this->getResponse($user, $brand->id, ['_token' => csrf_token(), 'client_id' => uniqid()]);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'Brand deleted successfully'
         ]);
    }

    public function test_403_returned_if_attempting_to_delete_brand_that_user_does_not_have_rights_to()
    {
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupClient($user);

        $another_user = $this->setupAuthUser();
        $another_brand = $this->setupClient($another_user);
        $response = $this->getResponse($user, $another_brand->id, ['_token' => csrf_token(), 'client_id' => uniqid()]);

        $response->assertStatus(403);
    }
    public function test_attempting_to_delete_non_existent_brand_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $brand_id = uniqid();
        $response = $this->getResponse($user, $brand_id, ['_token' => csrf_token(), 'client_id' => uniqid()]);
        $response->assertStatus(404);
    }
}
