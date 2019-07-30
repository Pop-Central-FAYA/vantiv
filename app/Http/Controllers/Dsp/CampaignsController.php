<?php

namespace Vanguard\Http\Controllers\Dsp;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\CampaignInformationUpdateRequest;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\PreselectedAdslot;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Models\Upload;
use Vanguard\Models\Campaign;
use Vanguard\Services\Campaign\MediaMix;
use Vanguard\Services\Campaign\CampaignBudgetGraph;
use Vanguard\Services\Campaign\CampaignDetails;
use Session;
use Vanguard\Services\Compliance\ComplianceGraph;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\CampaignMpoTimeBelt;
use Illuminate\Support\Facades\DB;

use Vanguard\Services\Mpo\ExportCampaignMpo;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Exports\MpoExport;
use Vanguard\Services\Mpo\ExportCampaignMpoSummary;
use Vanguard\Services\Mpo\GetCampaignMpo;
use PhpOffice\PhpSpreadsheet\Chart\Exception;
use Vanguard\Services\Walkin\WalkInLists;
use Vanguard\Services\MediaAsset\GetMediaAssetByClient;
use Vanguard\Services\Traits\SplitTimeRange;
use Vanguard\Http\Requests\UpdateMpoTimeBeltRequest;
use Vanguard\Services\Mpo\DeleteMpoTimeBelt;
use Vanguard\Services\Mpo\CreateMpoTimeBelt;
use Vanguard\Services\Client\AllClient;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\Mpo\UpdateCampaignMpoTimeBelt;

class CampaignsController extends Controller
{
    private $campaign_dates, $utilities;

    use SplitTimeRange;
    use CompanyIdTrait;

    public function __construct(CampaignDate $campaignDate, Utilities $utilities)
    {
        $this->campaign_dates = $campaignDate;
        $this->utilities = $utilities;
    }

    public function index(Request $request)
    {
        $agency_id = $this->companyId();
        $campaigns = Campaign::when($request->status, function ($query) use ($request){
                                $query->where('status', $request->status);
                            })
                            ->where('belongs_to',$agency_id)
                            ->get();
        $campaigns = $this->reformatCampaignList($campaigns);
        return view('agency.campaigns.index')->with('campaigns', $campaigns);
    }

    public function reformatCampaignList($campaigns)
    {
        $new_campaigns = [];
        foreach ($campaigns as $campaign) {
            $new_campaigns[] = [
                'id' => $campaign->campaign_reference,
                'campaign_id' => $campaign->id,
                'name' => $campaign->name,
                'product' => $campaign->product,
                'brand' => ucfirst($campaign->brand['name']),
                'date_created' => date('M j, Y', strtotime($campaign->time_created)),
                'start_date' => date('M j, Y', strtotime($campaign->start_date)),
                'end_date' => date('Y-m-d', strtotime($campaign->stop_date)),
                'adslots' => $campaign->ad_slots,
                'budget' => number_format($campaign->budget,2),
                'status' => $this->getCampaignStatusHtml($campaign),
                'redirect_url' => $this->generateRedirectUrl($campaign),
                'station' => ''
            ];
        }
        return $new_campaigns;
    }

    public function getCampaignStatusHtml($campaign)
    {
        // To do refactor this and push the rendering logic to vue frontend
        if ($campaign->status === "active") {
            return '<span class="span_state status_success">Active</span>';
        } elseif ($campaign->status === "expired") {
            return '<span class="span_state status_danger">Finished</span>';
        } elseif ($campaign->status === "pending") {
            return '<span class="span_state status_pending">Pending</span>';
        } elseif ($campaign->status === "on_hold") {
            return '<span class="span_state status_on_hold">On Hold</span>';
        } else {
            return '<span class="span_state status_danger">File Error</span>';
        }
    }

    public function generateRedirectUrl($campaign)
    {
        if ($campaign->status === "pending" || $campaign->status === "on_hold") {
            return route('agency.campaign.new.details', ['id' => $campaign->id]);
        } else {
            return route('agency.campaign.all');
        }
    }

