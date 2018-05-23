<?php

namespace Vanguard\Http\Controllers;

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

        if ($request->isMethod('POST')) {

            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|phone_number',
                'brand_name' => 'required',
                'image_url' => 'required|image',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password|min:6'
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
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ``);
                $clouder = Cloudder::getResult();
                $image_url = encrypt($clouder['url']);
            }

            $userInsert = DB::table('users')->insert([
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
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
                'password' => bcrypt($request->password),
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
                'location' => $request->location,
                'agency_id' => $agency_id,
                'nationality' => $request->country_id
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
        } else {
            $roles = Role::all();
            $countries = Country::all();
            $statuses = UserStatus::lists();
            $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors");
            $edit = false;
            $profile = false;

            return view('clients.create')
                ->with('roles', $roles)
                ->with('statuses', $statuses)
                ->with('countries', $countries)
                ->with('edit', $edit)
                ->with('profile', $profile)
                ->with('industries', $industries);
        }

    }

    public function clients()
    {
        $ageny_id = \Session::get('agency_id');

        $agencies = Utilities::switch_db('reports')->select("SELECT id, user_id, image_url, time_created FROM walkIns WHERE agency_id = '$ageny_id' ORDER BY time_created DESC");

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
            ];
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($agency_data);
        $perPage = 3;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');

        return view('clients.clients-list')->with('clients', $entries);
    }

    public function clientShow($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = (int) $client[0]->user_id;

        $walknin_id = $client[0]->id;

        $user_camp = [];

//        $brands = Utilities::switch_db('reports')->select("SELECT * FROM brands WHERE walkin_id = '$walknin_id'");
        $campaigns = Utilities::switch_db('api')->select("SELECT SUM(adslots) as adslots, time_created, product from campaignDetails where user_id = '$user_id' GROUP BY campaign_id");
        foreach ($campaigns as $campaign){
            $pay = Utilities::switch_db('api')->select("SELECT amount from payments where campaign_id = '$campaign->campaign_id'");
            $user_camp[] = [
                'product' => $campaign->product,
                'num_of_slot' => $campaign->adslots,
                'payment' => $pay[0]->amount,
                'date' => $campaign->time_created
            ];
        }

        $user_details = \DB::select("SELECT * FROM users WHERE id = '$user_id'");

        return view('clients.client-portfolio')->with('clients')
            ->with('client', $client)
            ->with('user_details', $user_details)
            ->with('campaign', $user_camp);

    }

    public function getClientBrands($id)
    {
        $brs = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$id'");
        $brands = [];
        foreach ($brs as $br){
            $campaigns = Utilities::switch_db('api')->select("SELECT count(id) as total_campaign from campaigns WHERE id IN (SELECT campaign_id from campaignDetails WHERE brand = '$br->id' GROUP BY campaign_id)");

            $brands[] = [
                'brand' => $br->name,
                'campaigns' => $campaigns[0]->total_campaign,
                'image_url' => $br->image_url
            ];
        }
        if(count($brands) === 0){
            Session::flash('info', 'You don`t have a brand on this client');
            return redirect()->back();
        }
        return view('clients.client_brand')->with('brands', $brands);
    }
}
