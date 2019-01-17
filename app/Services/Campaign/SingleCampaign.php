<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Utilities;

class SingleCampaign
{
    protected $campaign_id;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($campaign_id, $broadcaster_id, $agency_id)
    {
        $this->campaign_id = $campaign_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function getSingleCampaign()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
                            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                            ->join('invoices', 'invoices.campaign_id', '=', 'campaignDetails.campaign_id')
                            ->select('campaignDetails.campaign_id', 'payments.id AS payment_id',
                                        'invoices.id AS invoice_id', 'payments.total AS total')
                            ->where([
                                ['campaignDetails.adslots', '>', 0],
                                ['campaignDetails.campaign_id', $this->campaign_id]
                            ])
                            ->when($this->broadcaster_id, function ($query) {
                                return $query->where('campaignDetails.broadcaster', $this->broadcaster_id);
                            })
                            ->when($this->agency_id, function($query) {
                                return $query->where('campaignDetails.agency', $this->agency_id);
                            })
                            ->first();
    }
}
