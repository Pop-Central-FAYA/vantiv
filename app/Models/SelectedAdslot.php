<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus;
use Vanguard\Libraries\Utilities;

class SelectedAdslot extends Model
{
    protected $connection = 'api_db';
    protected $table = 'selected_adslots';
    protected $primaryKey = 'id';
    protected $keyType = 'string';


    protected $fillable = ['id', 'user_id', 'campaign_id', 'adslot', 'broadcaster_id', 'file_name', 'file_url', 'file_code','inventory_status', 'created_at',
                            'updated_at', 'status', 'agency_id', 'agency_broadcaster', 'time_picked', 'airbox_status', 'position_id', 'public_id',
                            'format', 'air_date', 'adslot_amount'];

    public function rejection_reasons()
    {
        return $this->belongsToMany(RejectionReason::class, 'adslot_reason','selected_adslot_id', 'rejection_reason_id');

    }

    public function get_adslot()
    {
        return $this->belongsTo(Adslot::class, 'adslot');
    }

    public function adslot_reasons()
    {
        return $this->hasMany(AdslotReason::class, 'selected_adslot_id', 'id');
    }

    /**
     * We need to hash the file for uniqueness sake.
     * This should probably be stored in the table itself
     * @return [type] [description]
     */
    public function getFileHashAttribute()
    {
        return md5("{$this->campaign_id}-{$this->file_url}");
    }

    public function broadcaster_playouts()
    {
        return $this->hasMany(BroadcasterPlayout::class);
    }

    public function countTotalSchedule($campaign_id, $adslot_id)
    {
        $schedule_adslot =  SelectedAdslot::where([
                                    ['campaign_id', $campaign_id],
                                    ['adslot', $adslot_id]
                                ])
                                ->count();
        return $schedule_adslot;
    }

    public function countAiredSlots ($adslot_id, $campaign_id)
    {
        $status = BroadcasterPlayoutStatus::PLAYED;

        $count_aired_spots = Utilities::switch_db('api')->table('broadcaster_playouts')
                                                            ->join('selected_adslots', function ($join) use($campaign_id, $adslot_id) {
                                                                   $join->on('selected_adslots.id', '=', 'broadcaster_playouts.selected_adslot_id')
                                                                        ->WHERE('selected_adslots.campaign_id', $campaign_id)
                                                                        ->WHERE('selected_adslots.adslot', $adslot_id);
                                                            })
                                                            ->select('broadcaster_playouts.id')
                                                            ->where('broadcaster_playouts.status', $status)
                                                            ->count();
        return $count_aired_spots;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'broadcaster_id', 'id');
    }

}
