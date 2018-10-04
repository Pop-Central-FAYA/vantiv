<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $connection = 'api_db';
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function rejection_reasons()
    {
        return $this->belongsToMany(RejectionReason::class, 'file_rejection_reason','file_id', 'rejection_reason_id');
    }
}
