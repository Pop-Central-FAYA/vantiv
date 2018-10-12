<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class AdslotReason extends Model
{
    protected $table = 'adslot_reason';
    protected $connection = 'api_db';

    protected $fillable = ['file_id', 'rejection_reason_id', 'user_id', 'recommendation'];

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id', 'adslot_reason');
    }

    public function rejection_reason()
    {
        return $this->belongsTo(RejectionReason::class, 'rejection_reason_id', 'id', 'adslot_reason');
    }
}
