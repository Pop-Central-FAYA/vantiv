<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Services\Traits\CampaignQueryTrait;

class CampaignStatus
{
    use CampaignQueryTrait;

    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getActiveCampaigns()
    {
        return $this->campaignBaseQuery()
                    ->when(!is_array($this->company_id), function ($query) {
                        return $query->where([
                            ['campaignDetails.status', 'active'],
                            ['campaignDetails.launched_on', $this->company_id]
                        ]);
                    })
                    ->when(is_array($this->company_id), function ($query) {
                        return $query->where('campaignDetails.status', 'active')
                                    ->whereIn('campaignDetails.launched_on', $this->company_id);
                    })
                    ->get();
    }
}
