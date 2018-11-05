<?php

namespace Vanguard\Services\BroadcasterPlayout;

use Vanguard\Libraries\Enum\BroadcasterPlayoutFileStatus as PlayoutFileStatus;
use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus as PlayoutStatus;
use Vanguard\Models\BroadcasterPlayout as Playout;
use Carbon\Carbon;
use Vanguard\Libraries\Utilities;

/**
 * TODO validate that status is in the accepted status enum list
 */
class DailyPlayoutProvider {

    public function __construct($broadcaster_id, $date){
        $this->broadcaster_id = $broadcaster_id;
        $this->date = Carbon::createFromFormat('Y-m-d', $date);
        $this->db = Utilities::switch_db('api');
    }

    protected function getPendingPlayouts() {
        $table = $this->db->table('broadcaster_playouts AS BP');
        $playout = $table->join(
            'broadcaster_playout_files AS BPF',
            'BP.broadcaster_playout_file_id',
            '=',
            'BPF.id'
        );
        $playout = $playout->where('BP.air_date', '=', $this->date->toDateString());
        $playout = $playout->where('BP.status', '=', PlayoutStatus::PENDING);
        $playout = $playout->where('BPF.status', '=', PlayoutFileStatus::DOWNLOADED);
        $playout = $playout->orderBy('BP.air_between');
        $playout = $playout->select(
            'BP.id',
            'BP.air_date',
            'BP.air_between',
            'BPF.media_path',
            'BPF.duration'
        );
        return $playout->get();
    }

    public function getAll() {
        $playout_list = $this->getPendingPlayouts();
        $playout_adblocks = $playout_list->groupBy('air_between');
        return $playout_adblocks;
    }

}
