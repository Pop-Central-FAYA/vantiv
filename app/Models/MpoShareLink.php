<?php

namespace Vanguard\Models;

use Vanguard\Libraries\Status;
use Carbon\Carbon;

class MpoShareLink extends Base
{
    protected $fillable = ['mpo_id', 'url', 'expired_at'];

    public function share_link_activities()
    {
        return $this->hasMany(MpoShareLinkActivity::class);
    }

    public function campaign_mpo()
    {
        return $this->belongsTo(CampaignMpo::class, 'mpo_id');
    }

    public function shareLinkDetails($id)
    {
        return $this->find($id);
    }

    public function isExpired()
    {
        return Carbon::now() > $this->expired_at;
    }
}
