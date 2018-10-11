<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $connection = 'api_db';
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['id', 'user_id', 'campaign_id', 'adslot', 'broadcaster_id', 'file_name', 'file_url', 'file_code','inventory_status', 'created_at',
                            'modified_at', 'status', 'agency_id', 'agency_broadcaster', 'time_picked', 'airbox_status', 'position_id', 'public_id', 'format'];

    public function rejection_reasons()
    {
        return $this->belongsToMany(RejectionReason::class, 'adslot_reason','file_id', 'rejection_reason_id');

    }

    public function get_adslot()
    {
        return $this->belongsTo(Adslot::class, 'adslot');
    }

    public function adslot_reasons()
    {
        return $this->hasMany(AdslotReason::class, 'file_id', 'id');
    }
}
