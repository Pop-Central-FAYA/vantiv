<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class DayPart extends Model
{
    protected $connection = 'api_db';
    protected $table = 'dayParts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function adslots()
    {
        return $this->hasMany(Adslot::class);
    }
}
