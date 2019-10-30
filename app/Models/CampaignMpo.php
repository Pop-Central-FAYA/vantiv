<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Libraries\Enum\Status;
use Vanguard\User;

class CampaignMpo extends Base
{
    protected $table = 'campaign_mpos';
    protected $fillable = ['campaign_id', 'ad_vendor_id', 'insertions', 'net_total', 'adslots', 'reference_number'];

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

    public function vendor()
    {
        return $this->belongsTo(AdVendor::class, 'ad_vendor_id');
    }

    public function mpo_accepter()
    {
        return $this->hasOne(MpoAccepter::class, 'mpo_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
