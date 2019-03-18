<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = ['program_id', 'total_slot_available', 'total_slot_sold'];

    protected $dates = ['date_sold'];
}
