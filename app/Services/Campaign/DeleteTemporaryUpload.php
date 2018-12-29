<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Utilities;
use Vanguard\Models\AdslotFilePosition;
use Vanguard\Models\Upload;

class DeleteTemporaryUpload
{
    //This class deletes the preselected adslots and carts after the campaign has been created

    protected $broadcaster_id;
    protected $agency_id;
    protected $user_id;

    public function __construct($broadcaster_id, $agency_id, $user_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->user_id = $user_id;
    }

    public function run()
    {
        return Utilities::switch_db('api')->transaction(function (){
            $this->deleteAdslotFilePositions();
            $this->deletePreselectedAdslots();
            $this->deleteTemporaryUploads();
        });
    }

    public function deleteTemporaryUploads()
    {
        return Upload::where('user_id', $this->user_id)->delete();
    }

    public function deleteAdslotFilePositions()
    {
        return AdslotFilePosition::where('select_status', 0)->delete();
    }

    public function deletePreselectedAdslots()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
            ->when($this->broadcaster_id, function ($query) {
                return $query->where('broadcaster_id', $this->broadcaster_id);
            })
            ->when($this->agency_id, function($query) {
                return $query->where('agency_id', $this->agency_id);
            })
            ->delete();
    }
}
