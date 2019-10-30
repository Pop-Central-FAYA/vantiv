<?php
namespace Vanguard\Models;

use Vanguard\Models\MediaPlanSuggestion;
use Actuallymab\LaravelComment\Contracts\Commentable;
use Actuallymab\LaravelComment\HasComments;
use Hypefactors\Laravel\Follow\Contracts\CanBeFollowedContract;
use Hypefactors\Laravel\Follow\Traits\CanBeFollowed;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaPlan extends Base implements Commentable, CanBeFollowedContract
{
    use HasComments, SoftDeletes, CanBeFollowed;

    protected $fillable = [
        'budget', 'criteria_gender', 'criteria_lsm', 'criteria_social_class', 'criteria_region', 'criteria_state', 
        'criteria_age_groups', 'start_date', 'end_date', 'planner_id', 'status', 'agency_commission', 
        'campaign_name', 'client_id', 'brand_id', 'product_name', 'media_type', 'state_list', 'filters', 'gender',
        'total_insertions','gross_impressions', 'total_grp', 'net_reach', 'avg_frequency', 'net_media_cost', 'cpm', 'cpp'
    ];

    /**
     * Get suggestions associated with the media plan.
     */
    public function suggestions()
    {
        return $this->hasMany(MediaPlanSuggestion::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get brand details associated with the media plan.
     */
    public function brand()
    {
        return $this->belongsTo('Vanguard\Models\Brand','brand_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo('Vanguard\User','planner_id');
    }
}
