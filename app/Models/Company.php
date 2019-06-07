<?php

namespace Vanguard\Models;


use Vanguard\User;
use Vanguard\Models\Ratecard\Ratecard;

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

    public function rate_cards()
    {
        return $this->hasMany(Ratecard::class);
    }

    public function time_belts()
    {
        return $this->hasMany(TimeBelt::class);
    }

    public function media_programs()
    {
        return $this->hasMany(MediaProgram::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function media_plan_volume_discounts()
    {
        return $this->hasMany(MediaPlanVolumeDiscount::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
