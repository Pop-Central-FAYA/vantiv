<?php

namespace Vanguard\Http\Controllers;

use Hamcrest\Util;
use Session;
use Vanguard\Role;
use Vanguard\Country;
use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Libraries\Utilities;
use Illuminate\Support\Facades\DB;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\Http\Requests\StoreClient;
use Vanguard\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Vanguard\Repositories\Permission\PermissionRepository;
use Yajra\DataTables\DataTables;

class ClientsController extends Controller
{
    public function index()
    {

    }

    public function create(Request $request)
    {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Client '.$request->first_name.' '. $request->last_name.' with brand '.$request->brand_name.' Created by '.Session::get('agency_id');
        $ip = request()->ip();
        $client_id = uniqid();
        $agency_id = Session::get('agency_id');
        $role_id = Utilities::switch_db('reports')->select("SELECT id FROM roles WHERE name = 'agency_client'");

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|phone_number',
            'brand_name' => 'required',
            'image_url' => 'required|image',
            'company_name' => 'required',
            'company_logo' => 'required|image',
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            Session::flash('error', 'Brands already exists');
            return redirect()->back();
        }

        if($request->hasFile('image_url')){
            $image = $request->image_url;
            $filename = $request->file('image_url')->getRealPath();
            Cloudder::upload($filename, Cloudder::getPublicId());
            $clouder = Cloudder::getResult();
            $image_url = encrypt($clouder['url']);
        }

        if($request->hasFile('company_logo')){
            /*handling uploading the image*/
            $featured = $request->company_logo;
            $featured_new_name = time().$featured->getClientOriginalName();
            /*moving the image to public/uploads/post*/
            $featured->move('company_logo', $featured_new_name);

            $company_image = encrypt('company_logo/'.$featured_new_name);
        }

        $userInsert = DB::table('users')->insert([
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt('password'),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'status' => 'Active',
        ]);

        if ($userInsert) {
            $user_id = \DB::select("SELECT id from users WHERE email = '$request->email'");
        }else{
            $deleteUser = DB::delete("DELETE FROM users where email = '$request->email'");
        }

        $role_user = DB::table('role_user')->insert([
            'user_id' => $user_id[0]->id,
            'role_id' => 5
        ]);

        $userApiInsert = Utilities::switch_db('reports')->table('users')->insert([
            'id' => uniqid(),
            'role_id' => $role_id[0]->id,
            'email' => $request->email,
            'token' => '',
            'password' => bcrypt('password'),
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'phone_number' => $request->phone,
            'user_type' => 4,
            'status' => 1
        ]);

        $apiUserDetails = Utilities::switch_db('api')->select("SELECT * FROM users where email = '$request->email'");

        $walkinInsert = Utilities::switch_db('reports')->table('walkIns')->insert([
            'id' => $client_id,
            'user_id' => $apiUserDetails[0]->id,
            'broadcaster_id' => $request->broadcaster_id,
            'client_type_id' => $request->client_type_id,
            'location' => $request->address,
            'agency_id' => $agency_id,
            'nationality' => 566,
            'company_name' => $request->company_name,
            'company_logo' => $company_image
        ]);

        $insertBrands = Utilities::switch_db('api')->insert("INSERT into brands (id, `name`, image_url, walkin_id, broadcaster_agency, industry_id, sub_industry_id) VALUES ('$unique', '$brand', '$image_url', '$client_id', '$agency_id', '$request->industry', '$request->sub_industry')");

