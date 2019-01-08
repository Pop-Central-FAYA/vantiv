<?php

namespace Vanguard\Services\Upload;

use Vanguard\Models\Upload;

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
        return Upload::where([
            ['user_id', $this->user_id],
            ['channel', $this->channel_id]
        ])->get();
    }
}
