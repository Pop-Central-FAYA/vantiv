<?php

namespace Vanguard\Http\Controllers;

use Hamcrest\Util;
use Illuminate\Http\Request;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Auth;
use Carbon\Carbon;
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
            $allBroadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters");
            $agency_id = Session::get('agency_id');
            $agency_info = Utilities::switch_db('api')->select("SELECT * from agents where id = '$agency_id'");
            $camp_prod = Utilities::switch_db('api')->select("SELECT id, product from campaignDetails where agency = '$agency_id' GROUP BY campaign_id");
            $pe = $this->broadcasterFilter($agency_id);

            $date = [];
            $bra = [];
            $tot = [];
            $amm = [];
            $dat = [];
            $bra_tot = [];
            $bra_dates = [];
            $braa = [];
            foreach ($pe as $p) {
                if (!$p) {
                    $tot[] = 0;
                } else {
                    $tot[] = $p['total'];
                }
            }

            foreach ($pe as $p) {
                if (!$p) {
                    $date[] = 0;
                } else {
                    $date[] = $p['date'];
                }
            }

            foreach ($pe as $p) {
                if (!$p) {
                    $bra[] = 0;
                } else {
                    $bra[] = $p['name'];
                }
            }

            $d = json_encode($date);
            $am = json_encode($tot);
            $na = json_encode($bra);

            #Periodic spend report of total * product
            $current_month = date('F');
            $months = [];
            $default_month = date('F', strtotime("2018-01-01"));
            for($i = 1; $i <= 12; $i++){
                $months[] = date('F', strtotime("2018-".$i."-01"));
            }

            #periodic spent report on brands
            $bra = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id IN (SELECT id from walkIns where agency_id = '$agency_id')");
            $brand = $this->clientDashboard($agency_id);

            if($brand){
                foreach ($brand as $b) {
                    $bra_tot[] = (integer)$b['total'];
                }
                foreach ($brand as $b) {
                    $braa[] = $b['brand'];
                }
                foreach ($brand as $b) {
                    $bra_dates[] = $b['date'];
                }

            }

            $bra_d = json_encode($bra_dates);
            $bra_am = json_encode($bra_tot);
            $bra_na = json_encode($braa);

            $pro_period = $this->periodic_spent($agency_id);
            $p = json_encode($pro_period);

            #Budget pacing report
            $b_pacing = $this->budgetPacing($agency_id);

            #all agency clients
            $count = Api::countClients($agency_id);

            #all campaigns
            $count_campaigns = Api::countCampaigns($agency_id);

            #invoices
            $count_invoice =Api::countInvoices($agency_id);

            #count Brands
            $count_brands = Api::countBrands($agency_id);

            #invoice
            $invoice_campaign_details = Api::allInvoiceAdvertiserorAgency($agency_id);

            #approval
            $invoice_approval = Api::countApproved($agency_id);

            #unapproval
            $invoice_unapproval = Api::countUnapproved($agency_id);

//            TV
            $tv_rating = $this->tv($agency_id);
            $active = $tv_rating['per_active'];
            $pending = $tv_rating['per_pending'];
            $finished = $tv_rating['per_finished'];

            //Radio
            $radio_rating = $this->radio($agency_id);
            $active_radio = $radio_rating['per_active'];
            $pending_radio = $radio_rating['per_pending'];
            $finished_radio = $radio_rating['per_finished'];

            $today_date = date("Y-m-d");

//            all clients
            $clients = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE agency_id = '$agency_id' ORDER BY time_created DESC");

//            pending invoices
            $pending_invoices = Utilities::switch_db('api')->select("SELECT * FROM invoiceDetails where agency_id = '$agency_id' AND status = 0 GROUP BY invoice_id");

