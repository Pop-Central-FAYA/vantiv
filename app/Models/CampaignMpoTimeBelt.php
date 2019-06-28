<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMpoTimeBelt extends Base
{
    protected $table = 'campaign_mpo_time_belts';
    protected $fillable = ['mpo_id', 'time_belt_start_time', 'time_belt_end_date', 'day', 'duration', 'program', 'ad_slots', 'playout_date', 'asset_id'];

    public function campaign_mpo()
    {
        return $this->belongsTo(CampaignMpo::class);
    }
}