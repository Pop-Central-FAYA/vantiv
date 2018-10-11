<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $connection = 'api_db';
    protected $table = 'days';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function rate_card()
    {
        return $this->hasMany(RateCard::class);
    }
}
