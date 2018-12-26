<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class TargetAudience extends Model
{
    protected $table = 'targetAudiences';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
}
