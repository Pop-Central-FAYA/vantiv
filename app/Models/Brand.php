<?php

namespace Vanguard\Models;
namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\Client;

class Brand extends Base
{
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'image_url', 'industry_code', 'sub_industry_code', 'slug','created_by', 'client_id', 'status'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
