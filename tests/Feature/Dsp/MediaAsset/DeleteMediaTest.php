<?php

namespace Tests\Feature\Dsp\MediaAsset;
use Tests\TestCase;


use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 */

class ListMediaAssets extends TestCase 
{
    protected $route_name = 'media_asset.delete';

    protected function setupMediaAsset($user)
    {
        $client = factory(\Vanguard\Models\MediaAsset::class)->create([
            'file_name' => 'New file.mp4',
            'created_by' => $user->id
        ]);
        return $client->refresh();
    }

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['delete.asset']);
        return $user;
    }

    protected function getResponse($user, $id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['id' => $id]));
    }

    public function test_unauthenticated_user_cannot_access_media_assets_list_route()
    {
        $response = $this->getJson(route($this->route_name, ['id' => uniqid()] ));
        $response->assertStatus(401);
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, uniqid());
        $response->assertStatus(403);
    }

    public function test_it_404_when_it_doesnot_exit()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, uniqid());
        $response->assertStatus(404);
    }
   
    public function test_vendor_list_is_always_limited_to_vendors_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $asset = $this->setupMediaAsset($user);
        $response = $this->getResponse($user, $asset->id);
        $this->assertTrue($response->json()['status'] == 'success');
        $response->assertStatus(200);    
    }

}