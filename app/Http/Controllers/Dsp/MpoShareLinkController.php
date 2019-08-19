<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\StoreMpoShareLinkRequest;
use Vanguard\Models\CampaignMpo;
use Vanguard\Services\Mpo\StoreMpoShareLink;
use Vanguard\Http\Resources\Dsp\MpoShareLinkResource;

class MpoShareLinkController extends Controller
{
    public function getActiveLink($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $share_link = $mpo->active_share_link;
        return new MpoShareLinkResource($share_link);
    }

    public function store(StoreMpoShareLinkRequest $request, CampaignMpo $mpo, $mpo_id)
    {
        $validated = $request->validated();
        try {
            $campaign = $mpo->getCampaign($mpo_id);
            $shared_link = (new StoreMpoShareLink($validated['email'],$mpo_id, $campaign->stop_date))->run();
        }catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Mpo link generated',
            'data' => $shared_link
        ], 201);
    }
}
