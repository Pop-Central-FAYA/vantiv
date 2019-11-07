<?php

namespace Tests\Feature\Dsp\MediaAsset;
use Tests\TestCase;


use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 */

class ListMediaAssets extends TestCase 
{
    protected $route_name = 'media_asset.list';

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
        $user = $this->setupAuthUser(null, ['view.asset']);
        return $user;
    }

    protected function getResponse($user, $query_params=[])
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $query_params));
    }
    public function test_unauthenticated_user_cannot_access_media_assets_list_route()
    {
        $response = $this->getJson(route($this->route_name));
        $response->assertStatus(401);
    }
    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(403);
    }

    public function test_empty_array_returned_if_no_client_exist_that_the_user_has_access_to()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $response->assertStatus(200);
    }

    public function test_vendor_list_is_always_limited_to_vendors_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $asset = $this->setupMediaAsset($user);
        $response = $this->getResponse($user);
        dd( $response );
        $this->assertTrue($response->json()['status'] == 'success');
        $response->assertStatus(200);     
    }

}