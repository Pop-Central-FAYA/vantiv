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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get brand details associated with the media plan.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function campaign_mpo_time_belts()
    {
        return $this->hasMany(CampaignMpoTimeBelt::class);
    }
}
