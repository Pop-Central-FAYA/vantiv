<?php

namespace Vanguard\Services\Schedule;

use Vanguard\Models\TimeBeltTransaction;

class GetTransactionMpos
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function run()
    {
        $unique_transactions = $this->baseQuery();
        $selected_mpos = [];
        foreach($unique_transactions as $transaction) {
            $selected_mpos[] = [
                'id' => $transaction->campaign_details_id,
                'name' => $transaction->campaign_details->name
            ];
        }
        return [
            'all' => 'All',
            'selected_mpos' => $selected_mpos
        ];
    }

    public function baseQuery()
    {
        return TimeBeltTransaction::with('campaign_details')
                ->where('company_id', $this->company_id)->groupBy('campaign_details_id')->get();
    }
}