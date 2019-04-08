<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\MediaPlan;

class MediaPlanSuggestion extends Base
{
	protected $table = 'media_plan_suggestions';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['media_plan_id', 'station', 'program', 'start_time', 'end_time', 'total_audience', 'day', 'media_type'];

    /**
     * Get audience associated with the MpsAudienceProgramActivity.
     */
    public function plan()
    {
        return $this->belongsTo(MediaPlan::class);
    }
}
