<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Services\Traits\CampaignQueryTrait;

class CampaignDetailsService
{
    use CampaignQueryTrait;

    protected $campaign_id;

    public function __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }

    public function campaignDetails()
    {
        $company_type = \Auth::user()->company_type;
        $company_id = \Auth::user()->company_id;
        return $this->campaignBaseQuery()
                    ->when($company_type == CompanyTypeName::BROADCASTER, function ($query) use ($company_id) {
                        $query->where('campaignDetails.campaign_id', $this->campaign_id)
                             ->whereIn('campaignDetails.launched_on', $company_id);
                    })
                    ->when($company_type == CompanyTypeName::AGENCY, function ($query) {
                        return $query->where('campaignDetails.campaign_id', $this->campaign_id)
                                    ->groupBy('campaignDetails.campaign_id');
                    })
                    ->get();
    }
}
