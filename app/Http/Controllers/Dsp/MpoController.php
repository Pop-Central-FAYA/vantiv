<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Mail;
use Vanguard\Http\Requests\StoreMpoShareLinkRequest;
use Vanguard\Models\CampaignMpo;
use Vanguard\Services\Mpo\StoreMpoShareLink;
use Vanguard\Http\Resources\MpoShareLinkResource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Exports\MpoExport;
use Vanguard\Models\Campaign;
use Vanguard\Services\Mpo\MpoDetailsService;
use Vanguard\Http\Requests\GenerateCampaignMpoRequest;
use Vanguard\Http\Resources\CampaignMpoResource;
use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Services\Mpo\StoreMpoService;

class MpoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create.campaign')->only(['getActiveLink', 'store', 'submitToVendor']);
    }

    public function list($campaign_id)
    {
        $mpos = CampaignMpo::with('vendor.contacts')->where('campaign_id', $campaign_id)
                            ->where('ad_vendor_id', '<>', '')
                            ->latest()->get()
                            ->unique('ad_vendor_id');
        return CampaignMpoResource::collection($mpos);
    }

    public function generateMpo(GenerateCampaignMpoRequest $request, $campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        $this->authorize('update', $campaign);
        $validated = $request->validated();

        //generate mpo
        (new StoreMpoService($validated, $campaign_id))->run();
        return $this->list($campaign_id);   
    }

    public function details($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('details', $mpo);

        $formatted_mpo = (new MpoDetailsService($mpo_id))->run();
        return view('agency.mpo.details')->with('mpo', $formatted_mpo);
    }

    public function exportMpoAsExcel($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('details', $mpo);
        
        $export_name = str_slug($mpo->campaign->name).'_'.str_slug($mpo->vendor->name);
        $formatted_mpo = (new MpoDetailsService($mpo_id))->run();
        return Excel::download(new MpoExport($formatted_mpo),$export_name.'.xlsx');
    }

    public function getActiveLink($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $share_link = $mpo->active_share_link;
        if($share_link != null){
            return new MpoShareLinkResource($share_link);
        }
    }

    public function storeLink(Request $request, CampaignMpo $mpo, $mpo_id)
    {
        try {
            $campaign = $mpo->getCampaign($mpo_id);
            $share_link = (new StoreMpoShareLink($mpo_id, $campaign->stop_date))->run();
            return response()->json([
                'status' => 'success',
                'message' => 'Mpo link generated',
                'data' => new MpoShareLinkResource($share_link)
            ], 201);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ], 500);
        }
    }

    public function submitToVendor(StoreMpoShareLinkRequest $request, $mpo_id)
    {
        $validated = $request->validated();
        $campaign_mpo = CampaignMpo::findOrFail($mpo_id);
        $campaign_name = $campaign_mpo->campaign->name;
        try {
            $this->sendVendorMail($validated['url'], $validated['email'], $campaign_name);
            $this->updateMpo($campaign_mpo);
            return response()->json([
                'status' => 'success',
                'message' => 'Link submitted to vendor',
            ], 200);
        }catch(\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ], 500);
        }
    }
    
    private function updateMpo($campaign_mpo) 
    {
        if($campaign_mpo->status === MpoStatus::ACCEPTED){
            return;
        }
        $campaign_mpo->status = MpoStatus::SUBMITTED;
        $campaign_mpo->submitted_by = Auth::user()->id;
        $campaign_mpo->submitted_at = Carbon::now();
        $campaign_mpo->save(); 
    }

    private function sendVendorMail($url, $email, $campaign_name)
    {
        $user = Auth::user();
        $company = $user->companies->first()->name;
        Mail::send('mail.vendor_mpo_mail', $this->emailData($url, $user, $company, $campaign_name), 
            function ($message) use($user, $email, $company) {
                $message->from($user->email, $user->full_name);
                $message->subject('MPO from '.$company);
                $message->to($email);
                $message->replyTo($user->email);
            }
        );
    }

    private function emailData($url, $user, $company, $campaign_name)
    {
        return [
            'mpo_url' => $url,
            'user' => $user,
            'company_name' => $company,
            'campaign_name' => $campaign_name
        ];
    }
}