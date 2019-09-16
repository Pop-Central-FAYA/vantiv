<?php

namespace Vanguard\Http\Controllers\Dsp;

use Auth;
use Mail;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\StoreMpoShareLinkRequest;
use Vanguard\Models\CampaignMpo;
use Vanguard\Services\Mpo\StoreMpoShareLink;
use Vanguard\Http\Resources\MpoShareLinkResource;
use Illuminate\Http\Request;

class MpoShareLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create.campaign')->only(['getActiveLink', 'store', 'submitToVendor']);
    }

    public function getActiveLink($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $share_link = $mpo->active_share_link;
        if($share_link != null){
            return new MpoShareLinkResource($share_link);
        }
    }

    public function store(Request $request, CampaignMpo $mpo, $mpo_id)
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
        $cmapaign_mpo = CampaignMpo::findOrFail($mpo_id);
        $campaign_name = $cmapaign_mpo->campaign->name;
        try {
            $this->sendVendorMail($validated['url'], $validated['email'], $campaign_name);
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

    private function sendVendorMail($url, $email, $campaign_name)
    {
        $user = Auth::user();
        $company = $user->companies->first()->name;
        Mail::send('mail.vendor_mpo_mail', 
        $this->emailData($url, $user, $company, $campaign_name), function ($message) use($user, $email, $company) {
            $message->from($user->email, $user->full_name);
            $message->subject('MPO from '.$company);
            $message->to($email);
            $message->replyTo($user->email);
        });
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