        if ($userInsert && $walkinInsert && $insertBrands) {
            $save_activity = Api::saveActivity($agency_id, $description, $ip, $user_agent);
            Session::flash('success', 'Client created successfully');
            return redirect()->route('clients.list');

        } else {
            Session::flash('error', 'Error occured while creating this client');
            return redirect()->back();
        }


    }

    public function clients()
    {
        $ageny_id = \Session::get('agency_id');

        $agencies = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE agency_id = '$ageny_id' ORDER BY time_created DESC");

        $agency_data = [];

        foreach ($agencies as $agency) {

            $user_id = $agency->user_id;

            $user_details = Utilities::switch_db('api')->select("SELECT * FROM users WHERE id = '$user_id'");

            $campaigns = Utilities::switch_db('api')->select("SELECT count(id) as number from campaigns where id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id')");

            $last_camp_date = Utilities::switch_db('api')->select("SELECT time_created from campaignDetails where user_id = '$user_id' GROUP BY campaign_id ORDER BY time_created DESC LIMIT 1");
            if ($last_camp_date) {
                $date = $last_camp_date[0]->time_created;
            } else {
                $date = 0;
            }

            $brs = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$agency->id'");

            $today = date("Y-m-d");

            $active_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails where user_id = '$user_id' AND stop_date >= '$today' GROUP BY campaign_id ");

            $inactive_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails where user_id = '$user_id' AND stop_date < '$today' GROUP BY campaign_id ");

            $payments = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments WHERE campaign_id IN (SELECT campaign_id from campaignDetails WHERE user_id = '$user_id' GROUP BY campaign_id)");

            $agency_data[] = [
                'client_id' => $agency->id,
                'user_id' => $agency->user_id,
                'agency_client_id' => $user_details && $user_details[0]->id ? $user_details[0]->id : 1,
                'image_url' => $agency->image_url,
                'num_campaign' => $campaigns ? $campaigns[0]->number : 0,
                'total' => $payments[0]->total,
                'name' => $user_details && $user_details[0] ? $user_details[0]->lastname . ' ' . $user_details[0]->firstname : '',
                'created_at' => $agency->time_created,
                'last_camp' => $date,
                'active_campaign' => $active_campaign ? count($active_campaign) : 'None',
                'inactive_campaign' => $inactive_campaign ? count($inactive_campaign) : 'None',
                'count_brands' => count($brs),
                'company_name' => $agency->company_name,
                'company_logo' => $agency->company_logo
            ];
        }


        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($agency_data);
        $perPage = 6;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');

        $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors");

        return view('clients.clients-list')->with('clients', $entries)->with('industries', $industries);
    }

    public function clientShow($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;

        $walknin_id = $client[0]->id;

        $user_camp = [];

        $current_month = date('F');
        $months = [];
        $default_month = date('F', strtotime("2018-01-01"));
        for($i = 1; $i <= 12; $i++){
            $months[] = date('F', strtotime("2018-".$i."-01"));
        }

        $all_campaigns = $this->getCampaignData($user_id);

        $all_brands = $this->getClientBrands($client_id);

        $date = date('Y-m', time());

        $all_campaign_this_month = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 AND DATE_FORMAT(time_created, '%Y-%m') = '$date' GROUP BY campaign_id ORDER BY time_created DESC");

        $total_this_month = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id) AND DATE_FORMAT(time_created, '%Y-%m') = '$date'");

        $brand_this_month = Utilities::switch_db('api')->select("SELECT count(id) as brand from brands where walkin_id = '$user_id' AND DATE_FORMAT(time_created, '%Y-%m') = '$date'");

        $total = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id) ");

        $campaigns = Utilities::switch_db('api')->select("SELECT campaign_id, SUM(adslots) as adslots, time_created, product from campaignDetails where user_id = '$user_id' GROUP BY campaign_id");
        foreach ($campaigns as $campaign){
            $pay = Utilities::switch_db('api')->select("SELECT total from payments where campaign_id = '$campaign->campaign_id'");
            $user_camp[] = [
                'product' => $campaign->product,
                'num_of_slot' => $campaign->adslots,
                'payment' => $pay[0]->total,
                'date' => $campaign->time_created
            ];
        }

        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");

//        campaign vs time graph
        $last_weekly_campaign = [];
        $last_weekly_total = [];
        $last_weekly_date = [];
//        $all_camps = Utilities::switch_db('api')->select("SELECT * FROM campaigns WHERE (time_created >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY) AND (time_created < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY) AND id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id)");
        $all_camps = Utilities::switch_db('api')->select("SELECT * FROM campaigns where DATE_FORMAT(time_created, '%Y-%m') = '$date' AND id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id)");
        foreach ($all_camps as $all_camp){
            $pay = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$all_camp->id' AND time_created = '$all_camp->time_created'");
            $last_weekly_campaign[] = [
                'id' => $all_camp->id,
                'date' => date('Y-m-d', strtotime($all_camp->time_created)),
                'total' => $pay[0]->total
            ];
        }

