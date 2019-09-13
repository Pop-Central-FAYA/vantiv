<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\CampaignTimeBelt;
use Vanguard\Services\BaseServiceInterface;

class GetCampaignTimeBelt implements BaseServiceInterface
{
    protected $campaign_id;

    public function __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }

    public function run()
    {
        return CampaignTimeBelt::with(['media_asset'])
                ->whereNotNull('asset_id')
                ->where('asset_id', '<>', '')
                ->where('campaign_id', $this->campaign_id)
                ->get()->groupBy('asset_id');
    }
}