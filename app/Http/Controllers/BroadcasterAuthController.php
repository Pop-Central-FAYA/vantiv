<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Country;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\StoreBroadcasterUser;
use Vanguard\Libraries\Utilities;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class BroadcasterAuthController extends Controller
{
    public function getRegister()
    {
        $countries = Country::all();
        $sectors = Utilities::switch_db('api')->select("SELECT * FROM sectors");

        return view('broadcaster_onboard.onboard')
            ->with('countries', $countries)
            ->with('sectors', $sectors);
    }

    public function postRegister(Request $request)
    {
        $role_id = Utilities::switch_db('api')->select("SELECT id FROM roles WHERE name = 'broadcaster'");

        if ($request->isMethod('POST')) {

            if ($request->hasFile('image_url')) {
                $image = $request->image_url;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);
            }

            $userInsert = DB::table('users')->insert([
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'avatar' => $image_path,
                'country_id' => $request->country_id,
                'address' => $request->address,
                'fullname' => $request->first_name . ' ' . $request->last_name,
            ]);

            if ($userInsert) {
                $user_id = DB::select("SELECT id from users WHERE email = '$request->email'");
            }

            $role_user = DB::table('role_user')->insert([
                'user_id' => $user_id[0]->id,
                'role_id' => 3
            ]);

            $userApiInsert = Utilities::switch_db('api')->table('users')->insert([
                'id' => uniqid(),
                'role_id' => $role_id[0]->id,
                'email' => $request->email,
                'token' => '',
                'password' => bcrypt($request->password),
                'firstname' => $request->first_name,
                'lastname' => $request->last_name,
                'phone_number' => $request->phone,
                'user_type' => 3,
                'status' => 1
            ]);

            if ($userApiInsert) {
                $apiUser = Utilities::switch_db('api')->select("SELECT id FROM users WHERE email = '$request->email'");
            }

            $broadcasterApiInsert = Utilities::switch_db('api')->table('broadcasters')->insert([
                'id' => uniqid(),
                'user_id' => $apiUser[0]->id,
                'sector_id' => $request->sector_id,
                'nationality' => $request->country_id,
                'location' => $request->location,
                'image_url' => $image_path,
                'brand' => $request->username,
                'status' => 1,
            ]);

            if ($broadcasterApiInsert) {
                Session::flash('success', 'Sign Up Successful, You can login now');
                return redirect()->route('login');
            } else {
                Session::flash('error', trans('Sign Up not successful, try again'));
                return redirect()->back();
            }
        }
    }

    public function allUser()
    {
        $broadcaster = Session::get('broadcaster_id');
        $all_users = Utilities::switch_db('api')->select("SELECT * from users where id IN(SELECT user_id from broadcasterUsers where broadcaster_id = '$broadcaster')");
        return view('broadcaster.index', compact('all_users'));
    }

    public function createUser()
    {
        $countries = Country::all();
        return view('broadcaster.create')->with('countries', $countries);
    }

    public function userData(DataTables $dataTables)
    {
        $users = [];
        $j = 1;
        $broadcaster = Session::get('broadcaster_id');
        $all_users = Utilities::switch_db('api')->select("SELECT * from users where id IN(SELECT user_id from broadcasterUsers where broadcaster_id = '$broadcaster')");
        foreach ($all_users as $all_user){
            $users[] = [
                's_n' => $j,
                'id' => $all_user->id,
                'name' => $all_user->firstname.' '.$all_user->lastname,
                'email' => $all_user->email,
                'phone' => $all_user->phone_number,
            ];
            $j++;
        }

        return $dataTables->collection($users)
            ->addColumn('delete', function ($users) {
                return '<button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal' . $users['id'] . '" style="font-size: 16px">
                            Delete
                        </button>';
            })
            ->rawColumns(['delete' => 'delete'])->addIndexColumn()
            ->make(true);
    }

    public function postBroadcasterUser(StoreBroadcasterUser $request)
    {
        $role_id = Utilities::switch_db('api')->select("SELECT id FROM roles WHERE name = 'broadcaster_user'");

        if ($request->hasFile('image_url')) {
            $image = $request->image_url;
            $filename = realpath($image);
            Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
            $clouder = Cloudder::getResult();
            $image_path = encrypt($clouder['url']);
        }

        $userInsert = DB::table('users')->insert([
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt('password'),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone_number,
            'avatar' => $image_path,
            'country_id' => $request->country,
            'address' => $request->address,
            'fullname' => $request->first_name . ' ' . $request->last_name,
        ]);

        if ($userInsert) {
            $user_id = DB::select("SELECT id from users WHERE email = '$request->email'");
        }

        $role_user = DB::table('role_user')->insert([
            'user_id' => $user_id[0]->id,
            'role_id' => 7
        ]);

        $userApiInsert = Utilities::switch_db('api')->table('users')->insert([
            'id' => uniqid(),
            'role_id' => $role_id[0]->id,
            'email' => $request->email,
            'token' => '',
            'password' => bcrypt('password'),
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'phone_number' => $request->phone_number,
            'user_type' => 6,
            'status' => 1
        ]);

        if ($userApiInsert) {
            $apiUser = Utilities::switch_db('api')->select("SELECT id FROM users WHERE email = '$request->email'");
        }

        $broadcasterUserApiInsert = Utilities::switch_db('api')->table('broadcasterUsers')->insert([
            'id' => uniqid(),
            'user_id' => $apiUser[0]->id,
            'broadcaster_id' => Session::get('broadcaster_id'),
            'nationality' => $request->country,
            'location' => $request->location,
            'image_url' => $image_path,
            'brand' => Session::get('broadcaster_id'),
            'status' => 1,
        ]);

        if ($broadcasterUserApiInsert) {
            Session::flash('success', 'User Created Successfully');
            return redirect()->route('broadcaster.user.all');
        } else {
            Session::flash('error', trans('There was an error while creating this user'));
            return redirect()->back();
        }
    }

}