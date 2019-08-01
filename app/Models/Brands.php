<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Base
{
    protected $fillable = ['name', 'image_url', 'created_by', 'client_id', 'status'];
}
