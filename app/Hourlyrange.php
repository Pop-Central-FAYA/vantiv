<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Hourlyrange extends Model
{
    protected $table = 'hourlyranges';

//    public function ad_slot(){
//        return $this->belongsTo(AdSlot::class);
//    }

    public function getQueueableConnection()
    {
        // TODO: Implement getQueueableConnection() method.
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // TODO: Implement resolveRouteBinding() method.
    }
}
