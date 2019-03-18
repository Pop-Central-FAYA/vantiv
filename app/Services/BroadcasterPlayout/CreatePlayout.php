<?php

namespace Vanguard\Services\BroadcasterPlayout;

use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus as PlayoutStatus;
use Vanguard\Libraries\Enum\BroadcasterPlayoutFileStatus as PlayoutFileStatus;
use Vanguard\Models\BroadcasterPlayoutFile as PlayoutFile;
use Vanguard\Models\BroadcasterPlayout as Playout;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Libraries\Utilities;

// TODO: We need to sanitize the file name and also have a file hash which is unique to the file
class CreatePlayout {
    protected $company_id;
    public function __construct($campaign_id, $mpo_details_id, $company_id){
        $this->campaign_id = $campaign_id;
        $this->mpo_details_id = $mpo_details_id;
        $this->company_id = $company_id;
    }



    /**
     * Given an approved mpoDetail id, create the relevant playout information
     * The following resources would need to be created
     * 1. The approved files
     * 2. The actual adslots
     * @return [type] [description]
     */
    public function run() {
        $adslot_list = $this->getAdslotsForMpo();
        $grouped_adslots = $adslot_list->groupBy('file_hash');
        return Utilities::switch_db('api')->transaction(function() use ($grouped_adslots) {
            return $this->store($grouped_adslots);
        });
    }

    protected function getAdslotsForMpo() {
        return SelectedAdslot::where([
                ['campaign_id', $this->campaign_id],
                ['broadcaster_id', $this->company_id]
            ])->get();
    }

    /**
     * Save all the files first
     * Save all the adslots associated with a file (there is a relationship there)
     * @param  [type] $adslot_list [description]
     * @return [type]              [description]
     */
    protected function store($grouped) {
        $playout_files = [];
        foreach($grouped as $file_hash => $grouped_adslots) {
            $file = $grouped_adslots->first();

            $playout_file = new PlayoutFile();
            $playout_file->file_hash = $file->file_hash;
            $playout_file->status = PlayoutFileStatus::PENDING;
            $playout_file->file_name = $file->file_name;
            $playout_file->url = $file->file_url;
            $playout_file->duration = $file->time_picked;
            $playout_file->save();

            # Save all adslots associated with this file
            $this->storePlayouts($playout_file, $grouped_adslots);

            $playout_files[] = $playout_file;
        }
        return $playout_files;
    }

    /**
     * @param  [type] $playout_file [description]
     * @param  [type] $adslot_list  [description]
     * @return [type]               [description]
     */
    protected function storePlayouts($playout_file, $adslot_list) {
        foreach($adslot_list as $selected_adslot) {
            $playout = new Playout();
            $playout->mpo_detail_id = $this->mpo_details_id;
            $playout->broadcaster_id = $selected_adslot->broadcaster_id;
            $playout->broadcaster_playout_file_id = $playout_file->id;
            $playout->selected_adslot_id = $selected_adslot->id;
            $playout->air_date = $selected_adslot->air_date;
            $playout->air_between = $selected_adslot->get_adslot->from_to_time;
            $playout->status = PlayoutStatus::PENDING;
            $playout->save();
        }
    }
}
