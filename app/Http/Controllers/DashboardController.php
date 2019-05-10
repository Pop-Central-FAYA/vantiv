<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Libraries\Utilities;
use Auth;
use Session;

use Vanguard\Models\Publisher;
use Vanguard\Services\Brands\CompanyBrands;
use Vanguard\Services\Campaign\AllCampaign;
use Vanguard\Services\Campaign\PeriodicRevenueChart;
use Vanguard\Services\Campaign\CampaignOnhold;
use Vanguard\Services\Campaign\CampaignStatus;
use Vanguard\Services\Campaign\CampaignStatusPercentage;
use Vanguard\Services\Campaign\TotalVolumeCampaignChart;
use Vanguard\Services\CampaignChannels\CampaignChannels;
use Vanguard\Services\Client\BroadcasterClient;
use Vanguard\Services\Company\CompanyDetailsFromIdList;
use Vanguard\Services\Company\UserCompanyByChannel;
use Vanguard\Services\Invoice\PendingInvoice;
use Vanguard\Services\Mpo\MpoList;
use Vanguard\Services\Traits\ListDayTrait;
use Vanguard\Services\Traits\YearTrait;
use Yajra\DataTables\DataTables;
use Vanguard\Services\MediaPlan\GetMediaPlans;
use Vanguard\Services\Campaign\CampaignList;

use Vanguard\Services\Reports\Publisher\RevenueByTimeBelt;

use Log;

class DashboardController extends Controller
{
    protected $utilities;
    protected $dataTables;

    use CompanyIdTrait;
    use YearTrait;
    use ListDayTrait;

    public function index()
    {
        //agency dashboard
        $allBroadcasters = Utilities::switch_db('api')->select("SELECT * from broadcasters");
        $agency_id = Session::get('agency_id');
        $agency_details = Utilities::switch_db('api')->select("SELECT * from agents where id = '$agency_id'");

//            TV
        $tv_rating = $this->agencyMediaChannel('TV');

        //Radio
        $radio_rating = $this->agencyMediaChannel('Radio');

        $today_date = date("Y-m-d");

//            all clients
        $clients = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE agency_id = '$agency_id' ORDER BY time_created DESC");

//            pending invoices
        $pending_invoices = Utilities::switch_db('api')->select("SELECT * FROM invoiceDetails where agency_id = '$agency_id' AND status = 0 GROUP BY invoice_id");

//            all_brands
        $all_brands = Utilities::getBrands($agency_id);

        //count campaign on hold
        $campaigns_on_hold = Utilities::switch_db('api')->select("SELECT id FROM campaignDetails WHERE agency = '$agency_id' 
                                                                      AND status = 'on_hold' GROUP BY campaign_id");

        $active_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' AND status = 'active' GROUP BY campaign_id");

        $media_plan_service = new GetMediaPlans();
        //count pending media plans
        $count_pending_media_plans = $media_plan_service->pendingPlans();

        //count approved media plans
        $count_approved_media_plans = $media_plan_service->approvedPlans();

        //count declined media plans
        $count_declined_media_plans = $media_plan_service->declinedPlans();

        return view('agency.dashboard.new_dashboard')->with([
            'broadcaster' => $allBroadcasters,
            'active_campaigns' => $active_campaigns,
            'all_brands' => $all_brands,
            'pending_invoices' => $pending_invoices,
            'clients' => $clients,
            'active' => $tv_rating['percentage_active'],
            'pending' => $tv_rating['percentage_pending'],
            'finished' => $tv_rating['percentage_finished'],
            'radio_rating' => $radio_rating,
            'active_radio' => $radio_rating['percentage_active'],
            'pending_radio' => $radio_rating['percentage_pending'],
            'finish_radio' => $radio_rating['percentage_finished'],
            'agency_info' => $agency_details, 'campaigns_on_hold' => $campaigns_on_hold,
            'count_pending_media_plans' => $count_pending_media_plans,
            'count_approved_media_plans' => $count_approved_media_plans,
            'count_declined_media_plans' => $count_declined_media_plans
        ]);

    }

    public function agencyMediaChannel($channel)
    {
        $agency_media_channels = [];

        $agency_id = Session::get('agency_id');

        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where broadcaster IN 
                                                      (SELECT b.id from broadcasters as b, campaignChannels as c where b.channel_id = c.id AND c.channel = '$channel')
                                                      AND agency = '$agency_id'");

        foreach ($campaigns as $campaign){
            $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($campaign->channel) AND channel = '$channel' ");
            $agency_media_channels[] = [
                'campaign_id' => $campaign->campaign_id,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->stop_date,
                'channel' => $channels[0]->channel,
                'channel_id' => $channels[0]->id,
                'campaign_status' => $campaign->status
            ];

        }

        return $this->calculateChannelPercentageOnAgency($agency_media_channels);
    }

    public function calculateChannelPercentageOnAgency($agency_media_channels)
    {
        $finished_campaigns = [];
        $active_campaigns = [];
        $pending_campaigns = [];
        $today = date("Y-m-d");
        foreach ($agency_media_channels as $agency_media_channel){
            if($agency_media_channel['campaign_status'] === 'expired'){
                $finished_campaigns[] = [
                    'campaign_id' => $agency_media_channel['campaign_id'],
                    'start_date' => $agency_media_channel['start_date'],
                    'end_date' => $agency_media_channel['end_date'],
                    'channel' => $agency_media_channel['channel'],
                    'channel_id' => $agency_media_channel['channel_id'],
                ];
            }else if($agency_media_channel['campaign_status'] === 'pending'){
                $pending_campaigns[] = [
                    'campaign_id' => $agency_media_channel['campaign_id'],
                    'start_date' => $agency_media_channel['start_date'],
                    'end_date' => $agency_media_channel['end_date'],
                    'channel' => $agency_media_channel['channel'],
                    'channel_id' => $agency_media_channel['channel_id'],
                ];
            }else {
                $active_campaigns[] = [
                    'campaign_id' => $agency_media_channel['campaign_id'],
                    'start_date' => $agency_media_channel['start_date'],
                    'end_date' => $agency_media_channel['end_date'],
                    'channel' => $agency_media_channel['channel'],
                    'channel_id' => $agency_media_channel['channel_id'],
                ];
            }
        }

        //maths to calculate the percentage values
        $total_for_active = count($active_campaigns);
        $total_pending = count($pending_campaigns);
        $total_finished = count($finished_campaigns);
        $total_campaigns_tv = count($agency_media_channels);

        //percentage values
        $percentage_active = $total_for_active != 0 ? ($total_for_active / $total_campaigns_tv) * 100 : 0;
        $percentage_finished = $total_finished != 0 ? ($total_finished / $total_campaigns_tv) * 100 : 0;
        $percentage_pending = $total_pending !=0 ? ($total_pending / $total_campaigns_tv) * 100 : 0;

        return (['percentage_active' => $percentage_active, 'percentage_finished' => $percentage_finished, 'percentage_pending' => $percentage_pending]);
    }

    public function dashboardCampaigns(Request $request)
    {
        $campaigns = new AllCampaign($request, $dashboard = true, $this->companyId());
        return $campaigns->run();
    }

    public function dashboardMediaPlans(Request $request)
    {
        $media_plan_service = new GetMediaPlans();
        return $media_plan_service->run();
    }

}
