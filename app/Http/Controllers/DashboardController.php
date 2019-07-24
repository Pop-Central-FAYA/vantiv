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
use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignChannel;

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

        //TV
        $tv_rating = $this->agencyMediaChannel('TV');

        //Radio
        $radio_rating = $this->agencyMediaChannel('Radio');

        $today_date = date("Y-m-d");

        //all clients
        $clients = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE agency_id = '$agency_id' ORDER BY time_created DESC");

        //pending invoices
        $pending_invoices = Utilities::switch_db('api')->select("SELECT * FROM invoiceDetails where agency_id = '$agency_id' AND status = 0 GROUP BY invoice_id");

        //all_brands
        $all_brands = Utilities::getBrands($agency_id);

        //count campaigns on hold
        $campaigns_on_hold = Campaign::where('belongs_to', $agency_id)->where('status','on_hold')->get();

        //count active campaigns
        $active_campaigns = Campaign::where('belongs_to', $agency_id)->where('status','active')->get();

        //count pending media plans
        $media_plan_service = new GetMediaPlans('pending');
        $count_pending_media_plans = count($media_plan_service->run());

        //count approved media plans
        $media_plan_service = new GetMediaPlans('approved');
        $count_approved_media_plans = count($media_plan_service->run());

        //count declined media plans
        $media_plan_service = new GetMediaPlans('declined');
        $count_declined_media_plans = count($media_plan_service->run());

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
            'agency_info' => $agency_details, 
            'campaigns_on_hold' => $campaigns_on_hold,
            'count_pending_media_plans' => $count_pending_media_plans,
            'count_approved_media_plans' => $count_approved_media_plans,
            'count_declined_media_plans' => $count_declined_media_plans
        ]);

    }

    public function agencyMediaChannel($channel)
    {
        $agency_media_channels = [];

        $agency_id = Session::get('agency_id');

        $campaigns = Campaign::where('belongs_to', $agency_id)->get();

        foreach ($campaigns as $campaign){
            $channels = CampaignChannel::whereIn('id', json_decode($campaign->channel))->first();
            $agency_media_channels[] = [
                'campaign_id' => $campaign->campaign_id,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->stop_date,
                'channel' => $channels->channel,
                'channel_id' => $channels->id,
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
