<?php

namespace Vanguard\Services\Discount;

use Vanguard\Models\Discount;

class GetPublisherDiscountList
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getDiscountList()
    {
        return Discount::when(is_array($this->company_id), function ($query) {
                                return $query->whereIn('company_id', $this->company_id);
                            })
                        ->when(!is_array($this->company_id), function ($query) {
                            return $query->where('company_id', $this->company_id);
                        })->get();
    }
}
