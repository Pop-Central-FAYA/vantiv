<?php

namespace Vanguard\Services\Industry;

use Vanguard\Libraries\Utilities;

class SubIndustryList
{
    public function getSubIndustryGroupByIndustry()
    {
        return Utilities::switch_db('api')->table('subSectors')
                        ->join('sectors', 'subSectors.sector_id', '=', 'sectors.sector_code')
                        ->select('subSectors.id', 'subSectors.sector_id', 'subSectors.name', 'subSectors.sub_sector_code')
                        ->get();
    }
}
