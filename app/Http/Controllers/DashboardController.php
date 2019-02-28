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
use Vanguard\Services\Company\UserCompanyByChannel;
use Vanguard\Services\Invoice\PendingInvoice;
use Vanguard\Services\Mpo\MpoList;
use Vanguard\Services\Traits\YearTrait;
use Yajra\DataTables\DataTables;


class DashboardController extends Controller
{
    protected $utilities;
    protected $dataTables;

    use CompanyIdTrait;
    use YearTrait;

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
        if ($broadcaster_id) {
            //redirect user to the new landing page of the broadcaster.
            return view('broadcaster_module.landing_page');

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

            return view('agency.dashboard.new_dashboard')->with(['broadcaster' => $allBroadcasters,
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
                                                                    'agency_info' => $agency_details, 'campaigns_on_hold' => $campaigns_on_hold]);

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

    public function campaignManagementDashbaord()
    {
        $total_volume_campaign_service = new TotalVolumeCampaignChart($this->companyId());
        $company_client_service = new BroadcasterClient($this->companyId());
        $pending_invoice_service = new PendingInvoice($this->companyId());
        $client_brand_service = new CompanyBrands($this->companyId());
        $campaign_status_service = new CampaignStatus($this->companyId());
        $mpo_list_service = new MpoList($this->companyId(), null,null);
        $campaign_on_hold_service = new CampaignOnhold($this->companyId());
        $user_channels_with_other_details = $this->getChannelWithOtherDetails(Auth::user()->user_company_channels, $this->companyId());
        $current_year = date('Y');

        return view('broadcaster_module.dashboard.campaign_management.dashboard')
                    ->with(['volume' => $total_volume_campaign_service->totalVolumeOfCampaign()['campaign_volumes'],
                            'month' => $total_volume_campaign_service->totalVolumeOfCampaign()['campaign_months'],
                            'walkins' => $company_client_service->getCompanyClients(),
                            'pending_invoices' => $pending_invoice_service->getPendingInvoice(),
                            'brands' => $client_brand_service->getBrandCreatedByCompany(),
                            'active_campaigns' => $campaign_status_service->getActiveCampaigns(),
                            'pending_mpos' => $mpo_list_service->pendingMpoList(),
                            'campaign_on_hold' => $campaign_on_hold_service->getCampaignsOnhold(),
                            'user_channel_with_other_details' => $user_channels_with_other_details,
                            'periodic_revenues' => Auth::user()->companies()->count() > 1 ? $this->periodicRevenueChart($this->companyId(), $current_year) : '',
                            'year_list' => Auth::user()->companies()->count() > 1 ? $this->getYearFrom2018() : '',
                            'current_year' => $current_year]);

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

        return [
                'walkIns' => $company_client_service->getCompanyClients(),
                'pending_invoices' => $pending_invoice_service->getPendingInvoice(),
                'brands' => $client_brand_service->getBrandCreatedByCompany(),
                'active_campaigns' => $campaign_status_service->getActiveCampaigns(),
                'pending_mpos' => $mpo_list_service->pendingMpoList(),
                'campaign_on_hold' => $campaign_on_hold_service->getCampaignsOnhold(),
                'periodic_revenues' => $periodic_revenues
            ];
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
        $broadcaster = Session::get('broadcaster_id');
        // high value customer
        $high_value_customers = $this->getHighValueCustomer($broadcaster);

        //paid invoices
        $paid_invoices = $this->getPeriodicPaidInvoices($broadcaster);

        //High performing Dayparts
        $high_performing_dayparts = $this->getHighPerformingDayParts($broadcaster);

        //high performing days
        $performing_days_data = $this->getHighPerformingDays($broadcaster);

        //periodic sales report
        $periodic_sales = $this->getPeriodicSalesReport($broadcaster);

        $adslot_monthly_count = json_encode($periodic_sales['adslot_monthly']);
        $total_monthly_spend = json_encode($periodic_sales['total_month']);
        $monthly_periods = json_encode($periodic_sales['months']);

        return view('broadcaster_module.dashboard.inventory_management.dashboard')->with(['adslot_monthly_count' => $adslot_monthly_count, 'total_monthly_spend' => $total_monthly_spend,
                                                                                                'monthly_periods' => $monthly_periods, 'performing_days_data' => $performing_days_data,
                                                                                                'high_performing_dayparts' => $high_performing_dayparts, 'paid_invoices' => $paid_invoices,
                                                                                                'high_value_customers' => $high_value_customers]);
    }

    public function getHighPerformingDays($broadcaster_id)
    {
        $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaigns, DATE_FORMAT(time_created, '%a') as days 
                                                            from campaignDetails where status != 'on_hold' AND broadcaster = '$broadcaster_id' AND day_parts != '' 
                                                            GROUP BY DATE_FORMAT(time_created, '%a') desc LIMIT 7");

        $day_names = [];

        $total_campaign_amount = 0;
        foreach ($days as $day) {
            $total_campaign_amount += $day->total_campaigns;
        }

        foreach ($days as $day) {
            $percentage_days = (($day->total_campaigns) / $total_campaign_amount) * 100;
            $day_names[] = [
                'name' => $day->days,
                'y' => $percentage_days
            ];
        }

        return json_encode($day_names);
    }

    public function getPeriodicSalesReport($broadcaster_id)
    {
        $total_month = [];
        $months = [];
        $adslot_monthly = [];
        $periodic = Utilities::switch_db('api')->select("SELECT count(id) as tot_camp, SUM(adslots) as adslot, time_created as days from campaignDetails WHERE status != 'on_hold' AND
                                                              broadcaster = '$broadcaster_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");

        $price = Utilities::switch_db('api')->select("SELECT SUM(amount) AS total_price, time_created AS days FROM paymentDetails 
                                                          WHERE payment_status = 1 AND broadcaster = '$broadcaster_id' 
                                                          GROUP BY DATE_FORMAT(time_created, '%Y-%m') 
                                                          ");
        for ($i = 0; $i < count($periodic); $i++) {
            $months[] = date('M, Y', strtotime($periodic[$i]->days));
            $total_month[] = $price[$i]->total_price;
            $adslot_monthly[] = (integer)$periodic[$i]->adslot;

        }

        return (['months' => $months, 'total_month' => $total_month, 'adslot_monthly' => $adslot_monthly]);
    }

    public function getHighPerformingDayParts($broadcaster_id)
    {
        $all_adslots_dayparts = [];
        $all_daypart_names = [];
        $adslot_ids = Utilities::switch_db('api')->select("SELECT adslots_id from campaignDetails where status != 'on_hold' AND broadcaster = '$broadcaster_id' ");
        foreach ($adslot_ids as $adslot_id){
            $adslot_dayparts_ids = Utilities::switch_db('api')->select("SELECT a.day_parts as id, d.day_parts as day_parts from adslots as a INNER JOIN dayParts
                                                                            as d ON a.day_parts = d.id where a.id IN ($adslot_id->adslots_id)");
            $all_adslots_dayparts[] = $adslot_dayparts_ids;
        }

        $daypart_ids = Utilities::array_flatten($all_adslots_dayparts);

        $total = (count($daypart_ids));

        $daypartArray = [];
        foreach($daypart_ids as $daypart_name)
        {
            $daypartArray[$daypart_name->day_parts][] = $daypart_name;
        }

        foreach ($daypartArray as $key => $value){
            $day_percent = ((count($value)) / $total) * 100;
            $all_daypart_names[] = [
                'name' => $key,
                'y' => $day_percent,
            ];
        }

        return  json_encode($all_daypart_names);
    }

    public function getPeriodicPaidInvoices($broadcaster_id)
    {
        $invoice_array = [];
        $broadcaster_name = Auth::user()->companies->first()->name;
        $invoices = Utilities::switch_db('api')->select("SELECT i_d.*, i.campaign_id, c.name as campaign_name, c.campaign_id, DATE_FORMAT(c.stop_date, '%Y-%m-%d') as stop_date, 
                                                             b.name as brand_name from invoiceDetails as i_d INNER JOIN invoices as i ON i.id = i_d.invoice_id 
                                                            INNER JOIN campaignDetails as c ON c.campaign_id = i.campaign_id AND c.broadcaster = '$broadcaster_id' 
                                                            INNER JOIN brand_client as b_c ON b_c.client_id = i_d.walkins_id
                                                            INNER JOIN brands as b ON b.id = b_c.brand_id  WHERE i_d.status = 1 AND
                                                            i_d.broadcaster_id = '$broadcaster_id' ORDER BY i_d.time_created DESC LIMIT 10");

        foreach ($invoices as $invoice) {
            $invoice_array[] = [
                'campaign_id' => $invoice->campaign_id,
                'invoice_number' => $invoice->agency_id ? $invoice->invoice_number.'v'.$broadcaster_name[0] : $invoice->invoice_number,
                'campaign_name' => $invoice->campaign_name,
                'customer' => ucfirst($invoice->brand_name),
                'date' => date('Y-m-d', strtotime($invoice->time_created)),
                'date_due' => $invoice->stop_date,
            ];
        }

        return (object) $invoice_array;
    }

    public function getHighValueCustomer($broadcaster_id)
    {
            $high_value_campaigns = [];
            $payments = Utilities::switch_db('api')->select("SELECT SUM(p.amount) as total_price, p.walkins_id, b.name as brand_name from paymentDetails as p
                                                                INNER JOIN brand_client as b_c ON b_c.client_id = p.walkins_id
                                                                INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                where p.payment_status = 1 AND p.broadcaster = '$broadcaster_id' GROUP BY p.walkins_id ORDER BY total_price DESC LIMIT 10");
            foreach ($payments as $payment){
                $campaign_count = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign_count, SUM(adslots) as total_adslots from campaignDetails where 
                                                                          status != 'on_hold' AND walkins_id = '$payment->walkins_id' AND broadcaster = '$broadcaster_id'");
                $high_value_campaigns[] = [
                    'number_of_campaigns' => $campaign_count[0]->total_campaign_count,
                    'total_adslots' => $campaign_count[0]->total_adslots,
                    'customer_name' => ucfirst($payment->brand_name),
                    'payment' => $payment->total_price,
                ];
            }

            return $high_value_campaigns;
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
