<?php

namespace Vanguard\Services\Adslot;

use Vanguard\Models\FilePosition;

class BroadcasterAdslotSurge
{
    protected $surge_position_id;
    protected $price;

    public function __construct($surge_position_id, $price)
    {
        $this->surge_position_id = $surge_position_id;
        $this->price = $price;
    }

    public function calculateSurge()
    {
        if((int)$this->surge_position_id != ''){
            $get_percentage = $this->getPercentageSurge();
            $percentage = $get_percentage->percentage;
            $percentage_price = (($percentage / 100) * (int)$this->price);
            $new_price = $percentage_price + (int)$this->price;
        }else{
            $new_price = (int)$this->price;
            $percentage = 0;
        }
        return (['percentage' => $percentage, 'new_price' => $new_price]);
    }

    public function getPercentageSurge()
    {
        return FilePosition::where('id', $this->surge_position_id)->get();
    }
}
