<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Vanguard\Libraries\AmazonS3;

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
     * Appends custom attributes model result
     *
     * @var array
     */
    protected $appends = ['expiry_url'];

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

    public function getExpiryUrlAttribute()
    {
        $url_components = parse_url($this->asset_url);
        $path_components = explode('/', $url_components['path']);
        $key = $path_components[1].'/'.$path_components[2];
        return '';
        //return AmazonS3::getPresignedUrlToReadFile($key);
    }
}
