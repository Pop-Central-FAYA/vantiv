<?php

namespace Vanguard\Http\Controllers;

use Hamcrest\Util;
use Vanguard\Http\Requests\Request;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Auth;
use Carbon\Carbon;
use Session;

class DashboardController extends Controller
{

    public function index()
    {
        //Broadcaster Dashboard module
        $role = \DB::table('role_user')->where('user_id', Auth::user()->id)->first();
        if ($role->role_id === 3) {
            // high value customer
            $broadcaster = Session::get('broadcaster_id');
            $camp = [];
            $user_details = '';
            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, agency_broadcaster, agency, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster' AND walkins_id != '' GROUP BY agency LIMIT 10");
            foreach ($campaign as $c) {
                $agent_id = $c->agency;
                $user_agency = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from agents where id = '$agent_id')");
                if($user_agency){
                    $user_details = $user_agency[0]->firstname . ' ' . $user_agency[0]->lastname;
                }
                $user_advertiser = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from advertisers where id = '$agent_id')");
                if($user_advertiser){
                    $user_details = $user_advertiser[0]->firstname . ' ' . $user_advertiser[0]->lastname;
                }
                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$agent_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
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
            $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster' ORDER BY time_created DESC LIMIT 10");
            foreach ($inv as $i) {
                $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$i->walkins_id'");
                $user_id = $walk[0]->user_id;
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$user_id'");
                $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaigns where id='$i->campaign_id'");
                $invoice_array[] = [
                    'campaign_name' => isset($campaign_det[0]->campaign_name) ? $campaign_det[0]->campaign_name : '',
                    'customer' => $customer_name[0]->firstname . ' ' . $customer_name[0]->lastname,
                    'date' => date('Y-m-d', strtotime($i->time_created)),
                    'date_due' => $campaign_det[0]->stop_date,
                ];
            }
            $invoice = (object) $invoice_array;

            //total volume of campaigns
            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaigns where broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
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
            $slots = Utilities::switch_db('api')->select("SELECT adslots_id from campaigns where broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster'");
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
            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, DATE_FORMAT(time_created, '%a %M %d, %Y') as days from campaigns where broadcaster = '$broadcaster' AND day_parts != '' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc LIMIT 1");
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
            $periodic = Utilities::switch_db('api')->select("SELECT count(id) as tot_camp, SUM(adslots) as adslot, time_created as days from campaigns WHERE broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
            $price = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price, time_created as days from payments WHERE broadcaster = '$broadcaster' or agency_broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");

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

            return view('dashboard.default')->with(['campaign' => $camp, 'volume' => $c_volume, 'month' => $c_mon, 'high_dayp' => $day_pie, 'days' => $days_data, 'adslot' => $ads_no, 'price' => $tot_pri, 'mon' => $mon, 'invoice' => $invoice]);

        } else if ($role->role_id === 4) {
            $allBroadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters");
            $agency_id = Session::get('agency_id');
            $camp_prod = Utilities::switch_db('api')->select("SELECT id, product from campaigns where agency = '$agency_id'");
            $pe = $this->broadcasterFilter($agency_id);
            $date = [];
            $bra = [];
            $tot = [];
            $amm = [];
            $dat = [];
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
            $pro_period = $this->periodic_spent($agency_id);
            $periodic_name = (json_encode($pro_period['name']));
            $periodic_data = (json_encode($pro_period['data']));


            #Budget pacing report
            $b = $this->budgetPacing($agency_id);
            foreach ($b as $bud) {
                $amm[] = $bud['total'];
            }
            foreach ($b as $bud) {
                $dat[] = $bud['date'];
            }

            $amm_bud = json_encode($amm);
            $date_bud = json_encode($dat);

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

            #region
//            $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total from payments WHERE campaign_id IN(SELECT id from campaigns where adslots_id IN(SELECT * from ))");

            return view('agency.dashboard.new_dashboard')->with(['broadcaster' => $allBroadcaster,
                                                                        'invoice_approval' => $invoice_approval,
                                                                        'all_invoices' => $invoice_campaign_details,
                                                                        'count_brands' => $count_brands, 'count_invoice' => $count_invoice,
                                                                        'count_campaigns' => $count_campaigns,  'count_client' => $count,
                                                                        'date' => $d, 'amount' => $am, 'name' => $na, 'camp_prod' => $camp_prod,
                                                                        'periodic_name' => $periodic_name, 'periodic_data' => $periodic_data, 'amount_bud' => $amm_bud, 'date_bud' => $date_bud,
                                                                        'invoice_unapproval' => $invoice_unapproval]);

        } else if ($role->role_id === 6) {

            $allBroadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters");

            $date = [];
            $tot = [];
            $bra = [];

            $advertiser_id = Session::get('advertiser_id');

            $camp_prod = Utilities::switch_db('api')->select("SELECT id,product from campaigns where agency = '$advertiser_id'");

            $pe = $this->broadcasterFilter($advertiser_id);

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
            $pro_period = $this->periodic_spent($advertiser_id);
            $periodic_name = (json_encode($pro_period['name']));
            $periodic_data = (json_encode($pro_period['data']));
//            $periodic_to_product = (json_encode($pro_period));

            #Budget pacing report
            $amm = [];
            $dat = [];
            $b = $this->budgetPacing($advertiser_id);
            foreach ($b as $bud) {
                $amm[] = $bud['total'];
            }
            foreach ($b as $bud) {
                $dat[] = $bud['date'];
            }

            $amm_bud = json_encode($amm);

            $date_bud = json_encode($dat);

            $invoice_campaign_details = Api::allInvoiceAdvertiserorAgency($advertiser_id);

            #approval
            $invoice_approval = Api::countApproved($advertiser_id);

            #unapproval
            $invoice_unapproval = Api::countUnapproved($advertiser_id);

            #all campaigns
            $count_campaigns = Api::countCampaigns($advertiser_id);

            #invoices
            $count_invoice =Api::countInvoices($advertiser_id);

            #count Brands
            $count_brands = Api::countBrands($advertiser_id);

            #count files
            $count_files = Api::countFiles($advertiser_id);

            return view('advertisers.dashboard.new_dashboard')->with(['broadcaster' => $allBroadcaster, 'date' => $d, 'amount' => $am,
                                                                            'name' => $na, 'camp_prod' => $camp_prod, 'amount_bud' => $amm_bud,
                                                                            'date_bud' => $date_bud, 'periodic_name' => $periodic_name, 'periodic_data' => $periodic_data,
                                                                            'all_invoices' => $invoice_campaign_details,
                                                                            'invoice_approval' => $invoice_approval,
                                                                            'invoice_unapproval' => $invoice_unapproval,
                                                                            'count_campaigns_advertiser' => $count_campaigns,
                                                                            'count_invoice' => $count_invoice,
                                                                            'count_brand' => $count_brands,
                                                                            'count_files' => $count_files]);

        }else if ($role->role_id === 7){

            //Broadcaster user dashboard

            $broadcaster_user = Session::get('broadcaster_user_id');
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;

            $camp = [];
            $user_details = '';
            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, agency, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND walkins_id != '' GROUP BY walkins_id LIMIT 10");
            foreach ($campaign as $c) {

                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$c->walkins_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
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
            $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster' AND agency_id = '$broadcaster_user' ORDER BY time_created DESC LIMIT 10");
            foreach ($inv as $i) {
                $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$i->walkins_id'");
                $user_id = $walk[0]->user_id;
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$user_id'");
                $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaigns where id='$i->campaign_id'");
                $invoice_array[] = [
                    'campaign_name' => isset($campaign_det[0]->campaign_name) ? $campaign_det[0]->campaign_name : '',
                    'customer' => $customer_name[0]->firstname . ' ' . $customer_name[0]->lastname,
                    'date' => date('Y-m-d', strtotime($i->time_created)),
                    'date_due' => $campaign_det[0]->stop_date,
                ];
            }
            $invoice = (object) $invoice_array;

            //total volume of campaigns
            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
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
            $slots = Utilities::switch_db('api')->select("SELECT adslots_id from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user'");
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
            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, DATE_FORMAT(time_created, '%a %M %d, %Y') as days from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND day_parts != '' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc LIMIT 1");
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
            $periodic = Utilities::switch_db('api')->select("SELECT count(id) as tot_camp, SUM(adslots) as adslot, time_created as days from campaigns WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");
            $price = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price, time_created as days from payments WHERE broadcaster = '$broadcaster' AND agency_id = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ");

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

        }
    }

    public function filterByBroad()
    {
        $b_id = request()->br_id;
        $broadcaster_brand = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id='$b_id'");
        $pe = [];
        $tot = [];
        $date = [];
        $bra = [];
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from payments WHERE agency_broadcaster = '$b_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
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

    public function clientDashboard()
    {
        $agency_id = Session::get('agency_id');
        $brand = [];
        $tot = [];
        $dates = [];
        $braa = [];
        $bra = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id IN (SELECT id from walkIns where agency_id = '$agency_id')");

        $br = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id IN (SELECT id from walkIns where agency_id = '$agency_id') LIMIT 1");
        foreach ($br as $b) {
            $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created FROM payments WHERE campaign_id IN (SELECT id from campaigns WHERE brand = '$b->id' AND agency = '$agency_id') GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
            if (!$camp_pay) {
                $total = 0;
                $date = 0;
            } else {

                foreach ($br as $b) {
                    $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created FROM payments WHERE campaign_id IN (SELECT id from campaigns WHERE brand = '$b->id' AND agency = '$agency_id') GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
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
                    $tot[] = $b['total'];
                }
                foreach ($brand as $b) {
                    $braa[] = $b['brand'];
                }
                foreach ($brand as $b) {
                    $dates[] = $b['date'];
                }


                $d = json_encode($dates);
                $am = json_encode($tot);
                $na = json_encode($braa);

                return view('clients.dashboard.dashboard')->with(['brand' => $bra, 'date' => $d, 'amount' => $am, 'name' => $na]);
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
        foreach ($br as $b) {
            $camp_pay = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created FROM payments WHERE campaign_id IN (SELECT id from campaigns WHERE brand = '$b->id' AND agency = '$agency_id') GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
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
            $tot[] = $b['total'];
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
        $broadAgency = Utilities::switch_db('api')->select("SELECT agency_broadcaster as broad_ag from campaigns where agency = '$agency_id' LIMIT 1");
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
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from payments WHERE agency_broadcaster = '$age_broad_id' AND agency_id = '$agency_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
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
            $b[] = [
                'date' => date('Y-m-d', strtotime($t->date)),
                'total' => (integer)$t->bal,
            ];
        }

        return $b;
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
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from payments WHERE agency_broadcaster = '$b_id' AND agency_id = '$agency_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
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
        $broad = Utilities::switch_db('api')->select("SELECT SUM(amount) as total, time_created from payments WHERE agency_broadcaster = '$b_id' AND agency_id = '$advertiser_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m')");
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
        $name = [];
        $data = [];
        $date = date('Y-m', time());
        $pro_cam = Utilities::switch_db('api')->select("SELECT * from campaigns where agency = '$agency_id' AND DATE_FORMAT(time_created, '%Y-%m') = '$date' ");
        foreach ($pro_cam as $pr){
            $campaign_p = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$pr->id'");
            $paym = Utilities::switch_db('api')->select("SELECT SUM(amount) as total from payments where agency_id = '$agency_id' AND DATE_FORMAT(time_created, '%Y-%m') = '$date'");
            $pro_period[] = [
                'name' => $pr->product,
                'y' => (($campaign_p[0]->amount) / $paym[0]->total) * 100,
            ];
        }

        foreach ($pro_period as $p){
            $name[] = $p['name'];
        }

        foreach ($pro_period as $p){
            $data[] = $p['y'];
        }

        return (['name' => $name, 'data' => $data]);
    }


}