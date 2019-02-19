<?php

namespace Vanguard\Services\PreloadedData;

use Vanguard\Models\CampaignChannel;
use Vanguard\Models\Day;
use Vanguard\Models\DayPart;
use Vanguard\Models\DiscountType;
use Vanguard\Models\HourlyRange;
use Vanguard\Models\Region;
use Vanguard\Models\Sector;
use Vanguard\Models\SubSector;
use Vanguard\Models\TargetAudience;

class PreloadedData
{
    public function getDayParts()
    {
        return DayPart::all();
    }

    public function getSectors()
    {
        return Sector::orderBy('name', 'ASC');
    }

    public function getSubsectors()
    {
        return SubSector::orderBy('name', 'ASC');
    }

    public function getCampaignChannels()
    {
        return CampaignChannel::all();
    }

    public function getDays()
    {
        return Day::all();
    }

    public function getHourlyRanges()
    {
        return HourlyRange::all();
    }

    public function getTargetAudiences()
    {
        return TargetAudience::all();
    }

    public function getRegions()
    {
        return Region::all();
    }

    public function getDiscountTypes()
    {
        $discount_types = [];
        $types =  DiscountType::all();
        foreach ($types as $type){
            $key = $type->value;
            $discount_types[$key] = $type->id;
        }

        return $discount_types;
    }
}
