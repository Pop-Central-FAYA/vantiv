<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class BrandClient extends Model
{
    protected $table = 'brand_client';
    protected $connection = 'api_db';
    protected $fillable = ['id', 'brand_id', 'client_id', 'media_buyer', 'media_buyer_id'];
}
