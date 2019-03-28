<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MpsAudienceProgramActivity extends Base
{
	protected $table = 'mps_audience_program_activities';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['mps_audience_id', 'media_type', 'station', 'program', 'day', 'start_time', 'end_time'];

    /**
     * Get audience associated with the MpsAudienceProgramActivity.
     */
    public function audience()
    {
        return $this->belongsTo('Vanguard\Models\MspAudience','mps_audience_id');
    }
}