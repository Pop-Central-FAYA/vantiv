<?php

namespace Vanguard\Models;


class CampaignTimeBelt extends Base
{
    protected $table = 'campaign_time_belts';
    protected $fillable = ['mpo_id', 'time_belt_start_time', 'time_belt_end_date', 'day', 
                        'duration', 'program', 'ad_slots', 'playout_date', 'asset_id', 
                        'volume_discount','net_total', 'unit_rate', 'campaign_id', 'publisher_id', 'ad_vendor_id'];

    public function campaign_mpo()
    {
        return $this->belongsTo(CampaignMpo::class, 'mpo_id', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function vendor()
    {
        return $this->belongsTo(AdVendor::class, 'ad_vendor_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
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