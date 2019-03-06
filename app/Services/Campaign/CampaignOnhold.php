<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Libraries\Utilities;

class CampaignOnhold
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getCampaignsOnhold()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
                        ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                        ->join('campaigns', 'campaigns.id', '=', 'campaignDetails.campaign_id')
                        ->join('brands', 'brands.id', '=', 'campaignDetails.brand')
                        ->join('users', 'users.id', '=', 'campaignDetails.user_id')
                        ->select('campaignDetails.adslots_id', 'campaignDetails.stop_date', 'campaignDetails.start_date',
                            'campaignDetails.status', 'campaignDetails.time_created', 'campaignDetails.product', 'campaignDetails.name',
                            'campaignDetails.campaign_id', 'payments.total', 'payments.id AS payment_id', 'brands.name AS brand_name',
                            'campaignDetails.user_id AS user_id', 'users.phone_number', 'users.email', 'campaigns.campaign_reference'
                        )
                        ->selectRaw('CONCAT(users.firstname," ",users.lastname) AS full_name')
                        ->where([
                            ['campaignDetails.status', CampaignStatus::ON_HOLD],
                            ['campaignDetails.adslots', '>', 0],
                            ['campaignDetails.created_by', \Auth::user()->id]
                        ])
                        ->when(!is_array($this->company_id) && \Auth::user()->company_type == CompanyTypeName::BROADCASTER, function($query) {
                            return $query->where([
                                ['campaignDetails.broadcaster', $this->company_id],
                                ['campaignDetails.agency', '']
                            ]);
                        })
                        ->when(is_array($this->company_id) && \Auth::user()->company_type == CompanyTypeName::BROADCASTER, function($query){
                            return $query->where('campaignDetails.agency', '')
                                        ->whereIn('campaignDetails.launched_on', $this->company_id);
                        })
                        ->when(!is_array($this->company_id) && \Auth::user()->company_type == CompanyTypeName::AGENCY, function($query) {
                            return $query->where('campaignDetails.agency', $this->company_id)
                                        ->groupBy('campaignDetails.campaign_id');

                        })
                        ->orderBy('campaignDetails.time_created', 'DESC')
                        ->get();
    }
}
