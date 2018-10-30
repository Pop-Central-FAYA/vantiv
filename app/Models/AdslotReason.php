<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class AdslotReason extends Model
{
    protected $table = 'adslot_reason';
    protected $connection = 'api_db';

    protected $fillable = ['selected_adslot_id', 'rejection_reason_id', 'user_id', 'recommendation'];

    public function selected_adslot()
    {
        return $this->belongsTo(SelectedAdslot::class, 'selected_adslot_id', 'id', 'adslot_reason');
    }

    public function rejection_reason()
    {
        return $this->belongsTo(RejectionReason::class, 'rejection_reason_id', 'id', 'adslot_reason');
    }
}
