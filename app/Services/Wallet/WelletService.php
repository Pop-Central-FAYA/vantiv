<?php

namespace Vanguard\Services\Wallet;

use Vanguard\Libraries\Utilities;

class WelletService
{
    protected $agency_id;

    public function __construct($agency_id)
    {
        $this->agency_id = $agency_id;
    }

    public function getCurrentBalance()
    {
        return Utilities::switch_db('api')->table('wallets')
                            ->select('current_balance')
                            ->where('user_id', $this->agency_id)
                            ->first();
    }
}
