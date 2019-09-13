<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\MediaPlan;

class MediaPlanSuggestion extends Base
{
	protected $table = 'media_plan_suggestions';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'media_plan_id', 'station', 'program', 'start_time', 'end_time', 'total_audience', 'day', 
        'media_type', 'state_counts', 'state', 'region', 'station_type', 'rating', 'exposure_calculation',
        'status', 'material_length', 'station_id'
    ];

    /**
     * Get audience associated with the MpsAudienceProgramActivity.
     */
    public function plan()
    {
        return $this->belongsTo(MediaPlan::class);
    }

    public function media_plan_suggestion_ratings()
    {
        return $this->hasMany(MediaPlanSuggestionRating::class);
    }
}
