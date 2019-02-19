<?php

namespace Vanguard\Services\Discount;

class BrandDiscountList
{
    protected $company_id;
    protected $discount_type;

    public function __construct($company_id, $discount_type)
    {
        $this->company_id = $company_id;
        $this->discount_type = $discount_type;
    }

    public function getBrandDiscountList()
    {
        return \DB::table('discounts')
                    ->join('brands', 'brands.id', '=', 'discounts.discount_type_value')
                    ->select('discounts.*', 'brands.name AS name')
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
