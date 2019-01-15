<?php

namespace Vanguard\Services\FilePosition;

use Vanguard\Libraries\Utilities;

class AdslotFilePositionService
{
    protected $adslot_id;
    protected $broadcaster_id;

    public function __construct($adslot_id, $broadcaster_id)
    {
        $this->adslot_id = $adslot_id;
        $this->broadcaster_id = $broadcaster_id;
    }

    public function updateAdslotFilePosition()
    {
        return Utilities::switch_db('api')->table('adslot_filePositions')
                            ->where([
                                ['adslot_id', $this->adslot_id],
                                ['broadcaster_id', $this->broadcaster_id],
                            ])
                            ->update(['select_status', 1]);
    }
}
