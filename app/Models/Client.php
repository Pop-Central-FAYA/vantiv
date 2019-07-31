<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Base
{
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'brand', 'image_url', 'status', 'created_by', 'company_id', 'street_address', 'city', 'state','nationality'];

}
