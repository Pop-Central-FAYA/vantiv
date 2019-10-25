<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;

class TvStationFilter extends ModelFilter
{
    public function publisher($publisher_id)
    {
        return $this->where('publisher_id', $publisher_id);
    }
}
