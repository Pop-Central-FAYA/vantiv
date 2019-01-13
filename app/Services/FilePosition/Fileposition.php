<?php

namespace Vanguard\Services\FilePosition;

use Vanguard\Models\FilePosition as Position;

class Fileposition
{
    protected $broadcaster_id;

    public function __construct($broadcaster_id)
    {
        $this->broadcaster_id = $broadcaster_id;
    }

    public function filePositionDetails()
    {
        return Position::where('broadcaster_id', $this->broadcaster_id)->get();
    }
}
