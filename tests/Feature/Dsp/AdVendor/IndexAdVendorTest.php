<?php

namespace Tests\Feature\Dsp\AdVendor;

use Illuminate\Support\Arr;

class IndexAdVendorTest extends AdVendorTestCase
{
    protected $route_name = 'ad-vendor.index';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.ad_vendor']);
        return $user;
    }

    protected function getResponse($user)
    {
        return $this->actingAs($user)->get(route($this->route_name));
    }

    public function test_unauthenticated_user_cannot_access_ad_vendor_index_route()
    {
        $response = $this->get(route($this->route_name));
        //it should redirect to login
        $response->assertStatus(302);
    }

    public function test_user_without_read_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(403);
    }

    public function test_user_with_read_permissions_loads_up_index_page()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $response->assertStatus(200)
                ->assertViewIs('agency.ad_vendor.index')
                ->assertViewHas(['publishers', 'vendors']);
    }

}