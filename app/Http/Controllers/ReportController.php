<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Utilities;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
use Session;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // High value customer report
    public function HVCdata(Datatables $datatables, Request $request)
    {
        //high value customer
        $broadcaster = Session::get('broadcaster_id');
        $camp = [];
        $j = 1;

        if($request->has('start_date') && $request->has('stop_date'))
        {
            $start = $request->start_date;
            $end = $request->stop_date;
            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' AND walkins_id != '' AND time_created BETWEEN '$start' AND '$end' GROUP BY walkins_id LIMIT 10");
            foreach ($campaign as $c)
            {
                $user = Utilities::switch_db('api')->select("SELECT firstname, lastname from users where id='$c->walkins_id'");
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
                $camp[] = [
                    'id' => $j,
                    'number_of_campaign' => $c->total_campaign,
                    'total_adslot' => $c->total_adslot,
                    'customer_name' => $user[0]->firstname. ' ' .$user[0]->lastname,
                    'payment' => '&#8358;'.number_format($payments[0]->total_price, 2),
                    'date' => ($c->time),
                ];
                $j++;
            }
            return $datatables->collection($camp)
                ->make(true);
        }else{
            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' AND walkins_id != '' GROUP BY walkins_id LIMIT 10");
            foreach ($campaign as $c)
            {
                $user = Utilities::switch_db('api')->select("SELECT firstname, lastname from users where id='$c->walkins_id'");
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
                $camp[] = [
                    'id' => $j,
                    'number_of_campaign' => $c->total_campaign,
                    'total_adslot' => $c->total_adslot,
                    'customer_name' => $user[0]->firstname. ' ' .$user[0]->lastname,
                    'payment' => '&#8358;'.number_format($payments[0]->total_price, 2),
                    'date' => ($c->time),
                ];
                $j++;
            }
            return $datatables->collection($camp)
                ->make(true);
        }

    }

    //paid Invoice report
    public function PIdata(Datatables $datatables, Request $request)
    {
        $broadcaster = Session::get('broadcaster_id');
        $start = strtotime($request->start_date);
        $end = strtotime($request->stop_date);
        $invoice_array = [];
        $j = 1;
        if($request->has('start_date') && $request->has('stop_date'))
        {
            $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster' AND time_created BETWEEN '$start' AND '$end'");
            foreach ($inv as $i)
            {
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$i->walkins_id'");
                $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaigns where id='$i->campaign_id'");

                $invoice_array[] = [
                    'id' => $j,
                    'campaign_name' => $campaign_det[0]->campaign_name,
                    'customer' => $customer_name[0]->firstname . ' ' .$customer_name[0]->lastname,
                    'date' => date('Y-m-d', strtotime($i->time_created)),
                    'date_due' => ($campaign_det[0]->stop_date)
                ];
                $j++;
            }

            return $datatables->collection($invoice_array)
                ->make(true);
        }else{
            $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster' ");
            foreach ($inv as $i)
            {
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$i->walkins_id'");
                $campaign_det = Utilities::switch_db('api')->select("SELECT `name` as campaign_name, DATE_FORMAT(stop_date, '%Y-%m-%d') as stop_date from campaigns where id='$i->campaign_id'");

                $invoice_array[] = [
                    'id' => $j,
                    'campaign_name' => $campaign_det[0]->campaign_name,
                    'customer' => $customer_name[0]->firstname . ' ' .$customer_name[0]->lastname,
                    'date' => date('Y-m-d', strtotime($i->time_created)),
                    'date_due' => ($campaign_det[0]->stop_date)
                ];
                $j++;
            }

            return $datatables->collection($invoice_array)
                ->make(true);
        }

    }

    //Periodic sales report
    public function psData(Datatables $datatables, Request $request)
    {
        $start = strtotime($request->start_date);
        $end = strtotime($request->stop_date);
        if($request->has('start_date') && $request->has('stop_date'))
        {
            //periodic sales report
            $broadcaster = Session::get('broadcaster_id');
            $ads = [];
            $j = 1;
            $periodic = Utilities::switch_db('api')->select("SELECT id, adslots, `name`, brand, walkins_id, time_created as days from campaigns WHERE broadcaster = '$broadcaster' AND walkins_id != '' AND adslots != 0 AND time_created BETWEEN '$start' AND '$end'  ORDER BY time_created desc");
            foreach ($periodic as $pe)
            {
                $price = Utilities::switch_db('api')->select("SELECT amount, time_created as days from payments WHERE broadcaster = '$broadcaster' AND campaign_id = '$pe->id' ORDER BY time_created desc");
                $customer = Utilities::switch_db('api')->select("SELECT firstname, lastname from users WHERE id = '$pe->walkins_id'");
                $ads[] = [
                    'id' => $j,
                    'total_amount' => '&#8358;'.number_format($price[0]->amount, 2),
                    'adslot' => $pe->adslots,
                    'date' => ($pe->days),
                    'campaign_name' => $pe->name,
                    'brand' => $pe->brand,
                    'buyer' => $customer[0]->firstname.' '.$customer[0]->lastname,
                ];
                $j++;
            }

            return $datatables->collection($ads)
                ->make(true);
        }else{
            //periodic sales report
            $broadcaster = Session::get('broadcaster_id');
            $ads = [];
            $j = 1;
            $periodic = Utilities::switch_db('api')->select("SELECT id, adslots, `name`, brand, walkins_id, time_created as days from campaigns WHERE broadcaster = '$broadcaster' AND walkins_id != '' AND adslots != 0 ORDER BY time_created desc");
            foreach ($periodic as $pe)
            {
                $price = Utilities::switch_db('api')->select("SELECT amount, time_created as days from payments WHERE broadcaster = '$broadcaster' AND campaign_id = '$pe->id' ORDER BY time_created desc");
                $customer = Utilities::switch_db('api')->select("SELECT firstname, lastname from users WHERE id = '$pe->walkins_id'");
                $ads[] = [
                    'id' => $j,
                    'total_amount' => '&#8358;'.number_format($price[0]->amount, 2),
                    'adslot' => $pe->adslots,
                    'date' => ($pe->days),
                    'campaign_name' => $pe->name,
                    'brand' => $pe->brand,
                    'buyer' => $customer[0]->firstname.' '.$customer[0]->lastname,
                ];
                $j++;
            }

            return $datatables->collection($ads)
                ->make(true);
        }

    }

    //Total Volume of Campaign
    public function tvcData(Datatables $datatables, Request $request)
    {
        $start = strtotime($request->start_date);
        $end = strtotime($request->stop_date);
        if($request->has('start_date') && $request->has('stop_date'))
        {
            //total volume of campaigns
            $broadcaster = Session::get('broadcaster_id');
            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, time_created as month from campaigns where broadcaster = '$broadcaster' AND time_created BETWEEN '$start' AND '$end' GROUP BY time_created ORDER BY time_created desc");
            $c_vol = [];
            $j = 1;
            foreach ($camp_vol as $ca)
            {
                $c_vol[] = [
                    'id' => $j,
                    'volume' => $ca->volume,
                    'date' => date("F", $ca->month).', '.date('Y', $ca->month),
                ];
                $j++;
            }

            return $datatables->collection($c_vol)
                ->make(true);
        }else{
            //total volume of campaigns
            $broadcaster = Session::get('broadcaster_id');
            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaigns where broadcaster = '$broadcaster' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ORDER BY time_created desc");
            $c_vol = [];
            $j = 1;
            foreach ($camp_vol as $ca)
            {
                $c_vol[] = [
                    'id' => $j,
                    'volume' => $ca->volume,
                    'date' => ($ca->month),
                ];
                $j++;
            }

            return $datatables->collection($c_vol)
                ->make(true);
        }

    }

    //High performing Dayparts
    public function hpdData(Datatables $datatables, Request $request)
    {
        $start = strtotime($request->start_date);
        $end = strtotime($request->stop_date);
        $j = 1;
        if($request->has('start_date') && $request->has('stop_date'))
        {
            $broadcaster = Session::get('broadcaster_id');
            $dayp = Utilities::switch_db('api')->select("SELECT COUNT(id) as campaigns, time_created as time_created, day_parts 
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
                    'id' => $j,
                    'daypart' => $day_partt,
                    'percentage' => $day_percent.'%',
                    'date' => date('Y-m-d', strtotime($day->time_created)),
                ];
                $j++;
            }

            return $datatables->collection($dayp_name)
                ->make(true);
        }else{
            $broadcaster = Session::get('broadcaster_id');
            $dayp = Utilities::switch_db('api')->select("SELECT COUNT(id) as campaigns, time_created as time_created, day_parts 
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
                    'id' => $j,
                    'daypart' => $day_partt,
                    'percentage' => $day_percent.'%',
                    'date' => date('Y-m-d', strtotime($day->time_created)),
                ];
                $j++;
            }

            return $datatables->collection($dayp_name)
                ->make(true);
        }

    }

    //High Performing Day
    public function hpdaysData(Datatables $datatables, Request $request)
    {
        $start = strtotime($request->start_date);
        $end = strtotime($request->stop_date);
        if($request->has('start_date') && $request->has('stop_date'))
        {
            $broadcaster = Session::get('broadcaster_id');
            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, time_created as days from campaigns where broadcaster = '$broadcaster' AND day_parts != '' AND time_created BETWEEN '$start' AND '$end' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc LIMIT 1");
            $day_name = [];
            $b = [];
            for($j = 0; $j < count($days); $j++)
            {
                $b[] = $days[$j]->tot_camp;
            }
            $s = array_sum($b);
            $j = 1;
            foreach ($days as $dd)
            {
                $perc_days = (($dd->tot_camp) / $s) * 100;
                $day_name[] = [
                    'id' => $j,
                    'day' => date('l', strtotime($dd->days)) .' '. date('F', strtotime($dd->days)).' '. date('d', strtotime($dd->days)). ',' .date('Y', strtotime($dd->days)),
                    'percentage' => $perc_days.'%',
                    'week' => date('W', strtotime($dd->days)),
                ];
            }

            return $datatables->collection($day_name)
                ->make(true);
        }else{
            $broadcaster = Session::get('broadcaster_id');
            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, time_created as days from campaigns where broadcaster = '$broadcaster' AND day_parts != '' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc ");
            $day_name = [];
            $b = [];
            for($j = 0; $j < count($days); $j++)
            {
                $b[] = $days[$j]->tot_camp;
            }
            $s = array_sum($b);
            $j = 1;
            foreach ($days as $dd)
            {
                $perc_days = (($dd->tot_camp) / $s) * 100;
                $day_name[] = [
                    'id' => $j,
                    'day' => date('l', strtotime($dd->days)) .' '. date('F', strtotime($dd->days)).' '. date('d', strtotime($dd->days)). ',' .date('Y', strtotime($dd->days)),
                    'percentage' => $perc_days.'%',
                    'week' => date('W', strtotime($dd->days)),
                ];
            }

            return $datatables->collection($day_name)
                ->make(true);
        }

    }

}
