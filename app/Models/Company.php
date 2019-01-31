<?php

namespace Vanguard\Models;


class Company extends Base
{
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'name', 'parent_company_id', 'address', 'channel_id', 'logo'];

    public function parent_company()
    {
        return $this->belongsTo(ParentCompany::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function channels()
    {
        return $this->belongsToMany(CampaignChannel::class, 'channel_company', 'company_id', 'channel_id');
    }
}
