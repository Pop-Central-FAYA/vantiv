<?php

namespace Vanguard\Http\Controllers\Guest;

use Illuminate\Http\Request;
use Vanguard\Models\MpoShareLink;
use Vanguard\Services\Mpo\StoreMpoShareLinkActivity;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Exports\MpoExport;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Mpo\AcceptRequest;
use Vanguard\Models\CampaignMpo;
use Vanguard\Services\Mpo\AcceptService;
use Vanguard\Services\Mpo\MpoDetailsService;

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
        $mpo_details = (new MpoDetailsService($campaign_mpo->id))->run();
        (new StoreMpoShareLinkActivity('Link is active', $share_link->id))->run();
        $campaign_mpo_time_belts = json_decode($campaign_mpo->adslots, true);
        $campaign_files = collect($campaign_mpo_time_belts)->where('asset_id', '<>', '')->groupBy('asset_id');
        return view('guest.mpo')->with('mpo_details', $mpo_details)
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
        $mpo = CampaignMpo::findOrFail($mpo_id);
        if (! $request->hasValidSignature()) {
            return response()->json(['error' => 'invalid'], 401);
        }
        $export_name =  str_slug($mpo->campaign->name).'_'.str_slug($mpo->vendor->name);
        $formatted_mpo = (new MpoDetailsService($mpo_id))->run();
        return Excel::download(new MpoExport($formatted_mpo),$export_name.'.xlsx');
    }

    public function acceptMpo(AcceptRequest $request, $mpo_id)
    {
        $validated = $request->validated();
        $mpo = CampaignMpo::findOrFail($mpo_id);
        (new AcceptService($validated, $mpo))->run();
    }
}