//            all_brands
            $all_brands = Utilities::switch_db('api')->select("SELECT * FROM brands where broadcaster_agency = '$agency_id'");

            $active_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' AND start_date <= '$today_date' AND stop_date > '$today_date' GROUP BY campaign_id");

            return view('agency.dashboard.new_dashboard')->with(['broadcaster' => $allBroadcaster,
                                                                    'invoice_approval' => $invoice_approval,
                                                                    'all_invoices' => $invoice_campaign_details,
                                                                    'count_brands' => $count_brands, 'count_invoice' => $count_invoice,
                                                                    'count_campaigns' => $count_campaigns,  'count_client' => $count,
                                                                    'date' => $d, 'amount' => $am, 'name' => $na, 'camp_prod' => $camp_prod,
                                                                    'periodic_data' => $p, 'b_pacing' => $b_pacing,
                                                                    'invoice_unapproval' => $invoice_unapproval,
                                                                    'months' => $months,
                                                                    'current_month' => $current_month,
                                                                    'bra_dates' => $bra_d,
                                                                    'bra_am' => $bra_am,
                                                                    'bra_na' => $bra_na,
                                                                    'brand' => $bra,
                                                                    'active_campaigns' => $active_campaigns,
                                                                    'all_brands' => $all_brands,
                                                                    'pending_invoices' => $pending_invoices,
                                                                    'clients' => $clients,
                                                                    'active' => $active,
                                                                    'pending' => $pending,
                                                                    'finished' => $finished,
                                                                    'radio_rating' => $radio_rating,
                                                                    'active_radio' => $active_radio,
                                                                    'pending_radio' => $pending_radio,
                                                                    'finish_radio' => $finished_radio,
                                                                    'agency_info' => $agency_info]);

        } else if ($role->role_id === 6) {

            //advertiser dashboard
            $allBroadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters");
            $advertiser_id = Session::get('advertiser_id');
            $advertiser_info = Utilities::switch_db('api')->select("SELECT * from advertisers where id = '$advertiser_id'");
            $camp_prod_advertiser = Utilities::switch_db('api')->select("SELECT id, product from campaignDetails where agency = '$advertiser_id' GROUP BY campaign_id");
            $pe_advertiser = $this->broadcasterFilter($advertiser_id);

            $date_advertiser = [];
            $bra_advertiser = [];
            $tot_advertiser = [];
            $amm = [];
            $dat = [];
            $bra_tot_advertiser = [];
            $bra_dates_advertiser = [];
            $braa_advertiser = [];
            foreach ($pe_advertiser as $p) {
                if (!$p) {
                    $tot_advertiser[] = 0;
                } else {
                    $tot_advertiser[] = $p['total'];
                }
            }
            foreach ($pe_advertiser as $p) {
                if (!$p) {
                    $date_advertiser[] = 0;
                } else {
                    $date_advertiser[] = $p['date'];
                }
            }
            foreach ($pe_advertiser as $p) {
                if (!$p) {
                    $bra_advertiser[] = 0;
                } else {
                    $bra_advertiser[] = $p['name'];
                }
            }

            $d_advertiser = json_encode($date_advertiser);
            $am_advertiser = json_encode($tot_advertiser);
            $na_advertiser = json_encode($bra_advertiser);

            #Periodic spend report of total * product
            $current_month = date('F');
            $months = [];
            $default_month = date('F', strtotime("2018-01-01"));
            for($i = 1; $i <= 12; $i++){
                $months[] = date('F', strtotime("2018-".$i."-01"));
            }

            #periodic spent report on brands

            $bra = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency  = '$advertiser_id'");

            $brand = $this->clientDashboard($advertiser_id);

            if($brand){
                foreach ($brand as $b) {
                    $bra_tot_advertiser[] = (integer)$b['total'];
                }
                foreach ($brand as $b) {
                    $braa_advertiser[] = $b['brand'];
                }
                foreach ($brand as $b) {
                    $bra_dates_advertiser[] = $b['date'];
                }

            }

            $bra_d_advertiser = json_encode($bra_dates_advertiser);
            $bra_am_advertiser = json_encode($bra_tot_advertiser);
            $bra_na_advertiser = json_encode($braa_advertiser);

            $pro_period_advertiser = $this->periodic_spent($advertiser_id);
            $p_advertiser = json_encode($pro_period_advertiser);

            #Budget pacing report
            $b_pacing_advertiser = $this->budgetPacing($advertiser_id);


            #all campaigns
            $count_campaigns_advertiser = Api::countCampaigns($advertiser_id);

            #invoices
            $count_invoice_advertiser =Api::countInvoices($advertiser_id);

            #count Brands
            $count_brands_advertiser = Api::countBrands($advertiser_id);

            #invoice
            $invoice_campaign_details_advertiser = Api::allInvoiceAdvertiserorAgency($advertiser_id);

            #approval
            $invoice_approval_advertiser = Api::countApproved($advertiser_id);

            #unapproval
            $invoice_unapproval_advertiser = Api::countUnapproved($advertiser_id);

            return view('advertisers.dashboard.new_dashboard')->with(['broadcaster' => $allBroadcaster,
                                                                            'invoice_approval' => $invoice_approval_advertiser,
                                                                            'pending_invoice' => $invoice_unapproval_advertiser,
                                                                            'all_invoices' => $invoice_campaign_details_advertiser,
                                                                            'count_brands' => $count_brands_advertiser, 'count_invoice' => $count_invoice_advertiser,
                                                                            'count_campaigns' => $count_campaigns_advertiser,
                                                                            'date' => $d_advertiser, 'amount' => $am_advertiser, 'name' => $na_advertiser, 'camp_prod' => $camp_prod_advertiser,
                                                                            'periodic_data' => $p_advertiser, 'b_pacing' => $b_pacing_advertiser,
                                                                            'invoice_unapproval' => $invoice_unapproval_advertiser,
                                                                            'months' => $months,
                                                                            'current_month' => $current_month,
                                                                            'bra_dates' => $bra_d_advertiser,
                                                                            'bra_am' => $bra_am_advertiser,
                                                                            'bra_na' => $bra_na_advertiser,
                                                                            'brand' => $bra_advertiser,
                                                                            'advertiser_info' => $advertiser_info,
                                                                                'brand' => $bra]);

        }else if ($role->role_id === 7){

            //Broadcaster user dashboard

            $broadcaster_user = Session::get('broadcaster_user_id');
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;

            $camp = [];
            $user_details = '';
            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, agency, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaignDetails WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND walkins_id != '' GROUP BY walkins_id LIMIT 10");

            foreach ($campaign as $c) {

                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$c->walkins_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from paymentDetails WHERE walkins_id = '$c->walkins_id'");
                $camp[] = [
                    'number_of_campaign' => $c->total_campaign,
                    'user_id' => $c->walkins_id,
                    'total_adslot' => $c->total_adslot,
                    'customer_name' => $user_details,
                    'payment' => $payments[0]->total_price,
                ];
            }

            array_multisort(array_column($camp, 'payment'), SORT_DESC, $camp);

            //paid invoices
            $invoice_array = [];
            $inv = Utilities::switch_db('api')->select("SELECT * from invoiceDetails WHERE broadcaster_id = '$broadcaster' AND agency_id = '$broadcaster_user' ORDER BY time_created DESC LIMIT 10");
            foreach ($inv as $i) {
                $invoice_info = Utilities::switch_db('api')->select("SELECT * from invoices where id = '$i->invoice_id'");
                $campaign_id = $invoice_info[0]->campaign_id;
                $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$i->walkins_id'");
                $user_id = $walk[0]->user_id;
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$user_id'");
                $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaignDetails where campaign_id='$campaign_id'");
                $invoice_array[] = [
                    'campaign_name' => isset($campaign_det[0]->campaign_name) ? $campaign_det[0]->campaign_name : '',
                    'customer' => $customer_name[0]->firstname . ' ' . $customer_name[0]->lastname,
                    'date' => date('Y-m-d', strtotime($i->time_created)),
                    'date_due' => $campaign_det[0]->stop_date,
                ];
            }
            $invoice = (object) $invoice_array;

            //total volume of campaigns
            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaignDetails where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
            $c_vol = [];
            $c_month = [];

            foreach ($camp_vol as $ca) {
                $c_vol[] = $ca->volume;
            }

            foreach ($camp_vol as $ca) {
                $month = ($ca->month);
                $c_month[] = $month;
            }

            $c_volume = json_encode($c_vol);
            $c_mon = json_encode($c_month);

            //High performing Dayparts
            $all_slots = [];
            $all_dayp = [];
            $dayp_namesss = [];
            $slots = Utilities::switch_db('api')->select("SELECT adslots_id from campaignDetails where broadcaster = '$broadcaster' AND agency = '$broadcaster_user'");
            foreach ($slots as $slot){
                $adslots = Utilities::switch_db('api')->select("SELECT day_parts from adslots where id IN ($slot->adslots_id)");
                $all_slots[] = $adslots;
            }

            $dayp_id = Utilities::array_flatten($all_slots);

            foreach ($dayp_id as $d){
                $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts where id = '$d->day_parts'");
                $all_dayp[] = $day_parts;
            }

            $dayp_name = Utilities::array_flatten($all_dayp);
            $total = (count($dayp_name));

            $newArray = [];
            foreach($dayp_name as $entity)
            {
                $newArray[$entity->day_parts][] = $entity;
            }

            foreach ($newArray as $nnn => $value){
                $day_percent = ((count($value)) / $total) * 100;
                $dayp_namesss[] = [
                    'name' => $nnn,
                    'y' => $day_percent,
                ];
            }

            $day_pie = json_encode($dayp_namesss);

            //high performing days
            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, DATE_FORMAT(time_created, '%a %M %d, %Y') as days from campaignDetails where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND day_parts != '' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc LIMIT 7");
            $day_name = [];
            $b = [];
            for ($j = 0; $j < count($days); $j++) {
                $b[] = $days[$j]->tot_camp;
            }
            $s = array_sum($b);

            foreach ($days as $dd) {
                $perc_days = (($dd->tot_camp) / $s) * 100;
                $day_name[] = [
                    'name' => $dd->days,
                    'y' => $perc_days
                ];
            }
            $days_data = json_encode($day_name);

            //periodic sales report
            $prr = [];
            $ads = [];
            $months = [];
            $slot = [];
            $periodic = Utilities::switch_db('api')->select("SELECT count(id) as tot_camp, SUM(adslots) as adslot, time_created as days from campaignDetails WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
            $price = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price, time_created as days from paymentDetails WHERE broadcaster = '$broadcaster' AND agency_id = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");

            for ($i = 0; $i < count($periodic); $i++) {
                $ads[] = [
                    'total' => $price[$i]->total_price,
                    'adslot' => $periodic[$i]->adslot,
                    'date' => date('M, Y', strtotime($periodic[$i]->days)),
                ];

            }
            foreach ($ads as $a) {
                $months[] = $a['date'];
            }
            foreach ($ads as $a) {
                $prr[] = $a['total'];
            }
            foreach ($ads as $a) {
                $slot[] = (integer)$a['adslot'];
            }

            $ads_no = json_encode($slot);
            $tot_pri = json_encode($prr);
            $mon = json_encode($months);

//            dd($ads,$ads_no, $tot_pri, $mon);

//        Inventory fill rate
            $today = getDate();
            $today_day = $today['weekday'];
            $rate_c = [];
            $get_day_id = Utilities::switch_db('api')->select("SELECT id as day_id from days WHERE day = '$today_day'");
            $t_day = $get_day_id[0]->day_id;
            $get_ratecards_for_this_day = Utilities::switch_db('api')->select("SELECT * from rateCards WHERE day = '$t_day' AND broadcaster = '$broadcaster'");
            foreach ($get_ratecards_for_this_day as $gr) {
                $adslot = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_slot, rate_card from adslots WHERE rate_card = '$gr->id'");
//            $campaign = Utilities::switch_db('reports')->select("SELECT SUM(adslots) as total_sold from campaigns WHERE broadcaster = '$broadcaster' AND  GROUP BY ");
                $rate_c[] = $adslot;
            }

            return view('broadcaster_user.dashboard.dashboard')->with(['campaign' => $camp, 'volume' => $c_volume, 'month' => $c_mon, 'high_dayp' => $day_pie, 'days' => $days_data, 'adslot' => $ads_no, 'price' => $tot_pri, 'mon' => $mon, 'invoice' => $invoice]);

        }elseif ($role->role_id === 1){
            //admin dashboard
            $all_agency = Utilities::switch_db('api')->select("SELECT * FROM  agents");
            $all_advertisers = Utilities::switch_db('api')->select("SELECT * FROM advertisers");
            $all_industries = Utilities::switch_db('api')->select("SELECT * from sectors");
            $all_broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters");
            return view('admin.dashboard', compact('all_agency', 'all_advertisers','all_industries','all_broadcasters'));
        }elseif($role->role_id === 5){
            return view('clients.dashboard.dashboard');
        }
    }

    public function filterByBroad()
    {
        $agency_id = Session::get('agency_id');
        $b_id = request()->br_id;
        $broadcaster_brand = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id='$b_id'");
        $pe = [];
        $tot = [];
        $date = [];
        $bra = [];
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from paymentDetails WHERE agency_broadcaster = '$b_id' AND agency_id = '$agency_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
        foreach ($broad as $broads) {
            $pe[] = [
                'total' => (integer)$broads->total,
                'date' => date('M', strtotime($broads->time_created)),
                'name' => $broadcaster_brand[0]->brand,
            ];
        }
        foreach ($pe as $p) {
            $tot[] = $p['total'];
        }
        foreach ($pe as $p) {
            $date[] = $p['date'];
        }
        foreach ($pe as $p) {
            $bra[] = $p['name'];
        }

        return response()->json(['date' => $date, 'amount_price' => $tot, 'name' => $bra]);
    }

    public function clientDashboard($agency_id)
    {
        $brand = [];

        if(Session::get('advertiser_id')){
            $br = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id IN (SELECT user_id from advertisers where id = '$agency_id') LIMIT 1");
        }else{
            $br = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id IN (SELECT id from walkIns where agency_id = '$agency_id') LIMIT 1");
        }

        foreach ($br as $b) {
            $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total, time_created FROM payments WHERE campaign_id IN (SELECT campaign_id from campaignDetails WHERE brand = '$b->id' AND agency = '$agency_id' GROUP BY campaign_id) GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
            if (!$camp_pay) {
                $total = 0;
                $date = 0;
            } else {

                foreach ($br as $b) {
                    $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total, time_created FROM payments WHERE campaign_id IN (SELECT campaign_id from campaignDetails WHERE brand = '$b->id' AND agency = '$agency_id' GROUP BY campaign_id) GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
                    if (!$camp_pay) {
                        $total = 0;
                        $date = 0;
                    } else {
                        $total = $camp_pay[0]->total;
                        $date = $camp_pay[0]->time_created;
                    }
                    $brand[] = [
                        'brand_id' => $b->id,
                        'brand' => $b->name,
                        'total' => $total,
                        'date' => date('M', strtotime($date)),
                    ];
                }

                return $brand;
            }
        }
    }

    public function filterByBrand()
    {
        $b_id = request()->br_id;
        $agency_id = Session::get('agency_id');
        $brand = [];
        $tot = [];
        $dates = [];
        $braa = [];
        $br = Utilities::switch_db('api')->select("SELECT * from brands where id = '$b_id'");

        $brand_id = $br[0]->id;
        $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total, time_created FROM payments WHERE campaign_id IN (SELECT campaign_id from campaignDetails WHERE brand = '$brand_id' AND agency = '$agency_id' ) GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
        foreach ($camp_pay as $cam){
            if (!$cam) {
                $total = 0;
                $date = 0;
            } else {
                $total = $cam->total;
                $date = $cam->time_created;
            }
            $brand[] = [
                'brand_id' => $br[0]->id,
                'brand' => $br[0]->name,
                'total' => $total,
                'date' => date('M', strtotime($date)),
            ];
        }

        foreach ($brand as $b) {
            $tot[] = (integer)$b['total'];
        }
        foreach ($brand as $b) {
            $braa[] = $b['brand'];
        }
        foreach ($brand as $b) {
            $dates[] = $b['date'];
        }

        return response()->json(['date' => $dates, 'amount_price' => $tot, 'name' => $braa]);

    }

    public function broadcasterFilter($agency_id)
    {

        #Periodic spend report of total * chanel
        $broadAgency = Utilities::switch_db('api')->select("SELECT agency_broadcaster as broad_ag from campaignDetails where agency = '$agency_id' LIMIT 1");
        if (count($broadAgency) === 0) {
            $age_broad_id = 0;
        } else {
            $age_broad_id = $broadAgency[0]->broad_ag;
        }
        $broadcaster_brand = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id='$age_broad_id'");
        $pe = [];
        $tot = [];
        $date = [];
        $bra = [];
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from paymentDetails WHERE agency_broadcaster = '$age_broad_id' AND agency_id = '$agency_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
        foreach ($broad as $broads) {
            $pe[] = [
                'total' => (integer)$broads->total,
                'date' => date('M', strtotime($broads->time_created)),
                'name' => $broadcaster_brand[0]->brand,
            ];
        }
        return $pe;


    }

    public function budgetPacing($agency_id)
    {
        $trans = Utilities::switch_db('api')->select("SELECT current_balance as bal, time_created as `date` from walletHistories where user_id='$agency_id'");
        $b = [];
        foreach ($trans as $t) {
            $date = strtotime($t->date) * 1000;
            $b[] = [
                   $date, (integer)$t->bal
            ];
        }

        return json_encode($b);
    }

    public function filterByAgencyBroad()
    {
        $b_id = request()->br_id;
        $broadcaster_brand = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id='$b_id'");
        $pe = [];
        $tot = [];
        $date = [];
        $bra = [];
        $agency_id = Session::get('agency_id');
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from paymentDetails WHERE agency_broadcaster = '$b_id' AND agency_id = '$agency_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
        foreach ($broad as $broads)
        {
            $pe[] = [
                'total' => (integer)$broads->total,
                'date' => date('M', strtotime($broads->time_created)),
                'name' => $broadcaster_brand[0]->brand,
            ];
        }
        foreach ($pe as $p)
        {
            $tot[] = $p['total'];
        }
        foreach ($pe as $p)
        {
            $date[] = $p['date'];
        }
        foreach ($pe as $p)
        {
            $bra[] = $p['name'];
        }

        return response()->json(['date' => $date, 'amount_price' => $tot, 'name' => $bra]);
    }

    public function filterByAdvertiserBroad()
    {
        $b_id = request()->br_id;
        $broadcaster_brand = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id='$b_id'");
        $pe = [];
        $tot = [];
        $date = [];
        $bra = [];
        $advertiser_id = Session::get('advertiser_id');
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from paymentDetails WHERE agency_broadcaster = '$b_id' AND agency_id = '$advertiser_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
        foreach ($broad as $broads)
        {
            $pe[] = [
                'total' => (integer)$broads->total,
                'date' => date('M', strtotime($broads->time_created)),
                'name' => $broadcaster_brand[0]->brand,
            ];
        }
        foreach ($pe as $p)
        {
            $tot[] = $p['total'];
        }
        foreach ($pe as $p)
        {
            $date[] = $p['date'];
        }
        foreach ($pe as $p)
        {
            $bra[] = $p['name'];
        }

        return response()->json(['date' => $date, 'amount_price' => $tot, 'name' => $bra]);
    }

    public function periodic_spent($agency_id)
    {
        $pro_period = [];
        $date = date('Y-m', time());
        $pro_cam = Utilities::switch_db('api')->select("SELECT * from campaignDetails where agency = '$agency_id' AND DATE_FORMAT(time_created, '%Y-%m') = '$date' GROUP BY campaign_id");
        foreach ($pro_cam as $pr){
            $campaign_p = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$pr->campaign_id'");
            $paym = Utilities::switch_db('api')->select("SELECT SUM(amount) as total from paymentDetails where agency_id = '$agency_id' AND DATE_FORMAT(time_created, '%Y-%m') = '$date' GROUP BY payment_id");
            $pro_period[] = [
                'name' => $pr->product,
                'y' => (($campaign_p[0]->total) / $paym[0]->total) * 100,
            ];
        }


        return $pro_period;
    }

    public function filterByMonth()
    {
        $agency_id = Session::get('agency_id');
        $month = request()->month;
        $year = date('Y');
        $date = (string)($year.'-'.$month);
        $pro_period_month = [];
        $pro_cam = Utilities::switch_db('api')->select("SELECT * from campaignDetails where agency = '$agency_id' AND DATE_FORMAT(time_created, '%Y-%M') = '$date' GROUP BY campaign_id");
        foreach ($pro_cam as $pr){
            $campaign_p = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$pr->campaign_id'");
            $paym = Utilities::switch_db('api')->select("SELECT SUM(amount) as total from paymentDetails where agency_id = '$agency_id' AND DATE_FORMAT(time_created, '%Y-%M') = '$date' GROUP BY payment_id");
            $pro_period_month[] = [
                'name' => $pr->product,
                'y' => (($campaign_p[0]->total) / $paym[0]->total) * 100,
            ];
        }

        return response()->json(['pro_month' => $pro_period_month]);

    }

    public function filterByAdvertiserBrand()
    {
        $b_id = request()->br_id;
        $advertiser_id = Session::get('advertiser_id');
        $brand = [];
        $tot = [];
        $dates = [];
        $braa = [];
        $br = Utilities::switch_db('api')->select("SELECT * from brands where id = '$b_id'");
        foreach ($br as $b) {
            $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total, time_created FROM payments WHERE campaign_id IN (SELECT campaign_id from campaignDetails WHERE brand = '$b->id' AND agency = '$advertiser_id' GROUP BY campaign_id) GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
            if (!$camp_pay) {
                $total = 0;
                $date = 0;
            } else {
                $total = $camp_pay[0]->total;
                $date = $camp_pay[0]->time_created;
            }
            $brand[] = [
                'brand_id' => $b->id,
                'brand' => $b->name,
                'total' => $total,
                'date' => date('M', strtotime($date)),
            ];
        }
        foreach ($brand as $b) {
            $tot[] = (integer)$b['total'];
        }
        foreach ($brand as $b) {
            $braa[] = $b['brand'];
        }
        foreach ($brand as $b) {
            $dates[] = $b['date'];
        }

        return response()->json(['date' => $dates, 'amount_price' => $tot, 'name' => $braa]);
    }

    public function filterByAdvertiserMonth()
    {
        $advertiser_id = Session::get('advertiser_id');
        $month = request()->month;
        $year = date('Y');
        $date = (string)($year.'-'.$month);
        $pro_period_month = [];
        $pro_cam = Utilities::switch_db('api')->select("SELECT * from campaignDetails where agency = '$advertiser_id' AND DATE_FORMAT(time_created, '%Y-%M') = '$date' GROUP BY campaign_id");
        foreach ($pro_cam as $pr){
            $campaign_p = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$pr->campaign_id'");
            $paym = Utilities::switch_db('api')->select("SELECT SUM(amount) as total from paymentDetails where agency_id = '$advertiser_id' AND DATE_FORMAT(time_created, '%Y-%M') = '$date' GROUP BY payment_id");
            $pro_period_month[] = [
                'name' => $pr->product,
                'y' => (($campaign_p[0]->total) / $paym[0]->total) * 100,
            ];
        }

        return response()->json(['pro_month' => $pro_period_month]);
    }

    public function tv($agency_id)
    {
        $today = date("Y-m-d");
        $all_tv_campaigns = [];
        $finished_tv_campaigns = [];
        $pending_tv_campaign = [];
        $active_tv_campaign = [];

        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where broadcaster IN (SELECT b.id from broadcasters as b, campaignChannels as c where b.channel_id = c.id AND c.channel = 'TV')");

        foreach ($campaigns as $campaign){
            $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($campaign->channel) AND channel = 'TV' ");
            $all_tv_campaigns[] = [
                'campaign_id' => $campaign->campaign_id,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->stop_date,
                'channel' => $channels[0]->channel,
                'channel_id' => $channels[0]->id,
            ];
        }

        foreach ($all_tv_campaigns as $all_tv_campaign){
            if($today > $all_tv_campaign['end_date']){
                $finished_tv_campaigns[] = [
                    'campaign_id' => $all_tv_campaign['campaign_id'],
                    'start_date' => $all_tv_campaign['start_date'],
                    'end_date' => $all_tv_campaign['end_date'],
                    'channel' => $all_tv_campaign['channel'],
                    'channel_id' => $all_tv_campaign['channel_id'],
                ];
            }
        }

        foreach ($all_tv_campaigns as $all_tv_campaign){
            if($today < $all_tv_campaign['start_date']){
                $pending_tv_campaign[] = [
                    'campaign_id' => $all_tv_campaign['campaign_id'],
                    'start_date' => $all_tv_campaign['start_date'],
                    'end_date' => $all_tv_campaign['end_date'],
                    'channel' => $all_tv_campaign['channel'],
                    'channel_id' => $all_tv_campaign['channel_id'],
                ];
            }
        }

        foreach ($all_tv_campaigns as $all_tv_campaign){
            if($today >= $all_tv_campaign['start_date'] && $today <= $all_tv_campaign['end_date']){
                $active_tv_campaign[] = [
                    'campaign_id' => $all_tv_campaign['campaign_id'],
                    'start_date' => $all_tv_campaign['start_date'],
                    'end_date' => $all_tv_campaign['end_date'],
                    'channel' => $all_tv_campaign['channel'],
                    'channel_id' => $all_tv_campaign['channel_id'],
                ];
            }
        }

        //maths to calculate the percentage values
        $total_for_active = count($active_tv_campaign);
        $total_pending = count($pending_tv_campaign);
        $total_finished = count($finished_tv_campaigns);
        $total_campaigns_tv = count($all_tv_campaigns);

        //percentage values
        $perc_active = ($total_for_active / $total_campaigns_tv) * 100;
        $perc_finished = ($total_finished / $total_campaigns_tv) * 100;
        $perc_pending = ($total_pending / $total_campaigns_tv) * 100;

        return (['per_active' => $perc_active, 'per_finished' => $perc_finished, 'per_pending' => $perc_pending]);
    }

    public function radio($agency_id)
    {
        $today = date("Y-m-d");
        $all_tv_campaigns = [];
        $finished_tv_campaigns = [];
        $pending_tv_campaign = [];
        $active_tv_campaign = [];

        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where broadcaster IN (SELECT b.id from broadcasters as b, campaignChannels as c where b.channel_id = c.id AND c.channel = 'Radio')");

        foreach ($campaigns as $campaign){
            $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($campaign->channel) AND channel = 'Radio' ");
                $all_tv_campaigns[] = [
                    'campaign_id' => $campaign->campaign_id,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->stop_date,
                    'channel' => $channels[0]->channel,
                    'channel_id' => $channels[0]->id,
                ];

        }

        foreach ($all_tv_campaigns as $all_tv_campaign){
            if($today > $all_tv_campaign['end_date']){
                $finished_tv_campaigns[] = [
                    'campaign_id' => $all_tv_campaign['campaign_id'],
                    'start_date' => $all_tv_campaign['start_date'],
                    'end_date' => $all_tv_campaign['end_date'],
                    'channel' => $all_tv_campaign['channel'],
                    'channel_id' => $all_tv_campaign['channel_id'],
                ];
            }
        }

        foreach ($all_tv_campaigns as $all_tv_campaign){
            if($today < $all_tv_campaign['start_date']){
                $pending_tv_campaign[] = [
                    'campaign_id' => $all_tv_campaign['campaign_id'],
                    'start_date' => $all_tv_campaign['start_date'],
                    'end_date' => $all_tv_campaign['end_date'],
                    'channel' => $all_tv_campaign['channel'],
                    'channel_id' => $all_tv_campaign['channel_id'],
                ];
            }
        }

        foreach ($all_tv_campaigns as $all_tv_campaign){
            if($today >= $all_tv_campaign['start_date'] && $today <= $all_tv_campaign['end_date']){
                $active_tv_campaign[] = [
                    'campaign_id' => $all_tv_campaign['campaign_id'],
                    'start_date' => $all_tv_campaign['start_date'],
                    'end_date' => $all_tv_campaign['end_date'],
                    'channel' => $all_tv_campaign['channel'],
                    'channel_id' => $all_tv_campaign['channel_id'],
                ];
            }
        }

        //maths to calculate the percentage values
        $total_for_active = count($active_tv_campaign);
        $total_pending = count($pending_tv_campaign);
        $total_finished = count($finished_tv_campaigns);
        $total_campaigns_tv = count($all_tv_campaigns);

        //percentage values
        $perc_active = ($total_for_active / $total_campaigns_tv) * 100;
        $perc_finished = ($total_finished / $total_campaigns_tv) * 100;
        $perc_pending = ($total_pending / $total_campaigns_tv) * 100;

        return (['per_active' => $perc_active, 'per_finished' => $perc_finished, 'per_pending' => $perc_pending]);
    }

    public function dashboardCampaigns(DataTables $dataTables, Request $request)
    {
            //campaigns
            $agency_id = Session::get('agency_id');
            $broadcaster_id = Session::get('broadcaster_id');
            if($agency_id){
                if($request->has('start_date') && $request->has('stop_date')) {
                    $start_date = $request->start_date;
                    $stop_date = $request->stop_date;
                    $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, 
                                                                            p.total, b.name as brand_name, c.campaign_reference from campaignDetails as c_d 
                                                                            INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id 
                                                                            INNER JOIN campaigns as c ON c.id = c_d.campaign_id
                                                                            INNER JOIN brands as b ON b.id = c_d.brand where  c_d.agency = '$agency_id' and c_d.adslots  > 0 and 
                                                                            c_d.stop_date > '$start_date' and c_d.stop_date > '$stop_date' GROUP BY c_d.campaign_id ORDER BY c_d.time_created DESC");
                }else{
                    $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, 
                                                                             p.total, b.name as brand_name, c.campaign_reference from campaignDetails as c_d
                                                                              INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                                                               INNER JOIN campaigns as c ON c.id = c_d.campaign_id 
                                                                               INNER JOIN brands as b ON b.id = c_d.brand where  c_d.agency = '$agency_id' and 
                                                                               c_d.adslots  > 0 GROUP BY c_d.campaign_id ORDER BY c_d.time_created DESC");
                }
            }else if($broadcaster_id){
                if($request->has('start_date') && $request->has('stop_date')) {
                    $start_date = $request->start_date;
                    $stop_date = $request->stop_date;
                    $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name, c.campaign_reference from campaignDetails as c_d, payments as p, campaigns as c, brands as b where c.id = c_d.campaign_id and p.campaign_id = c_d.campaign_id and c_d.brand = b.id and c_d.broadcaster = '$broadcaster_id' and c_d.adslots  > 0 and c_d.stop_date > '$start_date' and c_d.stop_date > '$stop_date' ORDER BY c_d.time_created DESC");
                }else{
                    $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name, c.campaign_reference from campaignDetails as c_d, payments as p, campaigns as c, brands as b where c.id = c_d.campaign_id and p.campaign_id = c_d.campaign_id and c_d.brand = b.id and c_d.broadcaster = '$broadcaster_id' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");
                }
            }

            $campaigns_datatables = Utilities::getCampaignDatatables($all_campaigns);


        return $dataTables->collection($campaigns_datatables)
            ->addColumn('name', function ($campaigns_datatables) {
                if(Session::has('agency_id')){
                    return '<a href="'.route('agency.campaign.details', ['id' => $campaigns_datatables['campaign_id']]).'">'.$campaigns_datatables['name'].'</a>';
                }else if(Session::has('broadcaster_id')){
                    return '<a href="'.route('broadcaster.campaign.details', ['id' => $campaigns_datatables['campaign_id']]).'">'.$campaigns_datatables['name'].'</a>';
                }
            })
            ->editColumn('status', function ($campaigns_datatables){
                if($campaigns_datatables['status'] === "Finished"){
                    return '<span class="span_state status_danger">Finished</span>';
                }elseif ($campaigns_datatables['status'] === "Active"){
                    return '<span class="span_state status_success">Active</span>';
                }else{
                    return '<span class="span_state status_pending">Pending</span>';
                }
            })
            ->rawColumns(['status' => 'status', 'name' => 'name'])
            ->addIndexColumn()
            ->make(true);


    }

    public function campaignManagementDashbaord()
    {
        $broadcaster = Session::get('broadcaster_id');
        //total volume of campaigns
        $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaignDetails where broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
        $c_vol = [];
        $c_month = [];

        foreach ($camp_vol as $ca) {
            $c_vol[] = $ca->volume;
        }

        foreach ($camp_vol as $ca) {
            $month = ($ca->month);
            $c_month[] = $month;
        }

        $c_volume = json_encode($c_vol);
        $c_mon = json_encode($c_month);

        $today_date = date("Y-m-d");

//            all clients
        $clients = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE broadcaster_id = '$broadcaster' ORDER BY time_created DESC");

//            pending invoices
        $pending_invoices = Utilities::switch_db('api')->select("SELECT * FROM invoiceDetails where broadcaster_id = '$broadcaster' AND status = 0 ");

//            all_brands
        $all_brands = Utilities::switch_db('api')->select("SELECT * FROM brands where broadcaster_agency = '$broadcaster'");

        $active_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where broadcaster = '$broadcaster' AND start_date <= '$today_date' AND stop_date > '$today_date' ");


        $broadcaster_info = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster'");

        $pending_mpos = Utilities::switch_db('api')->select("SELECT * FROM mpoDetails where is_mpo_accepted = 0 and broadcaster_id = '$broadcaster'");

        return view('broadcaster_module.dashboard.campaign_management.dashboard')->with(['volume' => $c_volume, 'month' => $c_mon, 'broadcaster_info' => $broadcaster_info,
                                                                                                'walkins' => $clients, 'pending_invoices' => $pending_invoices, 'brands' => $all_brands, 'active_campaigns' => $active_campaigns, 'pending_mpos' => $pending_mpos]);

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
                                                            from campaignDetails where broadcaster = '$broadcaster_id' AND day_parts != '' 
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
        $adslots = [];
        $months = [];
        $adslot_monthly = [];
        $periodic = Utilities::switch_db('api')->select("SELECT count(id) as tot_camp, SUM(adslots) as adslot, time_created as days from campaignDetails WHERE broadcaster = '$broadcaster_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
        $price = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price, time_created as days from paymentDetails WHERE broadcaster = '$broadcaster_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");

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
        $adslot_ids = Utilities::switch_db('api')->select("SELECT adslots_id from campaignDetails where broadcaster = '$broadcaster_id' ");
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
                                                            INNER JOIN brands as b ON b.walkin_id = i_d.walkins_id  WHERE 
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
                                                                INNER JOIN brands as b ON b.walkin_id = p.walkins_id 
                                                                where p.broadcaster = '$broadcaster_id' GROUP BY p.walkins_id ORDER BY total_price DESC LIMIT 10");
            foreach ($payments as $payment){
                $campaign_count = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign_count, SUM(adslots) as total_adslots from campaignDetails where walkins_id = '$payment->walkins_id' AND broadcaster = '$broadcaster_id'");
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