    public function getDetails($id)
    {
        //we will need to remove this later
        $agency_id = $this->companyId();
        $campaign_details = Utilities::campaignDetails($id, null, $agency_id);
        $user_id = $campaign_details['campaign_det']['company_user_id'];
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where agency_id = '$agency_id'");
        return view('agency.campaigns.campaign_details', compact('campaign_details', 'all_campaigns', 'all_clients'));
    }

    public function getNewDetails($id)
    {
        $agency_id = $this->companyId();
        $campaign_details_service = new CampaignDetails($id);
        $campaign_details = $campaign_details_service->run();
        $campaign_details_client_id = $campaign_details->client->id;
        $campaign_details_brand_id = $campaign_details->brand->id;
        $all_campaigns = Campaign::where('belongs_to', $agency_id)->get();
        $all_clients = (new WalkInLists($agency_id))->getWalkInListWithMinmalDetails();
        $client_media_assets = (new GetMediaAssetByClient($campaign_details_client_id, $campaign_details_brand_id))->run();
        $campaign_files = CampaignMpoTimeBelt::with(['media_asset'])->whereNotNull('asset_id')->whereHas('campaign_mpo', function ($query) use ($id){
                $query->whereHas('campaign', function ($query) use ($id) {
                    $query->where('id', $id);
                });
            })->get();
        $campaign_files = $campaign_files->groupBy('asset_id');
        return view('agency.campaigns.new_campaign_details', compact('campaign_details', 'all_campaigns', 'all_clients', 'client_media_assets', 'campaign_files'));
    }

    public function getCampaignsByClient($client_id)
    {
        $agency_id = $this->companyId();
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaigns where belongs_to = '$agency_id' and walkin_id = '$client_id' GROUP BY id");
        return (['campaign' => $all_campaigns]);
    }

