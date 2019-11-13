<?php

namespace Tests\Feature\Dsp\MediaAsset;

use Tests\TestCase;


class CreateUser extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'media_asset.create';
    
    protected function getData($client, $brand)
    {
        return [
            '_token' => csrf_token(),
            'client_id' => $client,
            'brand_id' =>  $brand,
            'media_type' => 'mp4',
            'asset_url' => 'LOLOLOL',
            'regulatory_cert_url' => 'string',
            'file_name' => 'string',
            'duration' => 45
        ];
    }
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['create.asset']);
        return $user;
    }

    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }
    protected function setupClient($user)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
        return $client->refresh();
    }
    protected function setupBrand($client)
    {
        $brand = factory(\Vanguard\Models\Brand::class)->create([
            'client_id' => $client->id,
            'created_by' => $client->created_by
        ]);
        return $brand->refresh();
    }
    public function test_unauthenticated_user_cannot_access_media_asset_creation_route()
    { 
        \Session::start();
        $response = $this->postJson(route($this->route_name), ['_token' => csrf_token()]);
        $response->assertStatus(401);
    }
  public function test_user_without_update_permissions_cannot_access_route()
    {
        \Session::start();
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }

    public function test_invalid_asset_data_is_validated_on_create()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, ['_token' => csrf_token()]);
        $response->assertStatus(422);
        $keys = ['client_id', 'brand_id',  'media_type', 'asset_url', 'file_name',  'duration' ];
         $response->assertJsonValidationErrors($keys);
    }
   

    public function test_authenticated_user_can_create_asset()
    {   
        $this->markTestSkipped('must be revisited.');
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);
        $brand = $this->setupBrand($client);
        $data = $this->getData( $client->id, $brand->id);
        $response = $this->getResponse($user, $data);
        $response->assertStatus(200);
    
    }

}
