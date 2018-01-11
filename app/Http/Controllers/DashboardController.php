<?php

namespace Vanguard\Http\Controllers;

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
        // high value customer
        $broadcaster = Session::get('broadcaster_id');
        $camp = [];
        $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' AND walkins_id != '' GROUP BY walkins_id LIMIT 10");
        foreach ($campaign as $c)
        {
            $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$c->walkins_id'");
            $user_id = $walk[0]->user_id;
            $user = Utilities::switch_db('api')->select("SELECT firstname, lastname from users where id = '$user_id'");
            $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
            $camp[] = [
                'number_of_campaign' => $c->total_campaign,
                'user_id' => $c->walkins_id,
                'total_adslot' => $c->total_adslot,
                'customer_name' => $user[0]->firstname. ' ' .$user[0]->lastname,
                'payment' => $payments[0]->total_price,
            ];
        }
        $c = ((object) $camp);

        //paid invoices
        $invoice_array = [];
        $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster' LIMIT 10");
        foreach ($inv as $i)
        {
            $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$i->walkins_id'");
            $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaigns where id='$i->campaign_id'");
            $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$i->walkins_id'");
            $user_id = $walk[0]->user_id;
            $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$user_id'");
            $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaigns where id='$i->campaign_id'");
            $invoice_array[] = [
                'campaign_name' => $campaign_det[0]->campaign_name,
                'customer' => $customer_name[0]->firstname . ' ' .$customer_name[0]->lastname,
                'date' => date('Y-m-d', strtotime($i->time_created)),
                'date_due' => $campaign_det[0]->stop_date,
            ];
        }
        $invoice = (object) $invoice_array;

        //total volume of campaigns
        $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaigns where broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ORDER BY time_created desc");
        $c_vol = [];
        $c_month = [];
        foreach ($camp_vol as $ca)
        {
            $c_vol[] = $ca->volume;
        }

        foreach ($camp_vol as $ca)
        {
            $month = ($ca->month);
            $c_month[] = $month;
        }
        $c_volume = json_encode($c_vol);
        $c_mon = json_encode($c_month);

        //High performing Dayparts
        $dayp = Utilities::switch_db('api')->select("SELECT COUNT(id) as campaigns, DATE_FORMAT(time_created, '%a') as time_created, day_parts 
                                                        from campaigns WHERE broadcaster = '$broadcaster' AND day_parts != '' 
                                                        GROUP BY broadcaster, time_created");
        $dayp_name = [];
        $d = [];
        for($i = 0; $i < count($dayp); $i++)
        {
            $d[] = $dayp[$i]->campaigns;
        }
        $s = array_sum($d);
        foreach ($dayp as $day)
        {
            $day_p = $day->day_parts;
            $query = Utilities::switch_db('api')->select("SELECT day_parts from dayParts WHERE id IN ('$day_p')");
            $day_percent = (($day->campaigns) / $s) * 100;
            $day_partt = isset($query[0]) ? $query[0]->day_parts : "";
            $dayp_name[] = [
                'name' => $day_partt,
                'y' => $day_percent,
                'date' => $day->time_created
            ];
        }

        $day_pie = json_encode($dayp_name);

        //high performing days
        $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, DATE_FORMAT(time_created, '%a') as days from campaigns where broadcaster = '$broadcaster' AND day_parts != '' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc LIMIT 1");
        $day_name = [];
        $b = [];
        for($j = 0; $j < count($days); $j++)
        {
            $b[] = $days[$j]->tot_camp;
        }
        $s = array_sum($b);

        foreach ($days as $dd)
        {
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
        $periodic = Utilities::switch_db('api')->select("SELECT count(id) as tot_camp, SUM(adslots) as adslot, time_created as days from campaigns WHERE broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ORDER BY time_created desc");
        foreach ($periodic as $pe)
        {
            $price = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price, time_created as days from payments WHERE broadcaster = '$broadcaster' AND time_created = '$pe->days' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ORDER BY time_created desc");
            $ads[] = [
                'total' => $price[0]->total_price,
                'adslot' => $pe->adslot,
                'date' => date('M, Y', strtotime($pe->days)),
            ];

        }
        foreach ($ads as $a)
        {
            $months[] = $a['date'];
        }
        foreach ($ads as $a)
        {
            $prr[] = $a['total'];
        }
        foreach ($ads as $a)
        {
            $slot[] = (integer) $a['adslot'];
        }

        $ads_no = json_encode($slot);
        $tot_pri = json_encode($prr);
        $mon = json_encode($months);

//        Inventory fill rate
        $today = getDate();
        $today_day = $today['weekday'];
        $rate_c = [];
        $get_day_id = Utilities::switch_db('api')->select("SELECT id as day_id from days WHERE day = '$today_day'");
        $t_day = $get_day_id[0]->day_id;
        $get_ratecards_for_this_day = Utilities::switch_db('api')->select("SELECT * from rateCards WHERE day = '$t_day' AND broadcaster = '$broadcaster'");
        foreach ($get_ratecards_for_this_day as $gr)
        {
            $adslot = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_slot, rate_card from adslots WHERE rate_card = '$gr->id'");
//            $campaign = Utilities::switch_db('reports')->select("SELECT SUM(adslots) as total_sold from campaigns WHERE broadcaster = '$broadcaster' AND  GROUP BY ");
            $rate_c[] = $adslot;
        }




        return view('dashboard.default')->with(['campaign' => $c, 'volume' => $c_volume, 'month' => $c_mon, 'high_dayp' => $day_pie, 'days' => $days_data, 'adslot' => $ads_no, 'price' => $tot_pri, 'mon' => $mon, 'invoice' => $invoice]);
    }




}