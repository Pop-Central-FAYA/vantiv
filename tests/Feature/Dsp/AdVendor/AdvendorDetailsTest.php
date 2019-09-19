<?php

namespace Tests\Feature\Dsp\AdVendor;

use Illuminate\Support\Arr;

class AdvendorDetailsTest extends AdVendorTestCase
{
    protected $route_name = 'ad-vendor.details';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.ad_vendor']);
        return $user;
    }

    protected function getResponse($user, $query_params=[])
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $query_params));
    }

    public function test_unauthenticated_user_cannot_access_ad_vendor_details_route()
    {
        $response = $this->getJson(route($this->route_name));
        $response->assertStatus(401);
    }

    public function test_empty_array_returned_if_no_vendors_exist_that_the_user_has_access_to()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $response->assertStatus(200)
                ->assertExactJson(['data' => []]);
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();

        $response = $this->getResponse($user);

        $response->assertStatus(403);
    }

}