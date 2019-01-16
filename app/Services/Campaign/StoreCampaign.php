<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Campaign;

class StoreCampaign
{
    protected $campaign_id;
    protected $now;
    protected $campaign_reference;

    public function __construct($campaign_id, $now, $campaign_reference)
    {
        $this->campaign_id = $campaign_id;
        $this->now = $now;
        $this->campaign_reference = $campaign_reference;
    }

    public function storeCampaign()
    {
        $campaign = new Campaign();
        $campaign->id = $this->campaign_id;
        $campaign->time_created = date('Y-m-d H:i:s', $this->now);
        $campaign->time_modified = date('Y-m-d H:i:s', $this->now);
        $campaign->campaign_reference = $this->campaign_reference;
        $campaign->save();
        return $campaign;
    }

}
