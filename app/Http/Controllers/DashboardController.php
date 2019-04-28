<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Libraries\Utilities;
use Auth;
use Session;
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

    public function __construct(Utilities $utilities, DataTables $dataTables)
    {
        $this->dataTables = $dataTables;
        $this->utilities = $utilities;
    }

    public function index()
    {
        //Broadcaster Dashboard module
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        /**
         * If it is a broadcaster, there are some various places the user should be redirected to
         * 1. If the User has the scheduler role (he/she should be redirected to the inventory management screen)
         * 2 If the User has the admin, super admin or media_buyer role (he/she should be redirected to the campaign management page)
         */
        if ($broadcaster_id) {
            if (Auth::user()->hasRole(array('ssp.scheduler'))) {
                $route = 'broadcaster.inventory_management';
            } else {
                // else, the user has the following roles ('ssp.admin', 'ssp.media_buyer', 'ssp.super_admin')
                $route = 'broadcaster.campaign_management';
            }
            return redirect()->route($route);

        } else if ($agency_id) {
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

    /**
     * @todo Generate year from database (like how many years active)
     */
    public function campaignManagementDashbaord(Request $request)
    {
        $campaign_list = new CampaignList($request, $this->companyId());
        $campaigns = $campaign_list->fetchAllCampaigns();

        $campaign_on_hold = $campaigns->where('status', \Vanguard\Libraries\Enum\CampaignStatus::ON_HOLD);
        $active_campaigns = $campaigns->where('status', \Vanguard\Libraries\Enum\CampaignStatus::ACTIVE_CAMPAIGN);

        $total_volume_campaign_service = new TotalVolumeCampaignChart($this->companyId());
        $company_client_service = new BroadcasterClient($this->companyId());
        $pending_invoice_service = new PendingInvoice($this->companyId());
        $client_brand_service = new CompanyBrands($this->companyId());
        $mpo_list_service = new MpoList($this->companyId(), null,null);
        $user_channels_with_other_details = $this->getChannelWithOtherDetails(Auth::user()->user_company_channels, $this->companyId());
        
        $current_year = date('Y');

        $company_id_list = $this->getCompanyIdsList();

        $monthly_filters = array('year' => $current_year);
        $data = [
            'reports_by_media_type' => $this->getReportsForPublisherDashboard($company_id_list),
            'monthly_reports' => $this->getMonthlyReport($company_id_list, $monthly_filters, 'station_revenue'),
            'volume' => $total_volume_campaign_service->totalVolumeOfCampaign()['campaign_volumes'],
            'month' => $total_volume_campaign_service->totalVolumeOfCampaign()['campaign_months'],
            'walkins' => $company_client_service->getCompanyClients(),
            'pending_invoices' => $pending_invoice_service->getPendingInvoice(),
            'brands' => $client_brand_service->getBrandCreatedByCompany(),
            'active_campaigns' => $active_campaigns,
            'pending_mpos' => $mpo_list_service->pendingMpoList(),
            'campaign_on_hold' => $campaign_on_hold,
            'user_channel_with_other_details' => $user_channels_with_other_details,
            'periodic_revenues' => Auth::user()->companies()->count() > 1 ? $this->periodicRevenueChart($this->companyId(), $current_year) : '',
            'year_list' => $this->getYearFrom2018(),
            'current_year' => $current_year,
            'stations' => $this->getCompaniesDetails($company_id_list),
            'top_media_type_revenue' => (new \Vanguard\Services\Reports\Publisher\TopRevenueByMediaType($company_id_list))->run(),
            'clients_and_brands' => (new \Vanguard\Services\Reports\Publisher\ClientsAndBrandsByMediaType($company_id_list))->run(),
            'top_revenue_by_client' => (new \Vanguard\Services\Reports\Publisher\TopRevenueByClient($company_id_list))->run()
        ];
        return view('broadcaster_module.dashboard.campaign_management.dashboard')->with($data);

    }

     /**
     * This method will return the reports for the new publisher dashboard
     * @todo make this the main reports and remove the others
     */
    protected function getReportsForPublisherDashboard($company_id_list) {
        return array(
            'campaigns' => (new \Vanguard\Services\Reports\Publisher\CampaignsByMediaType($company_id_list))->run(),
            'mpos' => (new \Vanguard\Services\Reports\Publisher\MposByMediaType($company_id_list))->run(),
            'top_media_type_revenue' => (new \Vanguard\Services\Reports\Publisher\TopRevenueByMediaType($company_id_list))->run(),
            'clients_and_brands' => (new \Vanguard\Services\Reports\Publisher\ClientsAndBrandsByMediaType($company_id_list))->run()
        );
    }

    protected function getMonthlyReport($company_id_list, $filters, $report_type) {
        switch ($report_type) {
            case 'station_revenue':
                $service = new \Vanguard\Services\Reports\Publisher\Month\StationRevenue($company_id_list);
                break;
            case 'active_campaigns':
                $service = new \Vanguard\Services\Reports\Publisher\Month\ActiveCampaigns($company_id_list);
                break;
            case 'spots_sold':
                $service = new \Vanguard\Services\Reports\Publisher\Month\SpotsSold($company_id_list);
                break;
            default:
                //default is station revenue
                $service = new \Vanguard\Services\Reports\Publisher\Month\StationRevenue($company_id_list);
                break;
        }
        $res = $service->setFilters($filters)->run();
        $res['report_type'] = $report_type;
        return $res;
    }

    /**
     * Requests to filter reports come through this method
     */
    protected function getFilteredPublisherReports(\Vanguard\Http\Requests\Publisher\DashboardReportRequest $request) {
        $validated = $request->validated();
        $company_id_list = $this->getCompanyIdsList();
        $response = array(
            'status' => 'success',
            'data' => $this->getMonthlyReport($company_id_list, $validated, $validated['report_type'])
        );
        return response()->json($response); 
    }

    public function campaignManagementFilterResult()
    {
        $companies_id = \request()->channel_id;
        $year = \request()->year;
        $company_client_service = new BroadcasterClient($companies_id);
        $pending_invoice_service = new PendingInvoice($companies_id);
        $client_brand_service = new CompanyBrands($companies_id);
        $campaign_status_service = new CampaignStatus($companies_id);
        $mpo_list_service = new MpoList($companies_id, null,null);
        $campaign_on_hold_service = new CampaignOnhold($companies_id);
        $periodic_revenues = $this->periodicRevenueChart($companies_id, $year);
        $response = [
                'walkIns' => $company_client_service->getCompanyClients(),
                'pending_invoices' => $pending_invoice_service->getPendingInvoice(),
                'brands' => $client_brand_service->getBrandCreatedByCompany(),
                'active_campaigns' => $campaign_status_service->getActiveCampaigns(),
                'pending_mpos' => $mpo_list_service->pendingMpoList(),
                'campaign_on_hold' => $campaign_on_hold_service->getCampaignsOnhold(),
                'periodic_revenues' => $periodic_revenues
        ];
        return response()->json($response);
    }

    public function filteredCampaignListTable()
    {
        $campaigns = new AllCampaign(\request(), $dashboard = true, \request()->company_id);
        return $campaigns->run();
    }

    public function periodicRevenueChart($company_id, $year)
    {
        $campaign_chart_service = new PeriodicRevenueChart($company_id, $year);
        return $campaign_chart_service->formatPeriodicChart();
    }

    public function filterPeriodicRevenueByYear()
    {
        $year = \request()->year;
        $company_id = \request()->channel_id;
        $campaign_chart_service = new PeriodicRevenueChart($company_id, $year);
        return $campaign_chart_service->formatPeriodicChart();
    }


    public function inventoryManagementDashboard()
    {
        $company_ids = $this->getCompanyIdsList();
        $timebelt_revenue_report = new RevenueByTimeBelt($company_ids, array());
        return view('broadcaster_module.dashboard.inventory_management.dashboard')
            ->with('days', $this->listDays())
            ->with('stations', $this->getCompaniesDetails($company_ids))
            ->with('day_parts', array("Late Night", "Overnight", "Breakfast", "Late Breakfast", "Afternoon", "Primetime"))
            ->with('timebelt_revenue', $timebelt_revenue_report->run());
    }

    public function getFilteredTimeBeltRevenue(Request $request) {
        $company_ids = $this->getCompanyIdsList();

        $filters = array();
        $filter_fields = array("day_parts", "day", "station_id");

        foreach ($filter_fields as $field) {
            $value = $request->input($field);
            if ($value) {
                $filters[$field] = $value;
            }
        }
        $timebelt_revenue_report = new RevenueByTimeBelt($company_ids, $filters);
        return response()->json(array("status" => "success", "data" => $timebelt_revenue_report->run()));
    }

    public function getChannelWithOtherDetails($channels_id, $companies_id)
    {
        $channels_with_other_details = [];
        foreach ($channels_id as $channel_id){
            $channels_details = new CampaignChannels($channel_id);
            $user_companies = new UserCompanyByChannel($channel_id, Auth::user()->id);
            $company_ids = $user_companies->getListOfCompanyIds();
            $campaign_status_service = new CampaignStatusPercentage($company_ids);
            $channels_with_other_details[] = [
                'channel_details' => $channels_details->getCampaignChannelsDetails(),
                'companies' => $user_companies->getListOfCompany(),
                'companies_id' => $company_ids,
                'campaign_status_percentage' => [
                    'percentage_active' => round($campaign_status_service->activePercentage()),
                    'percentage_pending' => round($campaign_status_service->pendingPercentage()),
                    'percentage_finished' => round($campaign_status_service->finishedPercentage())
                ]
            ];
        }

        return $channels_with_other_details;
    }


}
