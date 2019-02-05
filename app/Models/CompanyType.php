<?php

namespace Vanguard\Models;


class CompanyType extends Base
{
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
