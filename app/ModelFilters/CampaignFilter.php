<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;

class CampaignFilter extends ModelFilter
{

    public function brand($brand_id)
    {
        return $this->whereIn('brand_id', $brand_id);
    }

    public function belongsTo($belongs_to)
    {
        return $this->where('belongs_to', $belongs_to);
    }

    public function status($status)
    {
        return $this->where('status', $status);
    }
}
