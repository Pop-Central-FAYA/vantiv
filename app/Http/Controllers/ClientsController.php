<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Vanguard\Country;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\StoreClient;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Vanguard\Repositories\Permission\PermissionRepository;
use Vanguard\Role;
use Vanguard\Support\Enum\UserStatus;

class ClientsController extends Controller
{
    public function index()
    {

    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $userInsert = DB::table('users')->insert([
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'country_id' => $request->country_id,
                'birthday' => $request->birthday,
                'status' => $request->status,
            ]);

            if ($request->hasFile('image_url')) {

                $image = $request->file('image_url');

                $client_image = time() . $image->getClientOriginalName();

                $image->move('clients_uploads', $client_image);
            }

            $user_id = \DB::select("SELECT id from users WHERE email = '$request->email'");

            $walkinInsert = Utilities::switch_db('reports')->table('walkIns')->insert([
                'id' => uniqid(),
                'user_id' => $user_id[0]->id,
                'broadcaster_id' => $request->broadcaster_id,
                'client_type_id' => $request->client_type_id,
                'location' => $request->location,
                'image_url' => 'clients_uploads/' . $client_image,
                'agency_id' => \Session::get('agency_id')
            ]);

            if ($userInsert && $walkinInsert) {
                return redirect()->route('clients.all')->with('success', 'Client Successfully created');
            } else {
                return redirect()->back()->with('error', trans('Client not created, try again'));
            }
        } else {
            $roles = Role::all();
            $countries = Country::all();
            $statuses = UserStatus::lists();

            return view('clients.create')
                ->with('roles', $roles)
                ->with('statuses', $statuses)
                ->with('countries', $countries);
        }

    }

    public function clients()
    {
        $ageny_id = \Session::get('agency_id');

        $agencies = Utilities::switch_db('reports')->select("SELECT id, user_id, image_url, time_created FROM walkIns WHERE agency_id = '$ageny_id'");

        $agency_data = [];

        foreach ($agencies as $agency) {

            $user_id = (int) $agency->user_id;

            $user_details = \DB::select("SELECT * FROM users WHERE id = '$user_id'");

            $campaigns = Utilities::switch_db('api')->select("SELECT COUNT(id) as number from campaigns where user_id = '$user_id'");

            $last_camp_date = Utilities::switch_db('api')->select("SELECT time_created from campaigns where user_id = '$user_id' ORDER BY time_created DESC LIMIT 1");
            if($last_camp_date){
                $date = $last_camp_date[0]->time_created;
            }else{
                $date = 0;
            }

            $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as total from payments WHERE campaign_id IN(SELECT id from campaigns WHERE user_id = '$user_id')");

            $agency_data[] = [
                'client_id' => $agency->id,
                'user_id' => $agency->user_id,
                'image_url' => $agency->image_url,
                'num_campaign' => $campaigns[0]->number,
                'total' => $payments[0]->total,
                'name' => $user_details[0]->last_name . ' ' . $user_details[0]->first_name,
                'created_at' => $agency->time_created,
                'last_camp' => $date,
            ];
        }

        return view('clients.clients-list')->with('clients', $agency_data);
    }

    public function clientShow($client_id)
    {
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = (int) $client[0]->user_id;

        $walknin_id = $client[0]->id;

        $user_camp = [];

//        $brands = Utilities::switch_db('reports')->select("SELECT * FROM brands WHERE walkin_id = '$walknin_id'");
        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$user_id'");
        foreach ($campaigns as $campaign){
            $pay = Utilities::switch_db('api')->select("SELECT amount from payments where campaign_id = '$campaign->id'");
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
}
