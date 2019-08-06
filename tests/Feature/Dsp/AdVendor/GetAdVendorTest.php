<?php

namespace Tests\Feature\Dsp\AdVendor;

use Illuminate\Support\Arr;

class GetAdVendorTest extends AdVendorTestCase
{
    protected $route_name = 'ad-vendor.get';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.ad_vendor']);
        return $user;
    }

    protected function getResponse($user, $id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['id' => $id]));
    }

    public function test_unauthenticated_user_cannot_access_ad_vendor_get_route()
    {
        $vendor_id = uniqid();
        $response = $this->getJson(route($this->route_name, ['id' => $vendor_id]));
        $response->assertStatus(401);
    }

    public function test_attempting_to_get_non_existent_vendor_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $vendor_id = uniqid();
        $response = $this->getResponse($user, $vendor_id);
        $response->assertStatus(404);
    }

    public function test_ad_vendor_with_full_details_is_retrieved()
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);
        $another_vendor = $this->setupAdVendor($user);

        $response = $this->getResponse($user, $vendor->id);

        $response->assertStatus(200);

        $actual = Arr::dot($response->json()['data']);
        $expected = Arr::dot($vendor->with('contacts')->find($vendor->id)->toArray());
        
        $this->assertArraySubset($expected, $actual);
    }

    public function test_403_returned_if_attempting_to_get_vendor_that_user_does_not_have_rights_to()
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);

        $another_user = $this->setupAuthUser();
        $another_vendor = $this->setupAdVendor($another_user);

        $response = $this->getResponse($user, $another_vendor->id);

        $response->assertStatus(403);
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $vendor_id = uniqid();
        $response = $this->getResponse($user, $vendor_id);
        $response->assertStatus(403);
    }

    public function test_vendor_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);

        $response = $this->getResponse($user, $vendor->id);
        $response->assertStatus(200);

        $expected = [
            'links' => [
                'self' => route('ad-vendor.get', ['id' => $vendor->id], false)
            ]
        ];
        $actual = $response->json()['data'];
        $this->assertArraySubset($expected, $actual);
    }

}