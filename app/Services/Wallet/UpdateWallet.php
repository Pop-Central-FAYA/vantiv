<?php

namespace Vanguard\Services\Wallet;

use Vanguard\Models\Wallet;

class UpdateWallet
{
    protected $agency_id;
    protected $wallet_current_balance;
    protected $wallet_new_balance;

    public function __construct($agency_id, $wallet_current_balance, $wallet_new_balance)
    {
        $this->agency_id = $agency_id;
        $this->wallet_current_balance = $wallet_current_balance;
        $this->wallet_new_balance = $wallet_new_balance;
    }

    public function updateWallet()
    {
        $wallet = Wallet::where('user_id', $this->agency_id)->first();
        $wallet->current_balance = $this->wallet_new_balance;
        $wallet->prev_balance = $this->wallet_current_balance;
        $wallet->save();
        return $wallet;
    }
}
