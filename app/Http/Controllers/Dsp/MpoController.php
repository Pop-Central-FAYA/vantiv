<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Mail;
use Vanguard\Http\Requests\StoreMpoShareLinkRequest;
use Vanguard\Models\CampaignMpo;
use Vanguard\Services\Mpo\StoreMpoShareLink;
use Vanguard\Http\Resources\MpoShareLinkResource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use Vanguard\Exports\MpoExport;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Models\Campaign;
use Vanguard\Services\Mpo\MpoDetailsService;
use Vanguard\Http\Requests\GenerateCampaignMpoRequest;
use Vanguard\Http\Resources\CampaignMpoResource;
use Vanguard\Http\Resources\UserResource;
use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Mail\MpoActionNotification;
use Vanguard\Mail\MpoReviewNotification;
use Vanguard\Services\Mpo\StoreMpoService;
use Vanguard\Libraries\ActivityLog\LogActivity;
use Vanguard\Services\User\PermittedUser;
use Vanguard\User;

class MpoController extends Controller
{
    use CompanyIdTrait;

    protected $mpo_permissions = ['approve.mpo', 'decline.mpo'];

    public function __construct()
    {
        $this->middleware('permission:create.campaign')->only(['getActiveLink', 'store', 'submitToVendor']);
        $this->middleware(['permission:approve.mpo', 'permission:decline.mpo'])->only(['approveMpo', 'declineMpo']);
    }

    public function list($campaign_id)
    {
        $mpos = CampaignMpo::with('vendor.contacts')->where('campaign_id', $campaign_id)
                            ->where('ad_vendor_id', '<>', '')
                            ->orderBy('created_at', 'desc')
                            ->get();
        return CampaignMpoResource::collection($mpos);
    }

    public function permittedUserList($id)
    {
        $mpo = CampaignMpo::findOrFail($id);
        $this->authorize('listUsers', $mpo);

        $company_id = $this->companyId();
        $users = (new PermittedUser($company_id, $this->mpo_permissions))->run();

        return UserResource::collection($users);
    } 

    public function requestApproval(Request $request, $mpo_id)
    {
        $company_id = $this->companyId();
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('approve', $mpo);

        $reviewer = User::findOrFail($request->user_id);
        $permitted_users = (new PermittedUser($company_id, $this->mpo_permissions))->run()->pluck('id')->toArray();
        $sender = Auth::user();

        if(\in_array($reviewer->id, $permitted_users) && $mpo->status === MpoStatus::PENDING){
            DB::transaction(function() use($mpo, $reviewer, $sender) {
                $mpo->status = MpoStatus::IN_REVIEW;
                $mpo->requested_by = Auth::user()->id;
                $mpo->requested_at = Carbon::now();
                $mpo->save();

                Mail::to($reviewer->email)->send(new MpoReviewNotification($this->reviewInformation($mpo, $reviewer, $sender)));
                $logactivity = new LogActivity($mpo, "requested mpo review");
                $log = $logactivity->log();
                
            });
            return new CampaignMpoResource($mpo);
        }
    }

    public function approveMpo(Request $request, $mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('approve', $mpo);

        if($mpo->status === MpoStatus::IN_REVIEW) { //This is not the best way to do this, coming backwith a better way 
            $reciever = User::findOrFail($mpo->requested_by);
            $reviewer = Auth::user();
            DB::transaction(function() use ($mpo, $reciever, $reviewer) {
                $mpo->status = MpoStatus::APPROVED;
                $mpo->approved_by = $reviewer->id;
                $mpo->approved_at = Carbon::now();
                $mpo->save();

                Mail::to($reciever->email)
                    ->send(new MpoActionNotification($this->reviewInformation($mpo, $reciever, $reviewer, 'approved')));
                    $logactivity = new LogActivity($mpo, "approved mpo");
                    $log = $logactivity->log();
            });
            return (new MpoDetailsService($mpo_id))->run();
        }
    }

    public function declineMpo(Request $request, $mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('approve', $mpo);
        if($mpo->status === MpoStatus::IN_REVIEW) { //This is not the best way to do this, coming backwith a better way
            $reciever = User::findOrFail($mpo->requested_by);
            $reviewer = Auth::user();
            DB::transaction(function() use ($mpo, $reciever, $reviewer) {
                $mpo->status = MpoStatus::PENDING;
                $mpo->save();

                Mail::to($reciever->email)
                    ->send(new MpoActionNotification($this->reviewInformation($mpo, $reciever, $reviewer, 'declined')));
                    $logactivity = new LogActivity($mpo, "declined mpo");
                     $log = $logactivity->log();
            });
            return (new MpoDetailsService($mpo_id))->run();
        }
    }

    private function reviewInformation($mpo, $reviewer, $sender, $action = null)
    {
        return [
            'link' => route('mpos.details', ['id' => $mpo->id]),
            'reviewer' => $reviewer->full_name,
            'sender' => $sender->full_name,
            'client' => $mpo->campaign->client->name,
            'campaign' => $mpo->campaign->name,
            'action' => $action
        ];
    }

    public function vendorMpoList($campaign_id, $ad_vendor_id)
    {
        $mpos = CampaignMpo::with('vendor.contacts')
                            ->where('campaign_id', $campaign_id)
                            ->where('ad_vendor_id', $ad_vendor_id)
                            ->orderBy('created_at', 'desc')
                            ->get();
        return CampaignMpoResource::collection($mpos);
    }

    public function generateMpo(GenerateCampaignMpoRequest $request, $campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        $this->authorize('update', $campaign);
        $this->authorize('status', $campaign);
        $validated = $request->validated();

        //generate mpo
        $logactivity = new LogActivity($campaign, "generate mpo");
        $log = $logactivity->log();

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

    public function exportMpo($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('details', $mpo);
        
        $export_name = str_slug($mpo->campaign->name).'_'.str_slug($mpo->vendor->name);
        $data = (new MpoDetailsService($mpo_id))->run();
        $logactivity = new LogActivity($mpo, "exported mpo");
        $log = $logactivity->log();
        $pdf = LaravelMpdf::loadView('agency.mpo.pdf_export', compact('data'));
        return $pdf->stream($export_name.'.pdf');
    }

    public function getActiveLink($mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $share_link = $mpo->active_share_link;
        if($share_link != null){
            return new MpoShareLinkResource($share_link);
        }
    }

    public function storeLink(Request $request, $mpo_id)
    {
        $mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('share', $mpo);
        
        $share_link = (new StoreMpoShareLink($mpo_id, $mpo->campaign->stop_date))->run();
        return new MpoShareLinkResource($share_link); 
    }

    public function submitToVendor(StoreMpoShareLinkRequest $request, $mpo_id)
    {
        $validated = $request->validated();
        $campaign_mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('campaignStatus', $campaign_mpo);

        if($campaign_mpo->status === MpoStatus::APPROVED){
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