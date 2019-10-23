<?php

namespace Tests\Feature\Dsp\Mpo;

use Carbon\Carbon;
use Tests\TestCase;
use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\MediaAsset;
use Vanguard\Models\Publisher;

class MpoTestCase extends TestCase
{

    protected function setupMpo($user, $status = MpoStatus::PENDING, $requested_by = null, $approved_by = null)
    {
        $ad_vendor = factory(\Vanguard\Models\AdVendor::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);

        factory(\Vanguard\Models\AdVendorContact::class)->create([
            'ad_vendor_id' => $ad_vendor->id,
            'created_by' => $user->id
        ]);

        $campaign = factory(Campaign::class)->create([
            'created_by' => $user->id,
            'belongs_to' => $user->companies->first()->id,
            'status' => $status
        ]);

        $asset = factory(MediaAsset::class)->create([
            'client_id' => $campaign->client->id,
            'brand_id' => $campaign->brand->id,
            'company_id' => $user->companies->first()->id,
            'created_by' => $user->id
        ]);

        $publisher = factory(Publisher::class)->create();

        $adslots = $this->setUpTimeBelt($asset, $campaign, $publisher, $ad_vendor);
        
        $mpo = factory(CampaignMpo::class)->create([
            'campaign_id' => $campaign->id,
            'adslots' => json_encode($adslots),
            'ad_vendor_id' => $ad_vendor->id,
            'insertions' => count($adslots),
            'net_total' => collect($adslots)->sum('net_total'),
            'reference_number' => Utilities::generateReference(),
            'status' => $status,
            'requested_by' => $requested_by,
            'approved_by' => $approved_by
        ]);


        return $mpo->refresh();
    }

    private function setUpTimeBelt($asset, $campaign, $publisher, $ad_vendor)
    {
        return [
            [
                'id' => uniqid(),
                'time_belt_start_time' => '21:00',
                'time_belt_end_date' => '21:15',
                'day' => 'Monday',
                'program' => 'Clip and Crop',
                'ad_slots' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'playout_date' => '2019-10-14',
                'asset_id' => $asset->id,
                'volume_discount' => 10,
                'duration' => 15,
                'net_total' => 20000,
                'unit_rate' => 12000,
                'campaign_id' => $campaign->id,
                'publisher_id' => $publisher->id,
                'ad_vendor_id' => $ad_vendor->id,
                'publisher' => $publisher,
                'ad_vendors' => $ad_vendor,
                'media_asset' => $asset
            ]
        ];
    }

}
