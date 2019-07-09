<?php

namespace Vanguard\Models;

class Publisher extends Base
{
    protected $fillable = ['company_id', 'type', 'settings'];

    /**
     * Scope to return only publishers that belong to a list of companies
     */
    public function scopeAllowed($query, $id_list) {
        return $query->whereIn('company_id', $id_list);
    }

    public function scopeOfType($query, $type) {
        return $query->where('type', $type);
    }

    public function scopeUniqueType($query) {
        return $query->groupBy('type')->get()->pluck('type');
    }

    public function company() {
        return $this->belongsTo('Vanguard\Models\Company');
    }

    public function time_belt_transactions()
    {
        return $this->hasMany(TimeBeltTransaction::class, 'company_id', 'company_id');
    }

    public function getDecodedSettingsAttribute()
    {
        return json_decode($this->settings, true); 
    }

}
