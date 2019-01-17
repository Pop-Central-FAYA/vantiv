<?php

namespace Vanguard\Services\FilePosition;

use Vanguard\Libraries\Utilities;

class AdslotFilePositionService
{
    protected $adslot_id;

    public function __construct($adslot_id)
    {
        $this->adslot_id = $adslot_id;
    }

    public function updateAdslotFilePosition()
    {
        return Utilities::switch_db('api')->table('adslot_filePositions')
                            ->where('adslot_id', $this->adslot_id)
                            ->update(['select_status' => 1]);
    }
}
