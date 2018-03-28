<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;

class BroadcasterUserReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('broadcaster_user.reports.index');
    }

    // High value customer report
    public function HVCdata(Datatables $datatables, Request $request)
    {
        //high value customer
        $broadcaster_user = Session::get('broadcaster_user_id');
        $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
        $broadcaster = $broadcaster_id[0]->broadcaster_id;

        $camp = [];
        $j = 1;

        if($request->has('start_date') && $request->has('stop_date'))
        {
            $start = $request->start_date;
            $end = $request->stop_date;

            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, agency_broadcaster, agency, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' walkins_id != '' AND time_created BETWEEN '$start' AND '$end' GROUP BY walkins_id ");
            foreach ($campaign as $c) {


                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$c->walkins_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
                $camp[] = [
                    'id' => $j,
                    'number_of_campaign' => $c->total_campaign,
                    'user_id' => $c->walkins_id,
                    'total_adslot' => $c->total_adslot,
                    'customer_name' => $user_details,
                    'payment' => '&#8358;'.number_format($payments[0]->total_price, 2),
                ];
                $j++;
            }

            array_multisort(array_column($camp, 'payment'), SORT_DESC, $camp);

            return $datatables->collection($camp)
                ->make(true);
        }else{
            $campaign = Utilities::switch_db('api')->select("SELECT COUNT(id) as total_campaign, agency_broadcaster, agency, time_created as `time`, id, walkins_id, SUM(adslots) as total_adslot from campaigns WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND walkins_id != '' GROUP BY walkins_id ");
            foreach ($campaign as $c) {
                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$c->walkins_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total_price from payments WHERE walkins_id = '$c->walkins_id'");
                $camp[] = [
                    'id' => $j,
                    'number_of_campaign' => $c->total_campaign,
                    'user_id' => $c->walkins_id,
                    'total_adslot' => $c->total_adslot,
                    'customer_name' => $user_details,
                    'payment' => number_format($payments[0]->total_price, 2),
                ];
                $j++;
            }

            array_multisort(array_column($camp, 'payment'), SORT_DESC, $camp);

            return $datatables->collection($camp)
                ->make(true);
        }

    }

    //paid Invoice report
    public function PIdata(Datatables $datatables, Request $request)
    {
        $broadcaster_user = Session::get('broadcaster_user_id');
        $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
        $broadcaster = $broadcaster_id[0]->broadcaster_id;

        $start = $request->start_date;
        $end = $request->stop_date;
        $invoice_array = [];
        $j = 1;
        if($request->has('start_date') && $request->has('stop_date'))
        {
            $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster' and agency_id = '$broadcaster_user' AND time_created BETWEEN '$start' AND '$end'");
            foreach ($inv as $i)
            {
                $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$i->walkins_id'");
                $user_id = $walk[0]->user_id;
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$user_id'");
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
            $inv = Utilities::switch_db('api')->select("SELECT * from invoices WHERE broadcaster_id = '$broadcaster'AND agency_id = '$broadcaster_user' ");
            foreach ($inv as $i)
            {
                $walk = Utilities::switch_db('api')->select("SELECT user_id from walkIns where id='$i->walkins_id'");
                $user_id = $walk[0]->user_id;
                $customer_name = Utilities::switch_db('api')->select("SELECT firstname,lastname from users where id='$user_id'");
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
        $broadcaster_user = Session::get('broadcaster_user_id');
        $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
        $broadcaster = $broadcaster_id[0]->broadcaster_id;

        $start = $request->start_date;
        $end = $request->stop_date;
        if($request->has('start_date') && $request->has('stop_date'))
        {
            //periodic sales report
            $ads = [];
            $j = 1;

            $periodic = Utilities::switch_db('api')->select("SELECT id, agency, adslots, `name`, brand, walkins_id, time_created as days from campaigns WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND walkins_id != '' AND adslots != 0 AND ( time_created BETWEEN '$start' AND '$end') ORDER BY time_created desc");
            foreach ($periodic as $pe)
            {
                $price = Utilities::switch_db('api')->select("SELECT amount, time_created as days from payments WHERE campaign_id = '$pe->id'");
                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$pe->walkins_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }

                $brand = Utilities::switch_db('api')->select("SELECT * from brands where id = '$pe->brand'");
                $ads[] = [
                    'id' => $j,
                    'total_amount' => number_format($price[0]->amount, 2),
                    'adslot' => $pe->adslots,
                    'date' => date('Y-m-d', strtotime($pe->days)),
                    'campaign_name' => $pe->name,
                    'brand' => $brand[0]->name,
                    'buyer' => $user_details,
                ];
                $j++;
            }

            return $datatables->collection($ads)
                ->make(true);
        }else{
            //periodic sales report
            $ads = [];
            $j = 1;
            $periodic = Utilities::switch_db('api')->select("SELECT id,adslots,agency, `name`, brand, walkins_id, time_created as days from campaigns WHERE broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND walkins_id != '' AND adslots != 0 ORDER BY time_created desc");
            foreach ($periodic as $pe)
            {
                $price = Utilities::switch_db('api')->select("SELECT amount, time_created as days from payments WHERE campaign_id = '$pe->id' ");
                $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from walkIns where id = '$pe->walkins_id')");
                if($user_broad){
                    $user_details = $user_broad[0]->firstname . ' ' . $user_broad[0]->lastname;
                }

                $brand = Utilities::switch_db('api')->select("SELECT * from brands where id = '$pe->brand'");
                $ads[] = [
                    'id' => $j,
                    'total_amount' => '&#8358;'.number_format($price[0]->amount, 2),
                    'adslot' => $pe->adslots,
                    'date' => date('Y-m-d', strtotime($pe->days)),
                    'campaign_name' => $pe->name,
                    'brand' => $brand[0]->name,
                    'buyer' => $user_details,
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
        $broadcaster_user = Session::get('broadcaster_user_id');
        $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
        $broadcaster = $broadcaster_id[0]->broadcaster_id;
        $start = $request->start_date;
        $end = $request->stop_date;
        if($request->has('start_date') && $request->has('stop_date'))
        {
            //total volume of campaigns

            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, time_created as month from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND time_created BETWEEN '$start' AND '$end' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ORDER BY time_created desc");
            $c_vol = [];
            $j = 1;
            foreach ($camp_vol as $ca)
            {
                $c_vol[] = [
                    'id' => $j,
                    'volume' => $ca->volume,
                    'date' => date("F", strtotime($ca->month)).', '.date('Y', strtotime($ca->month)),
                ];
                $j++;
            }

            return $datatables->collection($c_vol)
                ->make(true);
        }else{
            //total volume of campaigns

            $camp_vol = Utilities::switch_db('api')->select("SELECT COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as `month` from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' GROUP BY DATE_FORMAT(time_created, '%Y-%m') ORDER BY time_created desc");
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
        $start = $request->start_date;
        $end = $request->stop_date;
        $broadcaster_user = Session::get('broadcaster_user_id');
        $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
        $broadcaster = $broadcaster_id[0]->broadcaster_id;
        $j = 1;
        if($request->has('start_date') && $request->has('stop_date'))
        {
            $all_slots = [];
            $all_dayp = [];
            $dayp_namesss = [];
            $slots = Utilities::switch_db('api')->select("SELECT adslots_id from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' and time_created BETWEEN '$start' AND '$end'");
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
                    'id' => $j,
                    'daypart' => $nnn,
                    'percentage' => $day_percent,
                ];
                $j++;
            }

            return $datatables->collection($dayp_namesss)
                ->make(true);
        }else{

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
                    'id' => $j,
                    'daypart' => $nnn,
                    'percentage' => $day_percent,
                ];
                $j++;
            }

            return $datatables->collection($dayp_namesss)
                ->make(true);
        }

    }

    //High Performing Day
    public function hpdaysData(DataTables $datatables, Request $request)
    {
        $start = $request->start_date;
        $end = $request->stop_date;
        $broadcaster_user = Session::get('broadcaster_user_id');
        $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
        $broadcaster = $broadcaster_id[0]->broadcaster_id;
        if($request->has('start_date') && $request->has('stop_date'))
        {

            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, time_created as days from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND day_parts != '' AND time_created BETWEEN '$start' AND '$end' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc LIMIT 1");
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
                $j++;
            }

            return $datatables->collection($day_name)
                ->make(true);
        }else{

            $days = Utilities::switch_db('api')->select("SELECT COUNT(id) as tot_camp, time_created as days from campaigns where broadcaster = '$broadcaster' AND agency = '$broadcaster_user' AND day_parts != '' GROUP BY DATE_FORMAT(time_created, '%a'), broadcaster ORDER BY WEEK(time_created) desc ");
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
                $j++;
            }

            return $datatables->collection($day_name)
                ->make(true);
        }

    }

}