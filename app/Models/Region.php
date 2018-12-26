<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
}
