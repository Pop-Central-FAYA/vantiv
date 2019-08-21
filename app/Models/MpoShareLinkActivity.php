<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MpoShareLinkActivity extends Model
{
    public function share_link()
    {
        return $this->belongsTo(MpoShareLink::class);
    }
}
