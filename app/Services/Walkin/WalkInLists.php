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
                    ->join('users', 'users.id', '=', 'walkIns.user_id')
                    ->join('brand_client', 'brand_client.client_id', '=', 'walkIns.id')
                    ->leftJoin('campaignDetails', function($query) {
                        return $query->on('campaignDetails.walkins_id', '=', 'walkIns.id')
                                    ->on('campaignDetails.brand', '=', 'brand_client.brand_id');
                    })
                    ->join('companies', 'companies.id', '=', 'campaignDetails.launched_on')
                    ->leftJoin('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                    ->leftJoin('paymentDetails', function ($query) {
                        return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                            ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
                    })

                    ->select('walkIns.company_name AS walkins_company_name', 'walkIns.company_id AS walkin_creator',
                        'walkIns.time_created', 'walkIns.company_logo', 'walkIns.user_id AS user_id', 'users.firstname',
                        'users.lastname', 'users.email', 'users.phone_number', 'walkIns.location', 'walkIns.id AS client_id')
                    ->selectRaw("
                                GROUP_CONCAT(DISTINCT companies.name) AS publishers, 
                                count(DISTINCT campaignDetails.campaign_id) AS campaign_count, 
                                SUM(paymentDetails.amount) AS total_spent_so_far,
                                count(DISTINCT brand_client.brand_id) AS total_brand,
                                SUM(CASE WHEN campaignDetails.status = 'active' THEN 1 ELSE 0 END) AS active_campaigns,
                                SUM(CASE WHEN campaignDetails.status <> 'active' THEN 1 ELSE 0 END) AS inactive_campaigns
                            ")
                    ->whereIn('walkIns.company_id', $this->company_id)
                    ->orWhereIn('campaignDetails.launched_on', $this->company_id)
                    ->groupBy('walkIns.id')
                    ->get();
    }
}
