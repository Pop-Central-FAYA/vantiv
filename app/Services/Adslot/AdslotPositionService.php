<?php

namespace Vanguard\Services\Adslot;

use Vanguard\Libraries\Utilities;
use Vanguard\Models\FilePosition;

class AdslotPositionService
{
    protected $broadcaster_id;
    protected $adslot_id;
    protected $file_position_id;

    public function __construct($broadcaster_id, $adslot_id, $file_position_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->adslot_id = $adslot_id;
        $this->file_position_id = $file_position_id;
    }

    public function reserveAdslotPosition()
    {
        $id = uniqid();
        return Utilities::switch_db('api')->table('adslot_filePositions')
                                            ->insert([
                                                'id' => $id,
                                                'adslot_id' => $this->adslot_id,
                                                'filePosition_id' => $this->file_position_id,
                                                'status' => 1,
                                                'select_status' => 0,
                                                'broadcaster_id' => $this->broadcaster_id
                                            ]);
    }

    public function checkPositionAvailability()
    {
        $file_position = Utilities::switch_db('api')->table('adslot_filePositions')
                                            ->where([
                                                ['broadcaster_id', $this->broadcaster_id],
                                                ['adslot_id', $this->adslot_id],
                                                ['filePosition_id', $this->file_position_id]
                                            ])
                                            ->first();

        if($file_position){
            return 'file_error';
        }
        return;
    }
}
