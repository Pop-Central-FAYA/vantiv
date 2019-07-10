<?php

namespace Vanguard\Services\CampaignChannels;

use Vanguard\Models\CampaignChannel as MediaChannel;

class GetChannelByName
{
    protected $channel_name;

    public function __construct($channel_name)
    {
        $this->channel_name = $channel_name;
    }

    public function getCampaignChannelsByName()
    {
        return MediaChannel::where('channel', $this->channel_name)->first();
    }
}
