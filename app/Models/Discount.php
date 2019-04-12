<?php

namespace Vanguard\Models;

class Discount extends Base
{
    protected $fillable = ['name','percentage','slug','status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
