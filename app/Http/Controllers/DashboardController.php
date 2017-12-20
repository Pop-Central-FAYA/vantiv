<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Utilities;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $camp = [];
        $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE walkins_id != '' GROUP BY walkins_id LIMIT 10");
        foreach ($campaign as $c)
        {
            $user = Utilities::switch_db('api')->select("SELECT firstname, lastname from users where id='$c->walkins_id'");
            $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE campaign_id = '$c->id'");
            $camp[] = [
                'campaign_id' => $c->id,
                'number_of_campaign' => $c->total_campaign,
                'user_id' => $c->walkins_id,
                'total_adslot' => $c->total_adslot,
                'customer_name' => $user[0]->firstname. ' ' .$user[0]->lastname,
                'payment' => $payments[0]->total_price,
            ];
        }

        $c = ((object) $camp);

        $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, time_created as month from campaigns GROUP BY time_created ORDER BY time_created desc");
        $c_vol = [];
        $c_month = [];
        foreach ($camp_vol as $ca)
        {
            $c_vol[] = $ca->volume;
        }

        foreach ($camp_vol as $ca)
        {
            $month = (date("F", $ca->month));
            $c_month[] = $month;
        }
        $c_volume = json_encode($c_vol);
        $c_mon = json_encode($c_month);

        $dayp = Utilities::switch_db('api')->select("SELECT COUNT(id) as campaigns, time_created, day_parts from campaigns WHERE day_parts != '' GROUP BY day_parts, time_created");
        $dayp_name = [];
        $d = [];
        $date = [];
        for($i = 0; $i < count($dayp); $i++)
        {
            $d[] = $dayp[$i]->campaigns;
        }
        $s = array_sum($d);
        foreach ($dayp as $day)
        {
            $query = Utilities::switch_db('api')->select("SELECT day_parts from dayParts WHERE id = '$day->day_parts'");
            $day_percent = (($day->campaigns) / $s) * 100;
            $dayp_name[] = [
                'name' => $query[0]->day_parts,
                'y' => $day_percent,
                'date' => $day->time_created
            ];
        }
        $day_pie = json_encode($dayp_name);

        $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, time_created as days from campaigns where day_parts != '' GROUP BY WEEK(time_created)");
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
                'name' => date('l', $dd->days),
                'y' => $perc_days
            ];
        }
        $days_data = json_encode($day_name);
        return view('dashboard.default')->with(['campaign' => $c, 'volume' => $c_volume, 'month' => $c_mon, 'high_dayp' => $day_pie, 'days' => $days_data]);
    }




}