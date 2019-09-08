<?php

namespace Vanguard\Models;

class MpsProfileActivity extends Base
{
    /**
     * Get profile associated with this activity.
     */
    public function profile()
    {
        return $this->belongsTo(MpsProfile::class, 'ext_profile_id', 'ext_profile_id');
    }
}