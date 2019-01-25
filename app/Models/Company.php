<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\User;

class Company extends Model
{
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'parent_company_id', 'address', 'channel_id', 'logo'];

    public function parent_company()
    {
        return $this->belongsTo(ParentCompany::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function company_type()
    {
        return $this->belongsTo(CompanyType::class);
    }
}
