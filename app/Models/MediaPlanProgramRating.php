<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPlanProgramRating extends Model
{
    protected $fillable = ['media_plan_program_id', 'duration', 'price'];

    public function media_plan_program()
    {
        return $this->belongsTo(MediaPlanProgram::class);
    }
}
