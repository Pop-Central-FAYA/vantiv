<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class FilePosition extends Model
{
    protected $table = 'filePositions';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'position', 'percentage', 'broadcaster_id', 'status'
    ];
}
