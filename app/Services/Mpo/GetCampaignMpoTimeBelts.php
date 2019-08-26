<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\CampaignMpoTimeBelt;
use Vanguard\Services\BaseServiceInterface;

class GetCampaignMpoTimeBelts implements BaseServiceInterface
{
    protected $mpo_id;

    public function __construct($mpo_id)
    {
        $this->mpo_id = $mpo_id;
    }

    public function run()
    {
        return CampaignMpoTimeBelt::with(['media_asset'])->whereNotNull('asset_id')
                ->whereIn('mpo_id', $this->mpo_id)
                ->get()->groupBy('asset_id');
    }
}