//        get the price
        foreach ($last_weekly_campaign as $last_week){
            $last_weekly_total[] = $last_week['total'];
        }
//        get the date
        foreach ($last_weekly_campaign as $last_week){
            $last_weekly_date[] = $last_week['date'];
        }

        $week_payment = json_encode($last_weekly_total);
        $week_date = json_encode($last_weekly_date);

        return view('clients.client-portfolio')->with('clients')
            ->with('client_id', $client_id)
            ->with('client', $client)
            ->with('user_details', $user_details)
            ->with('campaign', $user_camp)
            ->with('all_campaigns', $all_campaigns)
            ->with('all_brands', $all_brands)
            ->with('total', $total)
            ->with('week_payment', $week_payment)
            ->with('week_date', $week_date)
            ->with('current_month', $current_month)
            ->with('months', $months)
            ->with('total_this_month', $total_this_month)
            ->with('brand_this_month', $brand_this_month)
            ->with('all_campaign_this_month', $all_campaign_this_month);

    }

    public function getCampaignData($user_id)
    {
        $campaigns = [];
        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 GROUP BY campaign_id ORDER BY time_created DESC");
        foreach ($all_campaign as $cam)
        {
            $campaign_reference = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$cam->campaign_id'");
            $today = date("Y-m-d");
            if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                $status = 'expired';
            }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                $status = 'active';
            }else{
                $now = strtotime($today);
                $your_date = strtotime($cam->start_date);
                $datediff = $your_date - $now;
                $new_day =  round($datediff / (60 * 60 * 24));
                $status = 'pending';
            }
            $brand = Utilities::switch_db('api')->select("SELECT `name` as brand_name from brands where id = '$cam->brand'");
            $pay = Utilities::switch_db('api')->select("SELECT total from payments where campaign_id = '$cam->campaign_id'");
            $campaigns[] = [
                'id' => $campaign_reference[0]->campaign_reference,
                'camp_id' => $cam->campaign_id,
                'name' => $cam->name,
                'brand' => $brand[0]->brand_name,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'budget' => number_format($pay[0]->total, 2),
                'compliance' => '0%',
                'status' => $status
            ];
        }

        return $campaigns;
    }

    public function getClientBrands($id)
    {
        $brs = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$id'");
        $brands = [];
        foreach ($brs as $br){
            $campaigns = Utilities::switch_db('api')->select("SELECT count(id) as total_campaign from campaigns WHERE id IN (SELECT campaign_id from campaignDetails WHERE brand = '$br->id' GROUP BY campaign_id)");
            $last_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails where brand = '$br->id' GROUP BY campaign_id ORDER BY time_created DESC LIMIT 1");
            $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where brand = '$br->id' GROUP BY campaign_id)");
            $brands[] = [
                'id' => $br->id,
                'brand' => $br->name,
                'date' => $br->time_created,
                'count_brand' => count($brs),
                'campaigns' => $campaigns ? $campaigns[0]->total_campaign : 0,
                'image_url' => $br->image_url,
                'last_campaign' => $last_campaign ? $last_campaign[0]->name : 'none',
                'total' => number_format($pay[0]->total,2),
            ];
        }
        if(count($brands) === 0){
            Session::flash('info', 'You don`t have a brand on this client');
            return redirect()->back();
        }
        return $brands;
    }


    public function filterByMonth($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;
        $month = request()->month;
        $year = date('Y');
        $date = (string)($year.'-'.$month);

        $last_weekly_campaign = [];
        $last_weekly_total = [];
        $last_weekly_date = [];

        $all_campaign_this_month = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 AND DATE_FORMAT(time_created, '%Y-%M') = '$date' GROUP BY campaign_id ORDER BY time_created DESC");

        $total_this_month = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id) AND DATE_FORMAT(time_created, '%Y-%M') = '$date'");

        $brand_this_month = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$client_id' AND DATE_FORMAT(time_created, '%Y-%M') = '$date' ");

        $all_camps = Utilities::switch_db('api')->select("SELECT * FROM campaigns where DATE_FORMAT(time_created, '%Y-%M') = '$date' AND id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id)");

        foreach ($all_camps as $all_camp){
            $pay = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$all_camp->id' AND time_created = '$all_camp->time_created'");
            $last_weekly_campaign[] = [
                'id' => $all_camp->id,
                'date' => date('Y-m-d', strtotime($all_camp->time_created)),
                'total' => $pay[0]->total
            ];
        }

