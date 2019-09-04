<?php

namespace Tests\Feature\Dsp\Campaign;

use Tests\TestCase;

class CampaignMpoTest extends TestCase
{
    protected function setUpCampaign($user)
    {
        $campaign = factory(\Vanguard\Models\Campaign::class)->create([
            'belongs_to' => $user->companies->first()->id,
            'created_by' => $user->id
        ]);
        $mpo = factory(\Vanguard\Models\CampaignMpo::class)->create([
            'campaign_id' => $campaign->id,
        ]);
        factory(\Vanguard\Models\CampaignMpoTimeBelt::class)->create([
            'mpo_id' => $mpo->id
        ]);
        return $campaign->refresh();
    }
}
