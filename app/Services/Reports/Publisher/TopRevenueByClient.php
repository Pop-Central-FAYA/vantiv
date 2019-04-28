<?php

namespace Vanguard\Services\Reports\Publisher;

use DB;
use Log;

class TopRevenueByClient
{
    protected $company_id_list;

    public function __construct($company_id_list)
    {
        $this->company_id_list = $company_id_list;
        return $this;
    }

    public function run()
    {
        $collection = DB::table('campaignDetails as cd')
            ->selectRaw('w.id as client_id, w.company_name as client_name, w.company_logo, SUM(tbt.amount_paid) as estimated_revenue, SUM(IF(tbt.approval_status = "approved", tbt.`amount_paid`, 0)) as actual_revenue')
            ->join('time_belt_transactions as tbt', 'tbt.campaign_details_id', '=', 'cd.id')
            ->join('walkIns as w', 'w.id', '=', 'cd.walkins_id')
            ->whereIn('cd.launched_on', $this->company_id_list)
            ->groupBy('cd.walkins_id')
            ->get();
        
        $sorted = $collection->sortByDesc('actual_revenue');
        return $sorted->first();
    }

}
