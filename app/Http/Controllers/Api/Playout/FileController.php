<?php

namespace Vanguard\Http\Controllers\Api\Playout;

use Illuminate\Http\Request;
use Vanguard\Models\BroadcasterPlayoutFile as PlayoutFile;
use Vanguard\Services\BroadcasterPlayout\PlayoutFilesProvider;
use Vanguard\Libraries\Enum\BroadcasterPlayoutFileStatus as PlayoutFileStatus;

class FileController extends BaseController {

    /**
     * Get all the files for a particular broadcaster
     * #TODO add authorization and stuff, right now just manually checking that
     * # the header is a particular value
     * @return [type]
     */
    public function getFiles(Request $request) {
        if ($this->verifyThatHeaderIsCorrect($request)) {
            $status = $request->input('status');
            $provider = new PlayoutFilesProvider();
            $data_list = $provider->getAll();
            return response()->json(['data' => ['files' => $data_list]], 200);
        }
        return $this->invalidAuthentication();
    }

    public function updateDownloadStarted(Request $request, $file_hash) {
        if ($this->verifyThatHeaderIsCorrect($request)) {
            $file = PlayoutFile::where('file_hash', $file_hash)->first();
            if (!$file) {
                return $this->resourceNonExistent('file');
            }

            $file->status = PlayoutFileStatus::STARTED;
            $file->tmp_path = $request->tmp_path;
            $file->started_at = $file->freshTimestamp();
            $file->save();
            return response()->json(['data' => []], 200);
        }
        return $this->invalidAuthentication();
    }

    /**
     * Update that a file has been downloaded
     * @return [type]
     */
    public function updateDownloadFinished(Request $request, $file_hash) {
        if ($this->verifyThatHeaderIsCorrect($request)) {
            $file = PlayoutFile::where('file_hash', $file_hash)->first();
            if (!$file) {
                return $this->resourceNonExistent('file');
            }
            $file->status = PlayoutFileStatus::DOWNLOADED;
            $file->media_path = $request->media_path;
            $file->downloaded_at = $file->freshTimestamp();
            $file->save();
            return response()->json(['data' => []], 200);
        }
        return $this->invalidAuthentication();
    }

}
