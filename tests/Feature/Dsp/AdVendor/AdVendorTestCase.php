<?php

namespace Tests\Feature\Dsp\AdVendor;

use Tests\TestCase;

class AdVendorTestCase extends TestCase
{

    protected function setupAdVendor($user)
    {
        $ad_vendor = factory(\Vanguard\Models\AdVendor::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
        factory(\Vanguard\Models\AdVendorContact::class)->create([
            'ad_vendor_id' => $ad_vendor->id,
            'created_by' => $user->id
        ]);
        return $ad_vendor->refresh();
    }

}
