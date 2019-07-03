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

class CampaignsController extends Controller
{
    private $campaign_dates, $utilities;

    public function __construct(CampaignDate $campaignDate, Utilities $utilities)
    {
        $this->campaign_dates = $campaignDate;
        $this->utilities = $utilities;
    }

    public function getDetails($id)
    {
        //we will need to remove this later
        $agency_id = \Session::get('agency_id');
        $campaign_details = Utilities::campaignDetails($id, null, $agency_id);
        $user_id = $campaign_details['campaign_det']['company_user_id'];
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where agency_id = '$agency_id'");
        return view('agency.campaigns.campaign_details', compact('campaign_details', 'all_campaigns', 'all_clients'));
    }

    public function getNewDetails($id)
    {
        $agency_id = \Auth::user()->companies->first()->id;
        $campaign_details_service = new CampaignDetails($id);
        $campaign_details = $campaign_details_service->run();
        $campaign_details_client_id = $campaign_details->client->id;
        $campaign_details_brand_id = $campaign_details->brand->id;
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaigns where belongs_to = '$agency_id' GROUP BY id");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where agency_id = '$agency_id'");
        $client_media_assets = Utilities::switch_db('api')->select("SELECT * FROM media_assets where client_id = '$campaign_details_client_id' AND brand_id = '$campaign_details_brand_id'");
        return view('agency.campaigns.new_campaign_details', compact('campaign_details', 'all_campaigns', 'all_clients', 'client_media_assets'));
    }

    public function getCampaignsByClient($client_id)
    {
        $agency_id = \Auth::user()->companies->first()->id;
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaigns where belongs_to = '$agency_id' and walkin_id = '$client_id' GROUP BY id");
        return (['campaign' => $all_campaigns]);
    }

    public function filterByUser($user_id)
    {
        $agency_id = \Session::get('agency_id');
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");

        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where user_id = '$user_id')");
        return (['campaign' => $all_campaigns, 'channel' => $media_chanel]);
    }

    public function filterByCampaignId($campaign_id)
    {
        $agency_id = Session::get('agency_id');
        $summary = Utilities::campaignDetails($campaign_id, null, $agency_id);
        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$campaign_id')");
        return response()->json(['media_channel' => $media_chanel, 'summary' => $summary]);
    }

    public function mpoDetails($id)
    {
        $agency_id = Session::get('agency_id');
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
        return view('agency.campaigns.view_adslots')->with('campaign_mpo', $campaign_mpo);
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
                    CampaignMpoTimeBelt::where('mpo_id', $mpo_id)->where('duration',$duration)->update(['asset_id' => $media_assets[$key]]);
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

    public function deleteMultipleAdslots(Request $request, $mpo_id)
    {
        try {
            DB::table('campaign_mpo_time_belts')->where([
                ['program', $request->program],
                ['duration', $request->duration],
                ['playout_date', $request->playout_date]
            ])->delete();
        }catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot deleted successfully',
            'data' => CampaignMpoTimeBelt::where('mpo_id', $mpo_id)->get()
        ]);
    }

}
