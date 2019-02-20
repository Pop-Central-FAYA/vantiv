<?php

namespace Vanguard\Services\Discount;

class PublisherDiscountList
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getPublisherDiscounts()
    {
        return \DB::table('discounts')
                    ->selectRaw("GROUP_CONCAT(DISTINCT broadcaster) AS publisher_id")
                    ->whereIn('broadcaster', $this->company_id)
                    ->groupBy('broadcaster')
                    ->get();
    }
}
