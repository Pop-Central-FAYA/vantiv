<?php

namespace Vanguard\Models;
namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\Client;
use Vanguard\Models\Campaign;

class Brand extends Base
{
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'image_url', 'industry_code', 'sub_industry_code', 'slug','created_by', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
