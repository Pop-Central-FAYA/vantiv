<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Base
{
    protected $table = 'campaigns';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'campaign_status', 'reference'
    ];

    public $timestamps = false;

    /**
     * Get client details associated with the media plan.
     */
    public function client()
    {
        return $this->belongsTo('Vanguard\Models\WalkIns','walkin_id');
    }

    /**
     * Get brand details associated with the media plan.
     */
    public function brand()
    {
        return $this->belongsTo('Vanguard\Models\Brand','brand_id');
    }

    /**
     * get association with campaign mpos
     *
     * @return void
     */
    public function campaign_mpos()
    {
        return $this->hasMany(CampaignMpo::class);
    }
}
