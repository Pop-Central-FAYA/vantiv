<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Utilities;

class UpdateCampaignDetails
{
    protected $campaign_id;

    public function __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }

    public function updateCampaignStatus()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
                                    ->where('campaign_id', $this->campaign_id)
                                    ->update(['status' => CampaignStatus::PENDING]);
    }
}
