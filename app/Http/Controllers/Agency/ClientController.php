<?php

namespace Vanguard\Http\Controllers\Agency;

use Vanguard\Country;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\StoreClient;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Vanguard\Repositories\Permission\PermissionRepository;
use Vanguard\Role;
use Vanguard\Support\Enum\UserStatus;

class ClientController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        $roles = Role::all();
        $countries = Country::all();
        $statuses = UserStatus::lists();

        return view('clients.create')
            ->with('roles', $roles)
            ->with('statuses', $statuses)
            ->with('countries', $countries);
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

        if ($request->hasFile('image_url'))
        {
            $image = $request->file('image_url');

            $client_image = time().$image->getClientOriginalName();

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
