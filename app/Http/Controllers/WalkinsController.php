<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Http\Requests\StoreWalkins;
use Vanguard\Http\Requests\WalkinStoreRequest;
use Vanguard\Http\Requests\WalkinUpdateRequest;
use Vanguard\Models\Brand;
use Yajra\DataTables\DataTables;
use Vanguard\Libraries\Utilities;
use Session;
use Vanguard\Libraries\Api;
use Illuminate\Support\Facades\DB;

class WalkinsController extends Controller
{
    //NB: agency_id is assumed to be the broadcaster user
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $broadcaster_user = Session::get('broadcaster_user_id');
        
        if($broadcaster_id){
            $clients = Utilities::switch_db('api')->select("SELECT w.user_id, w.id, u.id as user_det_id, u.firstname, u.lastname, u.phone_number, 
                                                                w.location, w.company_logo, w.company_name, w.time_created, u.email, w.image_url from walkIns as w 
                                                                INNER JOIN users as u ON u.id = w.user_id where w.broadcaster_id = '$broadcaster_id'");
        }else{
            $clients = Utilities::switch_db('api')->select("SELECT w.user_id, w.id, u.id as user_det_id, u.firstname, u.lastname, u.phone_number, w.location, 
                                                                w.company_logo, w.company_name, w.time_created, u.email, w.image_url from walkIns as w 
                                                                INNER JOIN users as u ON u.id = w.user_id 
                                                                where w.agency_id = '$broadcaster_user'");
        }

        $client_data = $this->getClientDetails($clients, $broadcaster_id);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($client_data);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');

        $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors");

        return view('broadcaster_module.walk-In.index')->with('clients', $entries)->with('industries', $industries);
    }

    public function getClientDetails($clients, $broadcaster_id)
    {
        $client_data = [];

        foreach ($clients as $client) {

            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where user_id = '$client->user_id' and broadcaster = '$broadcaster_id'");
            $last_count_campaign = count($campaigns) - 1;

            $active_campaigns = [];

            $inactive_campaigns = [];

            $brs = Utilities::getBrandsForWalkins($client->id);

            $today = date("Y-m-d");

            foreach ($campaigns as $campaign){
                if($campaign->stop_date > $today){
                    $active_campaigns[] = $campaign;
                }else{
                    $inactive_campaigns[] = $campaign;
                }
            }

            $payments = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments WHERE campaign_id IN 
                                                              (SELECT campaign_id from campaignDetails WHERE user_id = '$client->user_id' and broadcaster = '$broadcaster_id')");

            $client_data[] = [
                'client_id' => $client->id,
                'user_id' => $client->user_id,
                'agency_client_id' => $client->user_det_id,
                'image_url' => $client->image_url,
                'num_campaign' => $campaigns ? count($campaigns) : 0,
                'total' => $payments[0]->total,
                'name' =>  $client->lastname . ' ' . $client->firstname,
                'email' => $client->email,
                'phone_number' => $client->phone_number,
                'first_name' => $client->firstname,
                'last_name' => $client->lastname,
                'created_at' => $client->time_created,
                'last_camp' => $campaigns ? $campaigns[$last_count_campaign]->time_created : 0,
                'active_campaign' => count($active_campaigns),
                'inactive_campaign' => count($inactive_campaigns),
                'count_brands' => count($brs),
                'company_name' => $client->company_name,
                'company_logo' => $client->company_logo,
                'location' => $client->location,
            ];
        }

        return $client_data;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getSubIndustry()
    {
        $industry = request()->industry;

        $sud_industry = Utilities::switch_db('api')->select("SELECT * from subSectors where sector_id = '$industry'");

        if(count($sud_industry) > 0){
            return $sud_industry;
        }else{
            return response()->json(['error' => 'error']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WalkinStoreRequest $request)
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Client '.$request->first_name.' '. $request->last_name.' with brand '.$request->brand_name.' Created by '.Session::get('agency_id');
        $ip = request()->ip();
        $client_id = uniqid();
        $api_db = Utilities::switch_db('api');
        $local_db = Utilities::switch_db('local');
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        if($broadcaster_id){
            $broadcaster_agency_id = $broadcaster_id;
            $role_id = $api_db->select("SELECT role_id from users WHERE id = ( SELECT user_id from broadcasters WHERE id = '$broadcaster_id')");
        }else{
            $broadcaster_agency_id = $agency_id;
            $role_id = Utilities::switch_db('reports')->select("SELECT id as role_id FROM roles WHERE name = 'agency_client'");
        }

        $brand_slug = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $check_brand = $api_db->select("SELECT b.* from brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                WHERE b.slug = '$brand_slug' AND client_id = '$broadcaster_agency_id'");

        if(count($check_brand) > 0) {
            Session::flash('error', 'Brands already exists');
            return redirect()->back();
        }

        $api_db->beginTransaction();
        $local_db->beginTransaction();

        try {
            Utilities::insertIntoUsersLocalDb($request);
        }catch (\Exception $e){
            $local_db->rollback();
            Session::flash('error', 'There was a problem creating this walk-In');
            return redirect()->back();
        }

        $user_id = \DB::select("SELECT id from users WHERE email = '$request->email'");

        try {
            Utilities::insertRolesInLocalDb($user_id[0]->id);
        }catch(\Exception $e) {
            $local_db->rollback();
            Session::flash('error', 'There was a problem creating this walk-In');
            return redirect()->back();
        }

        try {
            Utilities::insertIntoUsersApiDB($request, $role_id[0]->role_id);
        }catch(\Exception $e) {
            $api_db->rollback();
            Session::flash('error', 'There was a problem creating this walk-In');
            return redirect()->back();
        }

        $apiUserDetails = Utilities::switch_db('api')->select("SELECT * FROM users where email = '$request->email'");

        dd('hello');
        try {
            if($request->hasFile('company_logo')){
                $company_image = Utilities::uploadCompanyLogoToOurServer($request);
            }
            Utilities::insertIntoWalkinsApiDB($client_id, $apiUserDetails[0]->id, $broadcaster_id, $request, $company_image, $agency_id);
        }catch (\Exception $e){
            $api_db->rollback();
            Session::flash('error', 'There was a problem creating this walk-In');
            return redirect()->back();
        }

        //check if the brand exists in the brands table and if not create the brand in the brands table and attach the client in the brand_client table.
        $checkIfBrandExists = Brand::where('slug', $brand_slug)->first();
        if(!$checkIfBrandExists){
            $brand_logo = $request->file('image_url');
            $image_url = Utilities::uploadBrandImageToCloudinary($brand_logo);
            $brand = new Brand();
            try {
                Utilities::storeBrands($brand, $request, $unique, $image_url, $brand_slug);
            }catch (\Exception $e) {
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this walk-In');
                return redirect()->back();
            }

            try{
                Utilities::storeBrandClient($unique, $broadcaster_agency_id, $client_id);
            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this walk-In');
                return redirect()->back();
            }

        }else{
            try {
                Utilities::storeBrandClient($checkIfBrandExists->id, $broadcaster_agency_id, $client_id);
            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this walk-In');
                return redirect()->back();
            }
        }

        $save_activity = Api::saveActivity($broadcaster_agency_id, $description, $ip, $user_agent);

        $api_db->commit();
        $local_db->commit();
        Session::flash('success', 'Client created successfully');
        if($broadcaster_id){
            return redirect()->route('walkins.all');
        }else{
            return redirect()->route('clients.list');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWalKins(WalkinUpdateRequest $request, $client_id)
    {

        $result = Utilities::updateClients($request, $client_id);

        if($result === "success"){
            Session::flash('success', 'Client profile updated successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'An error occurred while submitting your request');
            return redirect()->back();
        }
    }

    public function getDetails($client_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;

        $all_campaigns = Utilities::getClientCampaignData($user_id, $broadcaster_id);

        $all_brands = Utilities::getClientsBrands($client_id, $broadcaster_id);

        if(count($all_brands) === 0){
            Session::flash('info', 'You don`t have a brand on this client');
            return redirect()->back();
        }

        $total = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' and broadcaster = '$broadcaster_id') ");

        $campaigns = Utilities::switch_db('api')->select("SELECT c.campaign_id, c.adslots, c.time_created, c.product, p.total, c.time_created from campaignDetails as c 
                                                              INNER JOIN payments as p ON p.campaign_id = c.campaign_id where c.user_id = '$user_id' and c.broadcaster = '$broadcaster_id'");

        $user_camp = Utilities::clientscampaigns($campaigns);

        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");

//        campaign vs time graph
        $campaign_graph = Utilities::clientGraph($campaigns);
        $campaign_payment = $campaign_graph['campaign_payment'];
        $campaign_date = $campaign_graph['campaign_date'];

        $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors");

        $sub_inds = Utilities::switch_db('api')->select("SELECT sub.id, sub.sector_id, sub.name, sub.sub_sector_code from subSectors as sub, sectors as s where sub.sector_id = s.sector_code");

        return view('broadcaster_module.walk-In.details')->with('clients')
            ->with('client_id', $client_id)
            ->with('client', $client)
            ->with('user_details', $user_details)
            ->with('campaign', $user_camp)
            ->with('all_campaigns', $all_campaigns)
            ->with('all_brands', $all_brands)
            ->with('total', $total)
            ->with('campaign_payment', $campaign_payment)
            ->with('campaign_date', $campaign_date)
            ->with('total_spent', $total)
            ->with('industries', $industries)
            ->with('sub_industries', $sub_inds);
    }


}
