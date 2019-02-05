<?php

namespace Vanguard\Models;


use Vanguard\User;

class Company extends Base
{
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'name', 'parent_company_id', 'address', 'logo'];

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

    public function channels()
    {
        return $this->belongsToMany(CampaignChannel::class, 'channel_company', 'company_id', 'channel_id');
    }
}
