<?php
namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\MediaPlanSuggestion;

class MediaPlan extends Base
{
	protected $table = 'media_plans';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['budget', 'criteria_gender', 'criteria_lsm', 'criteria_social_class', 'criteria_region', 'criteria_state', 'criteria_age_groups', 'start_date', 'end_date', 'planner_id', 'status', 'agency_commission', 'campaign_name', 'client_id', 'brand_id', 'product_name', 'media_type', 'state_list'];

    /**
     * Get suggestions associated with the media plan.
     */
    public function suggestions()
    {
        return $this->hasMany(MediaPlanSuggestion::class);
    }

    /**
     * Get client details associated with the media plan.
     */
    public function client()
    {
        return $this->belongsTo('Vanguard\Models\WalkIns','client_id');
    }
}
