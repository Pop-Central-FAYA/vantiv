<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class RejectionReasonCategory extends Model
{
    protected $connection = 'api_db';
    protected $fillable = ['name'];

    public function rejection_reasons()
    {
        return $this->hasMany(RejectionReason::class);
    }
}
