<?php

namespace Vanguard\Models;

use EloquentFilter\Filterable;

class TvStation extends Base
{
    use Filterable;

    protected $fillable = ['name', 'publisher_id', 'type', 'state', 'city', 'region', 'broadcast'];

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Vanguard\ModelFilters\TvStationFilter::class);
    }

    public function programs()
    {
        return $this->hasMany(MediaPlanProgram::class, 'station_id', 'id');
    }

}