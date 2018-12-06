<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class SubSector extends Model
{
    protected $table = 'subSectors';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'name', 'sector_id', 'sub_sector_code', 'status'
    ];
}
