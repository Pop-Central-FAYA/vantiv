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
    protected $route_name = 'brand.update';
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.client']);
        return $user;
    }
    
    protected function getData()
    {
        return [
            '_token' => csrf_token(),
            'name' => "Ayo NIG LMT",
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
        ];
    }
    protected function setupBrand($user_id)
    {
        $company = factory(\Vanguard\Models\Brand::class)->create([
            'created_by' => $user_id
        ]);
        return $company->refresh();
    }
    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name), $data);
    }

    public function test_invalid_brand_data_is_validated_on_update()
    {
        $company_id = uniqid();
        \Session::start();
        $data = [
            '_token' => csrf_token(),
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $company_id, $data);
        $response->assertStatus(422);
    }

    public function test_authentication_user_can_update_company_with_route()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupBrand($user->id);
        $data = $this->getData();
       

        $response = $this->getResponse($user, $brand->id, $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus'
         ]);
    }

    public function test_403_returned_if_attempting_to_update_brand_that_user_does_not_have_rights_to()
    {
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupBrand($user->id);

        $another_user = $this->setupAuthUser();
        $another_brand = $this->setupBrand($another_user->id);

        $response = $this->getResponse($user, $another_brand->id, ['_token' => csrf_token()]);

        $response->assertStatus(403);
    }
    public function test_attempting_to_update_non_existent_brand_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $brand_id = uniqid();
        $response = $this->getResponse($user, $brand_id, ['_token' => csrf_token()]);
        $response->assertStatus(404);
    }
}
