<?php

namespace Vanguard\Models;

class MpsProfile extends Base
{

    protected $fillable = [
        'id', 'ext_profile_id', 'age', 'gender', 'region', 'state', 'wave',
        'social_class', 'pop_weight', 'created_at', 'updated_at'
    ];

    /**
     * Get program activities associated with the mps profile record.
     */
    public function activities()
    {
        return $this->hasMany(MpsProfileActivity::class, 'ext_profile_id', 'ext_profile_id');
    }
}