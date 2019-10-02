<?php

namespace Vanguard\Models;
use EloquentFilter\Filterable;

class MpsProfile extends Base
{
    use Filterable;

    protected $fillable = [
        'ext_profile_id', 'wave', 'age', 'gender', 'region', 'state', 'social_class', 'pop_weight', 'created_at'
    ];

    public function modelFilter()
    {
        return $this->provideFilter(\Vanguard\ModelFilters\MpsProfileFilter::class);
    }

    /**
     * Get program activities associated with the mps profile record.
     */
    public function activities()
    {
        return $this->hasMany(MpsProfileActivity::class, 'ext_profile_id', 'ext_profile_id');
    }
}