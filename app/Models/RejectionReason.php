<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;


class RejectionReason extends Model
{
    protected $connection = 'api_db';
    protected $table = 'rejection_reasons';
    protected $fillable = ['name', 'rejection_reason_category_id'];

    public function rejection_reason_category()
    {
        return $this->belongsTo(RejectionReasonCategory::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'file_rejection_reason', 'rejection_reason_id', 'file_id');
    }
}
