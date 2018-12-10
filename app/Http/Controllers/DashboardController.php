<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Auth;
use Session;
use Yajra\DataTables\DataTables;


class DashboardController extends Controller
{

    public function index()
    {
        //Broadcaster Dashboard module
        $role = \DB::table('role_user')->where('user_id', Auth::user()->id)->first();
        if ($role->role_id === 3) {

            //redirect user to the new landing page of the broadcaster.
            $broadcaster = Session::get('broadcaster_id');
            $broadcaster_info = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster'");
            return view('broadcaster_module.landing_page', compact('broadcaster_info'));

        } else if ($role->role_id === 4) {
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

    public function dashboardCampaigns(DataTables $dataTables, Request $request)
    {
        //campaigns
        $agency_id = Session::get('agency_id');
        $broadcaster_id = Session::get('broadcaster_id');
        if($request->start_date && $request->stop_date) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
        }else{
            $start_date = '2000-01-01';
            $stop_date = '2070-01-01';
        }

        if($agency_id){
            $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, 
                                                                    c_d.start_date, c_d.time_created, c_d.product, 
                                                                    c_d.name, c_d.campaign_id, 
                                                                    p.total, b.name AS brand_name, c.campaign_reference 
                                                                    FROM campaignDetails AS c_d 
                                                                    INNER JOIN payments AS p ON p.campaign_id = c_d.campaign_id 
                                                                    INNER JOIN campaigns AS c ON c.id = c_d.campaign_id
                                                                    INNER JOIN brands AS b ON b.id = c_d.brand 
                                                                    WHERE  c_d.agency = '$agency_id' AND c_d.adslots  > 0 AND 
                                                                    c_d.start_date BETWEEN '$start_date' and '$stop_date' 
                                                                    GROUP BY c_d.campaign_id ORDER BY c_d.time_created DESC");

        }else if($broadcaster_id){
            if($request->filter_user){
                if($request->filter_user == 'agency'){
                    $all_campaigns = $this->filterCampaignDashboardByAgency($broadcaster_id);
                }else if($request->filter_user == 'broadcaster'){
                    $all_campaigns = $this->filterCampaignDashboardByWalkins($broadcaster_id);
                }else{
                    $all_campaigns = $this->broadcasterCampaignDashboard($broadcaster_id, $start_date, $stop_date);
                }
            }else{
                $all_campaigns = $this->broadcasterCampaignDashboard($broadcaster_id, $start_date, $stop_date);
            }
        }

        $campaigns_datatables = Utilities::getCampaignDatatables($all_campaigns);

        return $dataTables->collection($campaigns_datatables)
            ->addColumn('name', function ($campaigns_datatables) {
                if(Session::has('agency_id')){
                    if($campaigns_datatables['status'] === 'on_hold'){
                        return '<a href="'.route('agency.campaigns_onhold').'">'.$campaigns_datatables['name'].'</a>';
                    }else{
                        return '<a href="'.route('agency.campaign.details', ['id' => $campaigns_datatables['campaign_id']]).'">'.$campaigns_datatables['name'].'</a>';
                    }
                }else if(Session::has('broadcaster_id')){
                    if($campaigns_datatables['status'] === 'on_hold'){
                        return '<a href="'.route('broadcaster.campaign.hold').'">'.$campaigns_datatables['name'].'</a>';
                    }else{
                        return '<a href="'.route('broadcaster.campaign.details', ['id' => $campaigns_datatables['campaign_id']]).'">'.$campaigns_datatables['name'].'</a>';
                    }
                }
            })
            ->editColumn('status', function ($campaigns_datatables){
                if($campaigns_datatables['status'] === "on_hold"){
                    return '<span class="span_state status_on_hold">On Hold</span>';
                }elseif ($campaigns_datatables['status'] === "pending"){
                    return '<span class="span_state status_pending">Pending</span>';
                }elseif ($campaigns_datatables['status'] === 'expired'){
                    return '<span class="span_state status_danger">Finished</span>';
                }elseif($campaigns_datatables['status'] === 'active') {
                    return '<span class="span_state status_success">Active</span>';
                }else {
                    return '<span class="span_state status_danger">File Errors</span>';
                }
            })
            ->rawColumns(['status' => 'status', 'name' => 'name'])
            ->addIndexColumn()
            ->make(true);
    }

    public function filterCampaignDashboardByAgency($broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, 
                                                          c_d.start_date, c_d.time_created, c_d.product, c_d.name, 
                                                          c_d.campaign_id, p.total, b.name AS brand_name, 
                                                          c.campaign_reference 
                                                          FROM campaignDetails AS c_d 
                                                          INNER JOIN payments AS p ON p.campaign_id = c_d.campaign_id 
                                                          INNER JOIN campaigns AS c ON c.id = c_d.campaign_id 
                                                          INNER JOIN brands AS b ON b.id = c_d.brand 
                                                          WHERE c_d.agency_broadcaster = '$broadcaster_id'
                                                          AND c_d.adslots  > 0
                                                          ORDER BY c_d.time_created DESC");
    }

    public function filterCampaignDashboardByWalkins($broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, 
                                                          c_d.start_date, c_d.time_created, c_d.product, c_d.name, 
                                                          c_d.campaign_id, p.total, b.name AS brand_name, 
                                                          c.campaign_reference 
                                                          FROM campaignDetails AS c_d 
                                                          INNER JOIN payments AS p ON p.campaign_id = c_d.campaign_id 
                                                          INNER JOIN campaigns AS c ON c.id = c_d.campaign_id 
                                                          INNER JOIN brands AS b ON b.id = c_d.brand 
                                                          WHERE (c_d.broadcaster = '$broadcaster_id'
                                                          AND c_d.agency = '')
                                                          AND c_d.adslots  > 0 
                                                          ORDER BY c_d.time_created DESC");
    }

    public function broadcasterCampaignDashboard($broadcaster_id, $start_date, $stop_date)
    {
        return Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, 
                                                                      c_d.start_date, c_d.time_created, c_d.product, c_d.name, 
                                                                      c_d.campaign_id, p.total, b.name AS brand_name, 
                                                                      c.campaign_reference 
                                                                      FROM campaignDetails AS c_d 
                                                                      INNER JOIN payments AS p ON p.campaign_id = c_d.campaign_id 
                                                                      INNER JOIN campaigns AS c ON c.id = c_d.campaign_id 
                                                                      INNER JOIN brands AS b ON b.id = c_d.brand 
                                                                      WHERE c_d.broadcaster = '$broadcaster_id' 
                                                                      AND c_d.adslots  > 0 AND 
                                                                      c_d.start_date BETWEEN '$start_date' AND '$stop_date' 
                                                                      ORDER BY c_d.time_created DESC");
    }

    public function campaignManagementDashbaord()
    {
        $broadcaster = Session::get('broadcaster_id');
        //total volume of campaigns
        $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaignDetails where status != 'on_hold' AND
                                                            broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
        $c_vol = [];
        $c_month = [];

        foreach ($camp_vol as $ca) {
            $c_vol[] = $ca->volume;
            $c_month[] = $ca->month;
        }

        $c_volume = json_encode($c_vol);
        $c_mon = json_encode($c_month);

        $today_date = date("Y-m-d");

//            all clients
        $clients = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE broadcaster_id = '$broadcaster' ORDER BY time_created DESC");

//            pending invoices
        $pending_invoices = Utilities::switch_db('api')->select("SELECT * FROM invoiceDetails where broadcaster_id = '$broadcaster' AND status = 0 ");

//            all_brands
        $all_brands = Utilities::getBrands($broadcaster);

        $active_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name, 
                                                                      c.campaign_reference from campaignDetails as c_d LEFT JOIN payments as p ON p.campaign_id = c_d.campaign_id 
                                                                       LEFT JOIN campaigns as c ON c.id = c_d.campaign_id LEFT JOIN brands as b ON b.id = c_d.brand where  c_d.broadcaster = '$broadcaster' 
                                                                       and c_d.status = 'active' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");

        $broadcaster_info = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster'");

        $pending_mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d 
                                                            INNER JOIN mpos as m ON m.id = m_d.mpo_id 
                                                            INNER JOIN campaignDetails as c_d ON c_d.campaign_id = m.campaign_id 
                                                            AND c_d.broadcaster = m_d.broadcaster_id
                                                            where m_d.broadcaster_id = '$broadcaster' and c_d.status != 'on_hold' AND
                                                            m_d.is_mpo_accepted = 0 order by m_d.time_created desc");

        $campaign_on_hold = Utilities::switch_db('api')->select("SELECT id FROM campaignDetails where status = 'on_hold' AND broadcaster = '$broadcaster' AND agency = ''");

        return view('broadcaster_module.dashboard.campaign_management.dashboard')->with(['volume' => $c_volume, 'month' => $c_mon, 'broadcaster_info' => $broadcaster_info,
                                                                                                'walkins' => $clients, 'pending_invoices' => $pending_invoices,
                                                                                                'brands' => $all_brands, 'active_campaigns' => $active_campaigns,
                                                                                                'pending_mpos' => $pending_mpos, 'campaign_on_hold' => $campaign_on_hold]);

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
//        dd($price);
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
        $broadcaster_det = Utilities::getBroadcasterDetails($broadcaster_id);
        $broadcaster_name = $broadcaster_det[0]->brand;
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


}
