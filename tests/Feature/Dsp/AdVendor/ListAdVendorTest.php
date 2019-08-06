<?php

namespace Tests\Feature\Dsp\AdVendor;

use Illuminate\Support\Arr;

class ListAdVendorTest extends AdVendorTestCase
{
    protected $route_name = 'ad-vendor.list';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.ad_vendor']);
        return $user;
    }

    protected function getResponse($user, $query_params=[])
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $query_params));
    }

    public function test_unauthenticated_user_cannot_access_ad_vendor_list_route()
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

    public function test_vendor_list_with_no_filter_params_returns_all_vendors_retrievable_by_user()
    {
        $user = $this->setupUserWithPermissions();
        $ad_vendor_one = $this->setupAdVendor($user);
        $ad_vendor_two = $this->setupAdVendor($user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = Arr::sort([$ad_vendor_one->id, $ad_vendor_two->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_vendor_list_with_user_id_param_returns_relevant_list()
    {
        $user = $this->setupUserWithPermissions();
        $ad_vendor_one = $this->setupAdVendor($user);
        $ad_vendor_two = $this->setupAdVendor($user);

        //same company, different user that creates it
        $different_user = $this->setupAuthUser($user->companies->first());
        $ad_vendor_three = $this->setupAdVendor($different_user);

        $query_params = ['created_by' => $different_user->id];
        $response = $this->getResponse($user, $query_params);

        $response->assertStatus(200);

        $expected = [$ad_vendor_three->id];
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);

        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_vendor_list_is_always_limited_to_vendors_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $ad_vendor_one = $this->setupAdVendor($user);
        $ad_vendor_two = $this->setupAdVendor($user);

        //different company and user
        $different_user = $this->setupAuthUser();
        $ad_vendor_three = $this->setupAdVendor($different_user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = Arr::sort([$ad_vendor_one->id, $ad_vendor_two->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();

        $response = $this->getResponse($user);

        $response->assertStatus(403);
    }

    public function test_vendor_list_is_returned_with_links_value_for_other_actions_on_resource()
    {
        $user = $this->setupUserWithPermissions();
        $ad_vendor = $this->setupAdVendor($user);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = [
            'links' => [
                'self' => route('ad-vendor.get', ['id' => $ad_vendor->id], false)
            ]
        ];
        $actual = $response->json()['data'][0];
        $this->assertArraySubset($expected, $actual);
    }
}