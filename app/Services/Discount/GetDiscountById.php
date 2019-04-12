<?php

namespace Vanguard\Services\Discount;

use Vanguard\Models\Discount;

class GetDiscountById
{
    protected $discount_id;

    public function __construct($discount_id)
    {
        $this->discount_id = $discount_id;
    }

    public function getDiscount()
    {
        return Discount::where('id', $this->discount_id)->first();
    }
}
