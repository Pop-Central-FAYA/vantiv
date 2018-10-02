<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Http\Requests\StoreWalkins;
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
    public function store(Request $request)
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Client '.$request->first_name.' '. $request->last_name.' with brand '.$request->brand_name.' Created by '.Session::get('agency_id');
        $ip = request()->ip();
        $client_id = uniqid();
        $broadcaster_id = Session::get('broadcaster_id');
        $role_id = Utilities::switch_db('api')->select("SELECT role_id from users WHERE id = ( SELECT user_id from broadcasters WHERE id = '$broadcaster_id')");

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'brand_name' => 'required',
            'image_url' => 'required|image',
            'company_name' => 'required',
            'company_logo' => 'required|image',
        ]);

        $brand_slug = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $check_brand = Utilities::switch_db('api')->select("SELECT b.* from brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                WHERE b.slug = '$brand_slug' AND client_id = '$broadcaster_id'");

        if(count($check_brand) > 0) {
            Session::flash('error', 'Brands already exists');
            return redirect()->back();
        }

        $userInsert = DB::table('users')->insert([
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt('password'),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'status' => 'Inactive',
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

        $userApiInsert = Utilities::switch_db('api')->table('users')->insert([
            'id' => uniqid(),
            'role_id' => $role_id[0]->role_id,
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

        if($request->hasFile('company_logo')){
            /*handling uploading the image*/
            $featured = $request->company_logo;
            $featured_new_name = time().$featured->getClientOriginalName();
            /*moving the image to public/uploads/post*/
            $featured->move('company_logo', $featured_new_name);

            $company_image = encrypt('company_logo/'.$featured_new_name);
        }

        $walkinInsert = Utilities::switch_db('api')->table('walkIns')->insert([
            'id' => $client_id,
            'user_id' => $apiUserDetails[0]->id,
            'broadcaster_id' => $broadcaster_id,
            'client_type_id' => $request->client_type_id,
            'location' => $request->address,
            'agency_id' => '',
            'nationality' => 566,
            'company_name' => $request->company_name,
            'company_logo' => $company_image
        ]);

        //check if the brand exists in the brands table and if not create the brand in the brands table and attach the client in the brand_client table.
        $checkIfBrandExists = Utilities::switch_db('api')->select("SELECT id, `name` from brands where slug = '$brand_slug'");

        if(count($checkIfBrandExists) === 0){
            $brand_logo = $request->file('image_url');
            $image_url = Utilities::uploadBrandImageToCloudinary($brand_logo);
            $brand = new Brand();
            $brand->id = $unique;
            $brand->name = $request->brand_name;
            $brand->image_url = $image_url;
            $brand->industry_code = $request->industry;
            $brand->sub_industry_code = $request->sub_industry;
            $brand->slug = $brand_slug;
            $brand->save();

            $insertIntoBrandClient = Utilities::switch_db('api')->table('brand_client')->insert([
               'brand_id' => $unique,
               'client_id' => $broadcaster_id,
               'brands_client' => $client_id,
            ]);
        }else{
            $insertIntoBrandClient = Utilities::switch_db('api')->table('brand_client')->insert([
                'brand_id' => $checkIfBrandExists[0]->id,
                'client_id' => $broadcaster_id,
                'brands_client' => $client_id,
            ]);
        }

        if ($userInsert && $walkinInsert && $insertIntoBrandClient) {
            $save_activity = Api::saveActivity($broadcaster_id, $description, $ip, $user_agent);
            Session::flash('success', 'Client created successfully');
            return redirect()->route('walkins.all');

        } else {
            Session::flash('error', 'Error occured while creating this client');
            return redirect()->back();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWalKins(Request $request, $client_id)
    {
        $this->validate($request, [
            'company_name' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required'
        ]);

        $result = Utilities::updateClients($request, $client_id);

        if($result === "success"){
            Session::flash('success', 'Client profile updated successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'An error occured while submitting your request');
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
