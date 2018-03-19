<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Country;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Illuminate\Support\Facades\Session;

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

            $broadcasterApiInsert = Utilities::switch_db('api')->table('agents')->insert([
                'id' => uniqid(),
                'user_id' => $apiUser[0]->id,
                'sector_id' => $request->sector_id,
                'nationality' => $request->country_id,
                'location' => $request->location,
                'image_url' => $image_path,
                'brand' => null,
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
}