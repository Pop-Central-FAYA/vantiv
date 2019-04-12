<?php

namespace Vanguard\Services\Discount;

class UpdateDiscount extends StoreDiscount
{
    protected $discount_id;

    public function __construct($name, $percentage, $company_id, $discount_id)
    {
        $this->discount_id = $discount_id;
        parent::__construct($name, $percentage, $company_id);
    }

    public function updateDiscount()
    {
        $get_discount_service = new GetDiscountById($this->discount_id);
        $discount = $get_discount_service->getDiscount();

        $discount->name = $this->name;
        $discount->percentage = $this->percentage;
        $discount->slug = str_slug($this->name);
        $discount->company_id = $this->company_id;
        $discount->save();
        return $discount;
    }

}
