<?php

namespace Vanguard\Http\Controllers\Agency;

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
use Vanguard\Services\Campaign\MediaMix;
use Vanguard\Services\Campaign\CampaignBudgetGraph;
use Session;
use Vanguard\Services\Compliance\ComplianceGraph;

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
        $agency_id = \Session::get('agency_id');
        $campaign_details = Utilities::campaignDetails($id, null, $agency_id);
        $user_id = $campaign_details['campaign_det']['company_user_id'];
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where agency_id = '$agency_id'");
        return view('agency.campaigns.campaign_details', compact('campaign_details', 'all_campaigns', 'all_clients'));
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

}