    public function filterByUser($user_id)
    {
        $agency_id = $this->companyId();
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");

        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where user_id = '$user_id')");
        return (['campaign' => $all_campaigns, 'channel' => $media_chanel]);
    }

    public function filterByCampaignId($campaign_id)
    {
        $agency_id = $this->companyId();
        $summary = Utilities::campaignDetails($campaign_id, null, $agency_id);
        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$campaign_id')");
        return response()->json(['media_channel' => $media_chanel, 'summary' => $summary]);
    }

    public function mpoDetails($id)
    {
        $agency_id = $this->companyId();
        $mpo_details = Utilities::getMpoDetails($id, $agency_id);

        return view('agency.mpo.mpo')->with('mpo_details', $mpo_details);
    }

    public function getMediaChannel($campaign_id)
    {
        $channel = request()->channel;
        $broadcaster_retain = request()->media_channel;
        if($channel){
            $all_company_channels_service = new MediaMix($campaign_id, $channel, $broadcaster_retain);

            return response()->json(['all_channel' => $all_company_channels_service->getAllCompanyWithChannelInCampaign(),
                                    'media_mix' => $all_company_channels_service->getMediaMixData(),
                                    'retained_channel' => !empty($broadcaster_retain) ? $all_company_channels_service->getRetainedCompany() : null]);
        }else{
            return null;
        }
    }

    public function campaignBudgetGraph()
    {
        $campaign_id = request()->campaign_id;
        $media_publishers = request()->channel;
        if($media_publishers){
            $compliance_graph_service = new CampaignBudgetGraph($campaign_id, $media_publishers);
            return $compliance_graph_service->getCampaignBudgetData();
        }else{
            return null;
        }

    }

    public function complianceGraph()
    {
        $campaign_id = request()->campaign_id;
        $start_date = date('Y-m-d', strtotime(request()->start_date));
        $stop_date = date('Y-m-d', strtotime(request()->stop_date));
        $publisher_id = request()->media_channel;

        $compliance_graph_service = new ComplianceGraph($campaign_id, $start_date, $stop_date, $publisher_id);

        return response()->json(['date' => $compliance_graph_service->getComplianceDates(),
                                'data' => $compliance_graph_service->formatDataForGraphCompatibility()]);
    }

    public function campaignMpoDetails($campaign_mpo_id)
    {
        $campaign_mpo = CampaignMpo::find($campaign_mpo_id);
        $campaign_details_service = new CampaignDetails($campaign_mpo->campaign_id);
        $campaign_details = $campaign_details_service->run();
        $assets = (new GetMediaAssetByClient($campaign_mpo->campaign->walkin_id, $campaign_mpo->campaign->brand_id))->run();
        $time_belts = $this->splitTimeRangeByBase('00:00:00', '23:59:59', '15');
        return view('agency.campaigns.view_adslots')->with('campaign_mpo', $campaign_mpo)
                                                    ->with('assets', $assets)
                                                    ->with('campaign_details', $campaign_details)
                                                    ->with('time_belts', $time_belts);
    }

    public function exportMpoAsExcel($campaign_mpo_id)
    {
        $mpo_details = CampaignMpo::with('campaign')->find($campaign_mpo_id);
        $campaign_mpo_time_belts = DB::table('campaign_mpo_time_belts')->select(DB::raw("*,
                                                        DATE_FORMAT(playout_date, '%Y-%m') AS month,
                                                        DATE_FORMAT(playout_date, '%d') AS day_number"))
                                                        ->where('mpo_id', $campaign_mpo_id)
                                                        ->get()
                                                        ->toArray();
        $campaign_mpo_time_belts = collect($campaign_mpo_time_belts);
        $days_array = [];
        for($i = 1; $i <=31; $i++){
            $days_array[] = $i;
        }
        $mpo_time_belts = new ExportCampaignMpo(
            $campaign_mpo_time_belts->groupBy(['program', 'duration'])        
        );

        $mpo_time_belt_summary = new ExportCampaignMpoSummary($campaign_mpo_time_belts->groupBy('duration'));
        return Excel::download(new MpoExport($mpo_time_belts->run(), 
                                $days_array, 
                                $mpo_details,
                                $mpo_time_belt_summary->run()), str_slug($mpo_details->campaign->name).'.xlsx');
    }

    public function associateAssetsToMpo()
    {
        $file_durations = request()->durations;
        $media_assets = request()->assets;
        $mpo_id = request()->mpo_id;

        try {
            \DB::transaction(function () use ($file_durations, $media_assets, $mpo_id) {
                foreach ($file_durations as $key => $duration) {
                    if (array_key_exists($key, $media_assets)) {
                        CampaignMpoTimeBelt::where('mpo_id', $mpo_id)->where('duration',$duration)->update(['asset_id' => $media_assets[$key]]);
                    }
                }
            });
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => 'Something went wrong, Media asset cannot be associated with MPO.'.$ex->getMessage()
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => 'Media Assets successfully associated to MPO'
        ]);
    }

    public function deleteMultipleAdslots(Request $request, $mpo_id, CampaignMpoTimeBelt $time_belt)
    {
        try {
            (new DeleteMpoTimeBelt($request->program, $request->duration, $request->playout_date))->run();
        }catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot deleted successfully',
            'data' => $time_belt->timeBeltsByMpo($mpo_id)
        ]);
    }

    public function updateAdslots(UpdateMpoTimeBeltRequest $request, $mpo_id, 
                                CampaignMpoTimeBelt $time_belt, CampaignMpo $campaign_mpo,
                                Campaign $campaign)
    {
        try {
            DB::transaction(function() use ($request, $time_belt, $mpo_id, $campaign_mpo, $campaign) {
                //updating campaign mpo timebelts
                (new UpdateCampaignMpoTimeBelt($request))->run();
                //update campaign mpos
                $time_belt = $time_belt->timeBeltsByMpo($mpo_id);
                $total_sum = $time_belt->sum('net_total');
                $total_insertion = $time_belt->sum('ad_slots');
                $campaign_mpo = $campaign_mpo->campaignMpoDetails($mpo_id);
                $campaign_mpo->budget = $total_sum;
                $campaign_mpo->ad_slots = $total_insertion;
                $campaign_mpo->save();
                //update campaign budget
                $total_campaign_budget = $campaign_mpo->campaignByMpos($campaign_mpo->campaign_id)->sum('budget');
                $campaign = $campaign->getCampaign($campaign_mpo->campaign_id);
                $campaign->budget = $total_campaign_budget;
                $campaign->save();
            });
        }catch(Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => $time_belt->timeBeltsByMpo($mpo_id)
        ]);
    }

}
