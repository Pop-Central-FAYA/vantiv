<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMpoTimeBelt extends Base
{
    protected $table = 'campaign_mpo_time_belts';
    protected $fillable = ['mpo_id', 'time_belt_start_time', 'time_belt_end_date', 'day', 
                        'duration', 'program', 'ad_slots', 'playout_date', 'asset_id', 
                        'volume_discount','net_total', 'unit_rate'];

    public function campaign_mpo()
    {
        return $this->belongsTo(CampaignMpo::class, 'mpo_id', 'id');
    }

    public function media_asset()
    {
        return $this->belongsTo(MediaAsset::class, 'asset_id', 'id');
    }

    public function getTimeBeltStartTimeAttribute($value)
    {
        $split_value = explode(":", $value);
        return $split_value[0].':'.$split_value[1];
    }

    public function timeBeltsByMpo($mpo_id)
    {
        return $this->where('mpo_id', $mpo_id)->get();
    }
}