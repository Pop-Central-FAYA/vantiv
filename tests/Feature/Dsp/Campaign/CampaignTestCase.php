<?php

namespace Tests\Feature\Dsp\Campaign;

use Tests\TestCase;

class CampaignTestCase extends TestCase
{

    protected function setUpCampaign($user)
    {
        $campaign = factory(\Vanguard\Models\Campaign::class)->create([
            'belongs_to' => $user->companies->first(),
            'created_by' => $user->id
        ]);
        return $campaign->refresh();
    }

}
