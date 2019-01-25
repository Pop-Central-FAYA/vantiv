<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
