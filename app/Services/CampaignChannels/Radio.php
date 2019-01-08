<?php

namespace Vanguard\Services\CampaignChannels;

use Vanguard\Models\CampaignChannel;

class Radio
{
    public function getRadio()
    {
        return CampaignChannel::where('status', 1)->first();
    }
}
