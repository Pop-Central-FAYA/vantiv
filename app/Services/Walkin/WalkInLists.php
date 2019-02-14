<?php

namespace Vanguard\Services\Walkin;


class WalkInLists
{
    protected $company_id;
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getWalkInList()
    {
        return \DB::table('walkIns')
                ->join('campaignDetails', 'campaignDetails.walkins_id', '=', 'walkIns.id')
                ->join('users', 'users.id', '=', 'walkIns.user_id')
                ->join('companies', 'companies.id', '=', 'campaignDetails.launched_on')
                ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                ->join('paymentDetails', function ($query) {
                    return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                        ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
                })
                ->join('brand_client', 'brand_client.client_id', '=', 'walkIns.id')
                ->join('brands', 'brands.id', '=', 'brand_client.brand_id')
                ->select('walkIns.company_name AS walkins_company_name', 'walkIns.company_id AS walkin_creator',
                    'walkIns.time_created', 'walkIns.company_logo', 'walkIns.user_id AS user_id', 'users.firstname',
                    'users.lastname', 'users.email', 'users.phone_number', 'walkIns.location', 'walkIns.id AS client_id')
                ->selectRaw("
                            GROUP_CONCAT(DISTINCT companies.name) AS publishers, 
                            count(DISTINCT campaignDetails.campaign_id) AS campaign_count, 
                            SUM(paymentDetails.amount) AS total_spent_so_far,
                            count(DISTINCT brand_client.brand_id) AS total_brand,
                            SUM(CASE WHEN campaignDetails.status = 'active' THEN 1 ELSE 0 END) AS active_campaigns,
                            SUM(CASE WHEN campaignDetails.status = 'finished' OR campaignDetails.status = 'pending' THEN 1 ELSE 0 END) AS inactive_campaigns
                        ")
                ->whereIn('campaignDetails.launched_on', $this->company_id)
                ->groupBy('campaignDetails.walkins_id')
                ->get();
    }
}
