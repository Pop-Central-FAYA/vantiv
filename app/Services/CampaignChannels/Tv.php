<?php

namespace Vanguard\Services\CampaignChannels;

use Vanguard\Models\CampaignChannel;

class Tv
{
    public function getTv()
    {
        return CampaignChannel::where('status', 1)->skip(1)->first();
    }
}
