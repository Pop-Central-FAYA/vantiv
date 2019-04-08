<?php

namespace Vanguard\Services\Traits;

trait ListDayTrait
{
    public function listDays()
    {
        return [
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
        ];
    }
}
