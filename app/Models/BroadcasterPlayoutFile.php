<?php

namespace Vanguard\Models;

class BroadcasterPlayoutFile extends Base {

    public function broadcaster_playouts() {
        return $this->hasMany(BroadcasterPlayout::class);
    }
}
