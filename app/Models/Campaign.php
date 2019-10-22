<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

use EloquentFilter\Filterable;
use Hypefactors\Laravel\Follow\Contracts\CanBeFollowedContract;
use Hypefactors\Laravel\Follow\Traits\CanBeFollowed;

class Campaign extends Base implements CanBeFollowedContract
{
    use Filterable, CanBeFollowed;
    protected $table = 'campaigns';

    protected $dates = [
        'time_created', 'time_modified', 'start_date', 'end_date'
    ];

    protected $fillable = [
        'brand_id', 'campaign_status', 'reference', 'belongs_to'
    ];

    public $timestamps = false;

    public function modelFilter()
    {
        return $this->provideFilter(\Vanguard\ModelFilters\CampaignFilter::class);
    }
    /**
     * Get client details associated with the media plan.
     */
    public function client()
    {
        return $this->belongsTo('Vanguard\Models\Client','walkin_id');
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

    /**
     * Get association with campaign time belt through the campaign id column
     */
    public function time_belts()
    {
        return $this->hasMany(CampaignTimeBelt::class);
    }
}
