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
        'brand_id', 'campaign_status', 'reference'
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
     * Get brand details associated with the media plan.
     */
    public function creator()
    {
        return $this->belongsTo('Vanguard\User','created_by');
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

    public function getCampaign($id)
    {
        return $this->find($id);
    }

    /**
     * Get association with company through the belongs_to column
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'belongs_to');
    }
}
