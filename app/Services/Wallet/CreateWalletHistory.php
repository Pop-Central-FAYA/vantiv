<?php

namespace Vanguard\Services\Wallet;

use Vanguard\Models\WalletHistory;

class CreateWalletHistory
{
    protected $agency_id;
    protected $total_amount_on_campaign;
    protected $wallet_new_balance;
    protected $wallet_current_balance;

    public function __construct($agency_id, $total_amount_on_campaign, $wallet_new_balance, $wallet_current_balance)
    {
        $this->agency_id = $agency_id;
        $this->total_amount_on_campaign = $total_amount_on_campaign;
        $this->wallet_new_balance = $wallet_new_balance;
        $this->wallet_current_balance = $wallet_current_balance;
    }

    public function createHistory()
    {
        $wallet_histories = new WalletHistory();
        $wallet_histories->id = uniqid();
        $wallet_histories->user_id = $this->agency_id;
        $wallet_histories->amount = $this->total_amount_on_campaign;
        $wallet_histories->prev_balance = $this->wallet_current_balance;
        $wallet_histories->current_balance = $this->wallet_new_balance;
        $wallet_histories->status = 1;
        $wallet_histories->save();
        return $wallet_histories;
    }
}
