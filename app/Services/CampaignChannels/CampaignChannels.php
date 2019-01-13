<?php

namespace Vanguard\Services\CampaignChannels;

use Vanguard\Models\CampaignChannel as MediaChannel;

class CampaignChannels
{
    protected $channel_id;

    public function __construct($channel_id)
    {
        $this->channel_id = $channel_id;
    }

    public function getCampaignChannelsDetails()
    {
        return MediaChannel::where('id', $this->channel_id)->first();
    }
}
