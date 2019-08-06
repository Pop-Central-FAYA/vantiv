<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMpo extends Base
{
    protected $table = 'campaign_mpos';
    protected $fillable = ['campaign_id', 'station', 'ad_slots', 'budget'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function campaignMpoDetails($id)
    {
        return $this->find($id);
    }

    public function campaignByMpos($id)
    {
        return $this->where('campaign_id', $id)->get();
    }

    public function campaign_mpo_time_belts()
    {
        return $this->hasMany(CampaignMpoTimeBelt::class, 'mpo_id');
    }
}
