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
                return redirect()->back()->with('success', 'Client Successfully created');
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

    public function store(StoreClient $request)
    {
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

        $walkinInsert = Utilities::switbch_db('reports')->table('walklns')->insert([
            'broadcaster_id' => $request->broadcaster_id,
            'client_type_id' => $request->client_type_id,
            'location' => $request->location,
            'image_url' => 'clients_uploads/' . $client_image
        ]);

        if ($userInsert && $walkinInsert) {
            return redirect()->back()->with('success', 'Client Successfully created');
        } else {
            return redirect()->back()->with('error', trans('Client not created, try again'));
        }

    }
}
