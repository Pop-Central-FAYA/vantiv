<?php

namespace Vanguard\Services\Discount;

class DayPartDiscountList
{
    protected $company_id;
    protected $discount_type;

    public function __construct($company_id, $discount_type)
    {
        $this->company_id = $company_id;
        $this->discount_type = $discount_type;
    }

    public function getDayPartDiscountList()
    {
        return \DB::table('discounts')
                    ->join('dayParts', 'dayParts.id', '=', 'discounts.discount_type_value')
                    ->join('companies', 'companies.id', '=', 'discounts.broadcaster')
                    ->select('discounts.*', 'dayParts.day_parts AS day_part', 'companies.name AS station')
                    ->when(is_array($this->company_id), function($query) {
                        return $query->whereIn('discounts.broadcaster', $this->company_id);
                    })
                    ->when(!is_array($this->company_id), function($query) {
                        return $query->where('discounts.broadcaster', $this->company_id);
                    })
                    ->where([
                        ['discounts.discount_type', $this->discount_type],
                        ['discounts.status', '1']
                    ])
                    ->get();
    }
}
