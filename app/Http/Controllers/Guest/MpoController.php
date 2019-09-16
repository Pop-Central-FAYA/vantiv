<?php

namespace Vanguard\Http\Controllers\Guest;

use Illuminate\Http\Request;
use Vanguard\Models\MpoShareLink;
use Vanguard\Services\Mpo\StoreMpoShareLinkActivity;
use Illuminate\Support\Facades\URL;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Mpo\ExcelMpoExport;

class MpoController extends Controller
{
    public function index(Request $request, $id)
    {
        if(!$request->hasValidSignature()){
            return response()->view('guest.errors.invalid_mpo',[],403);
        }
        $share_link = MpoShareLink::findOrFail($id);
        $campaign_mpo = $share_link->campaign_mpo;
        $campaign = $campaign_mpo->campaign;
        $company = $campaign->company;
        if ($share_link->isExpired()) {
            (new StoreMpoShareLinkActivity('Link expired', $share_link->id))->run();
            return response()->view('guest.errors.expired_link', ['company_name' => $company->name], 410);
        }
        (new StoreMpoShareLinkActivity('Link is active', $share_link->id))->run();
        $campaign_mpo = $share_link->campaign_mpo;
        $campaign_mpo_time_belts = json_decode($campaign_mpo->adslots, true);
        $campaign_files = collect($campaign_mpo_time_belts)->where('asset_id', '<>', '')->groupBy('asset_id');
        return view('guest.mpo')->with('campaign_mpo', $campaign_mpo)
                                ->with('company', $company)
                                ->with('files', $campaign_files)
                                ->with('campaign', $campaign)
                                ->with('campaign_mpo_time_belts', $campaign_mpo_time_belts);
    }

    public function getTemporaryUrl($mpo_id)
    {
        return URL::temporarySignedRoute(
            'public.mpo.export', now()->addHour(1), ['id' => $mpo_id]
        );
    }

    public function export(Request $request, $mpo_id)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['error' => 'invalid'], 401);
        }
        return (new ExcelMpoExport($mpo_id))->run();
    }
}