<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaAsset extends Base
{
    use SoftDeletes;

    protected $table = 'media_assets';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'file_name', 'client_id', 'brand_id', 'media_type', 'asset_url', 'regulatory_cert_url', 'duration', 'company_id', 'created_by', 'updated_by'
    ];

    /**
     * Get client details associated with the media plan.
     */
    public function client()
    {
        return $this->belongsTo('Vanguard\Models\WalkIns','client_id');
    }

    /**
     * Get brand details associated with the media plan.
     */
    public function brand()
    {
        return $this->belongsTo('Vanguard\Models\Brand','brand_id');
    }

    public function campaign_mpo_time_belts()
    {
        return $this->hasMany(CampaignMpoTimeBelt::class);
    }
}
