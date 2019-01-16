<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Utilities;

class CampaignOnhold
{
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($broadcaster_id, $agency_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
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
                        ])
                        ->when($this->broadcaster_id, function($query) {
                            return $query->where([
                                ['campaignDetails.broadcaster_id', $this->broadcaster_id],
                                ['campaignDetails.agency', '']
                            ]);
                        })
                        ->when($this->agency_id, function($query) {
                            return $query->where('campaignDetails.agency', $this->agency_id)
                                        ->groupBy('campaignDetails.campaign_id');

                        })
                        ->orderBy('campaignDetails.time_created', 'DESC')
                        ->get();
    }
}
