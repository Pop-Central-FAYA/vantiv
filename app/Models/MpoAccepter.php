<?php

namespace Vanguard\Models;

class MpoAccepter extends Base
{
    protected $fillable = ['mpo_id', 'first_name', 'last_name', 'email'];

    /**
     * Relationship between mpo
     */
    public function mpo()
    {
        return $this->belongsTo(CampaignMpo::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
