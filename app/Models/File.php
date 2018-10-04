<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $connection = 'api_db';
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'campaign_id', 'adslot', 'broadcaster_id', 'file_name', 'file_url', 'file_code', 'is_file_accepted', 'time_created',
                            'time_modified', 'status', 'agency_id', 'agency_broadcaster', 'time_picked', 'airbox_status', 'position_id', 'public_id', 'format',
                            'start_date', 'end_date', 'recommendation'];

    public function rejection_reasons()
    {
        return $this->belongsToMany(RejectionReason::class, 'file_rejection_reason','file_id', 'rejection_reason_id');
    }
}
