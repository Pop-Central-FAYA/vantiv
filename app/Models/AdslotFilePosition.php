<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class AdslotFilePosition extends Model
{
    protected $table = 'adslot_filePositions';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'adslot_id', 'filePosition_id', 'status', 'select_status', 'broadcaster_id'
    ];
}
