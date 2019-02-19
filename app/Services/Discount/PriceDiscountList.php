<?php

namespace Vanguard\Services\Discount;

class PriceDiscountList
{
    protected $company_id;
    protected $discount_type;

    public function __construct($company_id, $discount_type)
    {
        $this->company_id = $company_id;
        $this->discount_type = $discount_type;
    }

    public function getPriceDiscountList()
    {
        return \DB::table('discounts')
                    ->select('*')
                    ->when(is_array($this->company_id), function ($query) {
                        return $query->whereIn('broadcaster', $this->company_id);
                    })
                    ->when(!is_array($this->company_id), function ($query) {
                        return $query->where('broadcaster', $this->company_id);
                    })
                    ->where([
                        ['discount_type', $this->discount_type],
                        ['status', '1']
                    ])
                    ->get();
    }
}
