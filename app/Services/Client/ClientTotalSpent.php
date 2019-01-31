<?php

namespace Vanguard\Services\Client;

use Vanguard\Libraries\Utilities;

class ClientTotalSpent
{
    protected $user_id;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($user_id, $broadcaster_id, $agency_id)
    {
        $this->user_id = $user_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function getClientTotalSpent()
    {
        return Utilities::switch_db('api')->table('payments')
                        ->join('campaignDetails', 'campaignDetails.campaign_id', '=', 'payments.campaign_id')
                        ->when($this->broadcaster_id, function($query) {
                            return $query->where('campaignDetails.broadcaster', $this->broadcaster_id);
                        })
                        ->where('campaignDetails.user_id', $this->user_id)
                        ->sum('total');
    }
}
