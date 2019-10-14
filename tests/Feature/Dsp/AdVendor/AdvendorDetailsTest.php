<?php

namespace Tests\Feature\Dsp\AdVendor;

use Illuminate\Support\Arr;

class AdvendorDetailsTest extends AdVendorTestCase
{
    
    protected $route_name = 'ad-vendor.get_details';
 
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.ad_vendor']);
        return $user;
    }

    protected function getResponse($user, $id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, ['id' => $id]));
    }
    protected function setupCampaign($vendor, $user)
    {

        $campaign = factory(\Vanguard\Models\Campaign::class)->create([
            'name' => 'new campaign',
            'created_by' => $user->id
        ]);
        $mpo = factory(\Vanguard\Models\CampaignMpo::class)->create([
            'campaign_id' => $campaign->id,
            'ad_vendor_id'=> $vendor->id
        ]); 
        return $mpo->refresh();
    }
    public function test_unauthenticated_user_cannot_access_ad_vendor_details_route()
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

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $vendor_id = uniqid();
        $response = $this->getResponse($user, $vendor_id);
        $response->assertStatus(403);
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

    public function test_ad_vendor_with_full_details_is_retrieved()
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);
        $mpo = $this->setupCampaign($vendor, $user);

        $response = $this->getResponse($user, $vendor->id);
        $response->assertStatus(200);
        $actual = Arr::dot($response->json()['ad_vendor']);
        $expected = Arr::dot($vendor->toArray());

        $this->assertEquals($response->json()['mpos']['0']['id'], $mpo->id);

        $this->assertArraySubset($expected, $actual);
    }

}