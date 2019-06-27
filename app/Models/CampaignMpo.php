<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMpo extends Base
{
    protected $table = 'campaign_mpos';
    protected $fillable = ['campaign_id', 'station', 'ad_slots'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function campaign_mpo_time_belt()
    {
        return $this->hasMany(CampaignMpoTimeBelt::class, 'mpo_id');
    }
}
