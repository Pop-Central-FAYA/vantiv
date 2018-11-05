<?php

namespace Vanguard\Services\BroadcasterPlayout;

use Vanguard\Libraries\Enum\BroadcasterPlayoutFileStatus as PlayoutFileStatus;
use Vanguard\Models\BroadcasterPlayoutFile as PlayoutFile;

/**
 * TODO validate that status is in the accepted status enum list
 */
class PlayoutFilesProvider {

    public function __construct($status=null){
        if (!$status) {
            $status = PlayoutFileStatus::PENDING;
        }
        $this->status = $status;

    }

    public function getAll() {
        $file_list = PlayoutFile::where('status', $this->status)->get();
        $data_list = [];
        foreach ($file_list as $file) {
            $data_list[] = [
                'file_name' => $file->file_hash . $file->file_name,
                'url' => $file->url,
                'file_hash' => $file->file_hash
            ];
        }
        return $data_list;
    }

}
