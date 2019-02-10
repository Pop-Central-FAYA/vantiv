<?php

namespace Vanguard\Services\Adslot;

use Vanguard\Models\AdslotPrice;

class StoreAdslotPrice
{
    protected $adslot_id;
    protected $price_60;
    protected $price_45;
    protected $price_30;
    protected $price_15;

    public function __construct($adslot_id, $price_60, $price_45, $price_30, $price_15)
    {
        $this->adslot_id = $adslot_id;
        $this->price_60 = $price_60;
        $this->price_45 = $price_45;
        $this->price_30 = $price_30;
        $this->price_15 = $price_15;
    }

    public function storeAdslotPrice()
    {
        $adslot_price = new AdslotPrice();
        $adslot_price->adslot_id = $this->adslot_id;
        $adslot_price->price_60 = $this->price_60;
        $adslot_price->price_45 = $this->price_45;
        $adslot_price->price_30 = $this->price_30;
        $adslot_price->price_15 = $this->price_15;
        $adslot_price->save();
        return $adslot_price;
    }
}
