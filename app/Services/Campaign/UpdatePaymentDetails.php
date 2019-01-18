<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Utilities;

class UpdatePaymentDetails
{
    protected $payment_id;
    protected $payment_method;

    public function __construct($payment_id, $payment_method)
    {
        $this->payment_id = $payment_id;
        $this->payment_method = $payment_method;
    }

    public function updatePaymentDetails()
    {
        return Utilities::switch_db('api')->table('paymentDetails')
                            ->where('payment_id', $this->payment_id)
                            ->update(['payment_method' => $this->payment_method, 'payment_status' => 1]);

    }
}
