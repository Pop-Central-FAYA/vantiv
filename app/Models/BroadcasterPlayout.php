<?php

namespace Vanguard\Models;

class BroadcasterPlayout extends Base {

    public function broadcaster_playout_file() {
        return $this->hasOne(BroadcasterPlayoutFile::class);
    }
}