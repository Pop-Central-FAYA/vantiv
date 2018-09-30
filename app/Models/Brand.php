<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $connection = 'api_db';
    protected $fillable = ['name', 'image_url', 'industry_code', 'sub_industry_code', 'slug'];

}
