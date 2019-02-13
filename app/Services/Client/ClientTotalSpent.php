<?php

namespace Vanguard\Services\Client;

class ClientTotalSpent
{
    protected $user_id;
    protected $company_id;

    public function __construct($user_id, $company_id)
    {
        $this->user_id = $user_id;
        $this->company_id = $company_id;
    }

    public function getClientTotalSpent()
    {
        return \DB::table('payments')
                        ->join('campaignDetails', 'campaignDetails.campaign_id', '=', 'payments.campaign_id')
                        ->when(!is_array($this->company_id), function($query) {
                            return $query->where('campaignDetails.belongs_to', $this->company_id);
                        })
                        ->when(is_array($this->company_id), function ($query) {
                            return $query->whereIn('campaignDetails.belongs_to', $this->company_id);
                        })
                        ->where('campaignDetails.user_id', $this->user_id)
                        ->sum('total');
    }
}