//        get the price
        foreach ($last_weekly_campaign as $last_week){
            $last_weekly_total[] = $last_week['total'];
        }

//        get the date
        foreach ($last_weekly_campaign as $last_week){
            $last_weekly_date[] = $last_week['date'];
        }

        return response()->json(['all_campaign' => $all_campaign_this_month, 'all_total' => number_format($total_this_month[0]->total,2), 'all_brand' => $brand_this_month, 'monthly_total' => $last_weekly_total, 'monthly_date' => $last_weekly_date]);

    }

    public function filterByYear($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;
        $date = date('Y');

        $last_weekly_campaign = [];
        $last_weekly_total = [];
        $last_weekly_date = [];

        $all_campaign_this_month = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 AND DATE_FORMAT(time_created, '%Y') = '$date' GROUP BY campaign_id ORDER BY time_created DESC");

        $total_this_month = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id) AND DATE_FORMAT(time_created, '%Y') = '$date'");

        $brand_this_month = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$client_id' AND DATE_FORMAT(time_created, '%Y') = '$date' ");

        $all_camps = Utilities::switch_db('api')->select("SELECT * FROM campaigns where DATE_FORMAT(time_created, '%Y') = '$date' AND id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id)");

        foreach ($all_camps as $all_camp){
            $pay = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$all_camp->id' AND time_created = '$all_camp->time_created'");
            $last_weekly_campaign[] = [
                'id' => $all_camp->id,
                'date' => date('Y-m-d', strtotime($all_camp->time_created)),
                'total' => $pay[0]->total
            ];
        }

//        get the price
        foreach ($last_weekly_campaign as $last_week){
            $last_weekly_total[] = $last_week['total'];
        }

//        get the date
        foreach ($last_weekly_campaign as $last_week){
            $last_weekly_date[] = $last_week['date'];
        }

        return response()->json(['all_campaign' => $all_campaign_this_month, 'all_total' => number_format($total_this_month[0]->total,2), 'all_brand' => $brand_this_month, 'monthly_total' => $last_weekly_total, 'monthly_date' => $last_weekly_date]);
    }

    public function brandCampaign($id, $client_id)
    {
        $campaigns = [];
        //get client details
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");
        $user_id = $client[0]->user_id;
        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");


        $this_brand = Utilities::switch_db('api')->select("SELECT * FROM brands where id = '$id'");
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where brand = '$id' GROUP BY campaign_id");
        foreach ($all_campaigns as $cam)
        {
            $mpo = Utilities::switch_db('api')->select("SELECT * FROM mpoDetails where mpo_id = (SELECT id from mpos where campaign_id = '$cam->campaign_id') LIMIT 1");
            $campaign_reference = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$cam->campaign_id'");
            $today = date("Y-m-d");
            if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                $status = 'Expired';
            }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                $status = 'Active';
            }else{
                $now = strtotime($today);
                $your_date = strtotime($cam->start_date);
                $datediff = $your_date - $now;
                $new_day =  round($datediff / (60 * 60 * 24));
                $status = 'pending';
            }

            $brand = Utilities::switch_db('api')->select("SELECT `name` as brand_name from brands where id = '$cam->brand'");
            $pay = Utilities::switch_db('api')->select("SELECT total from payments where campaign_id = '$cam->campaign_id'");
            $campaigns[] = [
                'id' => $campaign_reference[0]->campaign_reference,
                'camp_id' => $cam->id,
                'name' => $cam->name,
                'brand' => $brand[0]->brand_name,
                'product' => $cam->product,
                'date_created' => date('Y/m/d',strtotime($cam->time_created)),
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'budget' => number_format($pay[0]->total, 2),
                'compliance' => '0%',
                'status' => $status,
                'mpo_status' => $mpo[0]->is_mpo_accepted
            ];
        }

        return view('clients.client_brand', compact('this_brand', 'campaigns', 'user_details', 'client_id', 'client'));
    }

}
