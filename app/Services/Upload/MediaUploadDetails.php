<?php

namespace Vanguard\Services\Upload;

use Vanguard\Libraries\Utilities;

class MediaUploadDetails
{
    protected $user_id;
    protected $channel_id;

    public function __construct($user_id, $channel_id)
    {
        $this->user_id = $user_id;
        $this->channel_id = $channel_id;
    }

    public function uploadDetails()
    {
        return Utilities::switch_db('api')->table('uploads')
                            ->when($this->user_id, function ($query) {
                                return $query->where('user_id', $this->user_id);
                            })
                            ->when($this->channel_id, function($query) {
                                return $query->where('channel', $this->channel_id);
                            })
                            ->get();
    }
}
