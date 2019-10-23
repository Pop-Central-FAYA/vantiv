<?php

namespace Tests\Feature\Feature\Dsp\Mpo;

use Tests\Feature\Dsp\Mpo\MpoTestCase;

class VendorMpoListTest extends MpoTestCase
{
    protected $route_name = 'campaign.vendors.mpos.lists';

    protected function getResponse($user, $campaign_id, $vendor_id)
    {
        return $this->actingAs($user)->getJson(route($this->route_name, 
                    ['campaign_id' => $campaign_id, 'ad_vendor_id' => $vendor_id]
                ));
    }

    public function test_only_authenticated_user_can_get_ad_vendor_mpos()
    {
        $campaign_id = uniqid();
        $ad_vendor_id = uniqid();
        $response = $this->getJson(route($this->route_name, 
                    ['campaign_id' => $campaign_id, 'ad_vendor_id' => $ad_vendor_id]
                ));
        $response->assertStatus(401);
    }

    public function test_it_get_the_vendor_mpos()
    {
        $user = $this->setupAuthUser();
        $mpo = $this->setupMpo($user);
        $response = $this->getResponse($user, $mpo->campaign_id, $mpo->ad_vendor_id);
        $response->assertStatus(200);
    }
}
