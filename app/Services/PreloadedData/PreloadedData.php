<?php

namespace Vanguard\Services;

use Vanguard\Models\CampaignChannel;
use Vanguard\Models\Day;
use Vanguard\Models\DayPart;
use Vanguard\Models\HourlyRange;
use Vanguard\Models\Sector;
use Vanguard\Models\SubSector;

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
}
