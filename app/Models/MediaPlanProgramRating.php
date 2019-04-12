<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPlanProgramRating extends Model
{
    protected $fillable = ['program_name', 'duration', 'price', 'station'];

    public function media_plan_program()
    {
        return $this->belongsTo(MediaPlanProgram::class);
    }
}
