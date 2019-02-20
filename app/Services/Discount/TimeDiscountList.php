<?php

namespace Vanguard\Services\Discount;

class TimeDiscountList
{
    protected $company_id;
    protected $discount_type;

    public function __construct($company_id, $discount_type)
    {
        $this->company_id = $company_id;
        $this->discount_type = $discount_type;
    }

    public function getTimeDiscount()
    {
        return \DB::table('discounts')
                    ->join('hourlyRanges', 'hourlyRanges.id', '=', 'discounts.discount_type_value')
                    ->join('companies', 'companies.id', '=', 'discounts.broadcaster')
                    ->select('discounts.*', 'hourlyRanges.time_range AS hourly_range', 'companies.name AS station')
                    ->when(is_array($this->company_id), function ($query) {
                        return $query->whereIn('discounts.broadcaster', $this->company_id);
                    })
                    ->when(!is_array($this->company_id), function ($query) {
                        return $query->where('discounts.broadcaster', $this->company_id);
                    })
                    ->where([
                        ['discounts.discount_type', $this->discount_type],
                        ['discounts.status', '1']
                    ])
                    ->get();
    }
}
