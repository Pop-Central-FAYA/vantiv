<?php

namespace Vanguard\Services\Upload;

use Vanguard\Models\Upload;

class MediaUploadProcessing
{
    protected $request;
    protected $user_id;
    protected $upload_id;

    public function __construct($request, $user_id, $upload_id)
    {
        $this->request = $request;
        $this->user_id = $user_id;
        $this->upload_id = $upload_id;
    }

    public function run()
    {
        return $this->processMediaUploads();
    }

    public function processMediaUploads()
    {
        $this->checkTimepickedWithMediaDuration();

        if($this->request->file_url){
            $this->checkIfUploadedForAParticularDuration();
            $this->storeMediaDetails();

            return 'success';
        }

        return;
    }

    public function checkTimepickedWithMediaDuration()
    {
        if(round((integer)$this->request->duration) > (integer)$this->request->time_picked){
            return 'error';
        }
    }

    public function checkIfUploadedForAParticularDuration()
    {
        $upload = Upload::where([
            ['time', $this->request->time],
            ['channel', $this->request->channel],
            ['user_id', $this->request->user_id]
        ])
        ->first();

        if($upload){
            return 'error_check_image';
        }
        return ;
    }

    public function storeMediaDetails()
    {
        return Upload::create([
                    'user_id' => $this->request->user_id,
                    'time' => $this->request->time_picked,
                    'file_url' => $this->request->file_url,
                    'file_name' => $this->request->time_picked.'_'.$this->request->file_format.'_'.$this->request->file_name,
                    'channel' => $this->request->channel,
                    'format' => $this->request->file_format
                ]);
    }

    public function deleteUploadedMedia()
    {
        try{
            Upload::where([
                ['id', $this->upload_id],
                ['user_id', $this->user_id]
            ])->delete();
            return 'success';
        }catch (\Exception $exception){
            return 'error';
        }

    }
}
