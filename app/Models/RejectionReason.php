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

    public function selected_adslots()
    {
        return $this->belongsToMany(SelectedAdslot::class, 'file_rejection_reason', 'rejection_reason_id', 'selected_adslot_id');
    }

    public function adslot_reasons()
    {
        return $this->hasMany(AdslotReason::class, 'rejection_reason_id', 'id');
    }
}
