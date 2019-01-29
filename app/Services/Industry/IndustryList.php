<?php

namespace Vanguard\Services\Industry;

use Vanguard\Models\Sector;

class IndustryList
{
    public function industryList()
    {
        return Sector::orderBy('name', 'ASC')->get();
    }
}
