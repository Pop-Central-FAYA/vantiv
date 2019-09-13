<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    /**
     * Get association with campaign time belts table through the publisher_id column
     */
    public function time_belts()
    {
        return $this->hasMany(CampaignTimeBelt::class);
    }
}
