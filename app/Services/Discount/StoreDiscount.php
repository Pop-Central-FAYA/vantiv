<?php

namespace Vanguard\Services\Discount;

use Vanguard\Models\Discount;

class StoreDiscount
{
    protected $name;
    protected $percentage;
    protected $company_id;

    public function __construct($name, $percentage, $company_id)
    {
        $this->name = $name;
        $this->percentage = $percentage;
        $this->company_id = $company_id;
    }

    public function storeDiscount()
    {
        $discount = new Discount();
        $discount->name = $this->name;
        $discount->percentage = $this->percentage;
        $discount->slug = str_slug($discount->name);
        $discount->company_id = $this->company_id;
        $discount->save();
        return $discount;
    }
}
