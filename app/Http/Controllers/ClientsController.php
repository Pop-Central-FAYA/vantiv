<?php

namespace Vanguard\Http\Controllers;

use Hamcrest\Util;
use Session;
use Vanguard\Http\Requests\WalkinStoreRequest;
use Vanguard\Models\Brand;
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

    public function clients()
    {

        $agency_id = \Session::get('agency_id');

        $clients = Utilities::switch_db('api')->select("SELECT w.user_id, w.id, u.id as user_det_id, u.firstname, u.lastname, u.phone_number, w.location,
                                                            w.company_logo, w.company_name, w.time_created, u.email, w.image_url from walkIns as w
                                                            INNER JOIN users as u ON u.id = w.user_id where agency_id = '$agency_id'");

        $client_data = [];

        foreach ($clients as $client) {

            $campaigns = Utilities::switch_db('api')->select("SELECT count(id) as total_campaigns from campaigns where id IN
                                                                (SELECT campaign_id from campaignDetails where user_id = '$client->user_id')");

            $last_camp_date = Utilities::switch_db('api')->select("SELECT time_created from campaignDetails where user_id = '$client->user_id' ORDER BY time_created DESC LIMIT 1");
            if ($last_camp_date) {
                $date = $last_camp_date[0]->time_created;
            } else {
                $date = 0;
            }

            $brs = Utilities::getBrandsForWalkins($client->id);

            $today = date("Y-m-d");

            $active_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails where user_id = '$client->user_id' AND stop_date >= '$today' GROUP BY campaign_id ");

            $inactive_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails where user_id = '$client->user_id' AND stop_date < '$today' GROUP BY campaign_id ");

            $payments = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments WHERE campaign_id IN
                                                                  (SELECT campaign_id from campaignDetails WHERE user_id = '$client->user_id' GROUP BY campaign_id)");

            $client_data[] = [
                'client_id' => $client->id,
                'user_id' => $client->user_id,
                'agency_client_id' => $client->user_det_id,
                'image_url' => $client->image_url,
                'num_campaign' => $campaigns ? $campaigns[0]->total_campaigns : 0,
                'total' => $payments[0]->total,
                'name' =>  $client->lastname . ' ' . $client->firstname,
                'email' => $client->email,
                'phone_number' => $client->phone_number,
                'first_name' => $client->firstname,
                'last_name' => $client->lastname,
                'created_at' => $client->time_created,
                'last_camp' => $date,
                'active_campaign' => $active_campaign ? count($active_campaign) : '0',
                'inactive_campaign' => $inactive_campaign ? count($inactive_campaign) : '0',
                'count_brands' => count($brs),
                'company_name' => $client->company_name,
                'company_logo' => $client->company_logo,
                'location' => $client->location,
            ];
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($client_data);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');

        $industries = Utilities::switch_db('api')->select("
            SELECT * FROM sectors
            ORDER BY `name` ASC
        ");

        return view('clients.clients-list')->with('clients', $entries)->with('industries', $industries);
    }

    public function clientShow($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;

        $all_campaigns = $this->getCampaignData($user_id);

        $all_brands = $this->getClientBrands($client_id);

        $all_campaign_this_month = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0  GROUP BY campaign_id ORDER BY time_created DESC");

        $brands = Utilities::getBrandsForWalkins($client_id);

        $total = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id) ");

        $campaigns = Utilities::switch_db('api')->select("SELECT c.campaign_id, c.adslots , c.time_created, c.product, p.total, c.time_created from campaignDetails as c, payments as p where c.user_id = '$user_id' and p.campaign_id = c.campaign_id GROUP BY c.campaign_id");

        $user_camp = Utilities::clientscampaigns($campaigns);


        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");

//        campaign vs time graph
        $campaign_graph = Utilities::clientGraph($campaigns);
        $campaign_payment = $campaign_graph['campaign_payment'];
        $campaign_date = $campaign_graph['campaign_date'];

        $industries = Utilities::switch_db('api')->select("
            SELECT * FROM sectors
            ORDER BY `name` ASC
        ");

        $sub_inds = Utilities::switch_db('api')->select("SELECT sub.id, sub.sector_id, sub.name, sub.sub_sector_code from subSectors as sub, sectors as s where sub.sector_id = s.sector_code");

        return view('clients.client-portfolio')->with('clients')
            ->with('client_id', $client_id)
            ->with('client', $client)
            ->with('user_details', $user_details)
            ->with('campaign', $user_camp)
            ->with('all_campaigns', $all_campaigns)
            ->with('all_brands', $all_brands)
            ->with('total', $total)
            ->with('campaign_payment', $campaign_payment)
            ->with('campaign_date', $campaign_date)
            ->with('total_this_month', $total)
            ->with('brands', $brands)
            ->with('all_campaign_this_month', $all_campaign_this_month)
            ->with('industries', $industries)
            ->with('sub_industries', $sub_inds);

    }

    public function getCampaignData($user_id)
    {
        $campaigns = [];
        $all_campaign = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.name, c_d.product, c_d.start_date, c_d.stop_date, c_d.adslots, c.campaign_reference, p.total, b.name as brands
                                                              from campaignDetails as c_d INNER JOIN campaigns as c ON c.id = c_d.campaign_id
                                                              INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                                              INNER JOIN brands as b ON b.id = c_d.brand WHERE c_d.user_id = '$user_id'
                                                              AND c_d.adslots > 0 GROUP BY c_d.campaign_id ORDER BY c_d.time_created DESC");
        foreach ($all_campaign as $cam)
        {
            $campaigns[] = [
                'id' => $cam->campaign_reference,
                'camp_id' => $cam->campaign_id,
                'name' => $cam->name,
                'brand' => $cam->brands,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'budget' => number_format($cam->total, 2),
                'compliance' => '0%',
                'status' => $cam->status
            ];
        }

        return $campaigns;
    }

    public function getClientBrands($id)
    {
        $client_brands = Utilities::getBrandsForWalkins($id);
        $brands = [];
        foreach ($client_brands as $client_brand){
            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE brand = '$client_brand->id'
                                                                  AND walkins_id = '$client_brand->client_walkins_id' GROUP BY campaign_id");
            $last_count_campaign = count($campaigns) - 1;
            $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where brand = '$client_brand->id'
                                                          AND walkins_id = '$client_brand->client_walkins_id' GROUP BY campaign_id)");
            $brands[] = [
                'id' => $client_brand->id,
                'brand' => $client_brand->name,
                'date' => $client_brand->created_at,
                'count_brand' => count($client_brands),
                'campaigns' => count($campaigns),
                'image_url' => $client_brand->image_url,
                'last_campaign' => $campaigns ? $campaigns[$last_count_campaign]->name : 'none',
                'total' => number_format($pay[0]->total,2),
                'industry_id' => $client_brand->industry_code,
                'sub_industry_id' => $client_brand->sub_industry_code,
            ];
        }
        if(count($brands) === 0){
            Session::flash('info', 'You don`t have a brand on this client');
            return redirect()->back();
        }
        return $brands;
    }


    public function filterByDate($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;
        $start_date = date('Y-m-d', strtotime(request()->start_date));
        $stop_date = date('Y-m-d', strtotime(request()->stop_date));

        $month_total = [];
        $month_date = [];

        $all_campaign_this_month = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 AND time_created BETWEEN
                                                                            '$start_date' AND '$stop_date' GROUP BY campaign_id ORDER BY time_created DESC");

        $total_this_month = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN
                                                                  (SELECT campaign_id from campaignDetails where user_id = '$user_id'
                                                                  GROUP BY campaign_id) AND time_created BETWEEN '$start_date' AND '$stop_date'");

        $brands = Utilities::switch_db('api')->select("SELECT b.* from brands as b INNER JOIN brand_client as b_c ON b_c.brand_id = b.id
                                                                  where b_c.brands_client = '$client_id' AND b.created_at BETWEEN '$start_date' AND '$stop_date' ");

        $all_camps = Utilities::switch_db('api')->select("SELECT * FROM campaigns where time_created BETWEEN '$start_date' AND '$stop_date' AND id IN
                                                            (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id)");

        foreach ($all_camps as $all_camp){
            $pay = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$all_camp->id' AND time_created = '$all_camp->time_created'");
            $month_total[] = $pay[0]->total;
            $month_date[] = date('Y-m-d', strtotime($all_camp->time_created));

        }


        return response()->json(['all_campaign' => $all_campaign_this_month, 'all_total' => number_format($total_this_month[0]->total,2), 'all_brand' => $brands, 'monthly_total' => $month_total, 'monthly_date' => $month_date]);

    }

    public function filterByYear($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;
        $date = date('Y');
        $year_total = [];
        $year_date = [];

        $all_campaign_this_year = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 AND DATE_FORMAT(time_created, '%Y') = '$date' GROUP BY campaign_id ORDER BY time_created DESC");

        $total_this_year = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id) AND DATE_FORMAT(time_created, '%Y') = '$date'");

        $brands = Utilities::switch_db('api')->select("SELECT b.* from brands as b INNER JOIN brand_client as b_c ON b_c.brand_id = b.id
                                                                  where b_c.brands_client = '$client_id' AND DATE_FORMAT(b.created_at, '%Y') = '$date' ");

        $all_camps = Utilities::switch_db('api')->select("SELECT * FROM campaigns where DATE_FORMAT(time_created, '%Y') = '$date' AND id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' GROUP BY campaign_id)");

        foreach ($all_camps as $all_camp){
            $pay = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$all_camp->id' AND time_created = '$all_camp->time_created'");
            $year_total[] = $pay[0]->total;
            $year_date[] = date('Y-m-d', strtotime($all_camp->time_created));
        }

        return response()->json(['all_campaign' => $all_campaign_this_year, 'all_total' => number_format($total_this_year[0]->total,2), 'all_brand' => $brands, 'monthly_total' => $year_total, 'monthly_date' => $year_date]);
    }

    public function brandCampaign($id, $client_id)
    {
        $campaigns = [];
        //get client details
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");
        $user_id = $client[0]->user_id;
        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");


        $this_brand = Utilities::singleBrand($id, $client_id);
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where brand = '$id' AND walkins_id = '$client_id' GROUP BY campaign_id");
        foreach ($all_campaigns as $campaign)
        {
            $mpo = Utilities::switch_db('api')->select("SELECT * FROM mpoDetails where mpo_id = (SELECT id from mpos where campaign_id = '$campaign->campaign_id') LIMIT 1");
            $campaign_reference = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$campaign->campaign_id'");
            $brand = Utilities::switch_db('api')->select("SELECT `name` as brand_name from brands where id = '$campaign->brand'");
            $pay = Utilities::switch_db('api')->select("SELECT total from payments where campaign_id = '$campaign->campaign_id'");
            $campaigns[] = [
                'id' => $campaign_reference[0]->campaign_reference,
                'camp_id' => $campaign->campaign_id,
                'name' => $campaign->name,
                'brand' => $brand[0]->brand_name,
                'product' => $campaign->product,
                'date_created' => date('Y/m/d',strtotime($campaign->time_created)),
                'start_date' => date('Y-m-d', strtotime($campaign->start_date)),
                'end_date' => date('Y-m-d', strtotime($campaign->stop_date)),
                'adslots' => $campaign->adslots,
                'budget' => number_format($pay[0]->total, 2),
                'compliance' => '0%',
                'status' => ucfirst($campaign->status),
                'mpo_status' => $mpo[0]->is_mpo_accepted
            ];
        }

        return view('clients.client_brand', compact('this_brand', 'campaigns', 'user_details', 'client_id', 'client'));
    }


}
