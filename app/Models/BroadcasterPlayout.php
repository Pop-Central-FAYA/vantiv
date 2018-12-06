<?php

namespace Vanguard\Models;

use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus;

class BroadcasterPlayout extends Base
{

    public function broadcaster_playout_file()
    {
        return $this->hasOne(BroadcasterPlayoutFile::class);
    }

    public function selected_adslot()
    {
        return $this->belongsTo(SelectedAdslot::class);
    }



}