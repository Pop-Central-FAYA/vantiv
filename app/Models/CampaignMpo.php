<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Libraries\Enum\Status;

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

    public function share_links()
    {
        return $this->hasMany(MpoShareLink::class, 'mpo_id');
    }

    public function getCampaign($id)
    {
        return $this->find($id)->campaign;
    }

    public function getActiveShareLinkAttribute()
    {
        return $this->share_links->last();
    }
}
