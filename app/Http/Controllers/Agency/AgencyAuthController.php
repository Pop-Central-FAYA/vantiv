<?php

namespace Vanguard\Http\Controllers\Agency;

use DB;
use Vanguard\Mail\SendConfirmationMail;
use Vanguard\Role;
use Vanguard\Country;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Vanguard\Http\Requests\StoreAgent;
use Vanguard\Http\Controllers\Controller;
use JD\Cloudder\Facades\Cloudder;
use Mail;
use Session;

class AgencyAuthController extends Controller
{
    public function getRegister()
    {
        $roles = Role::all();
        $countries = Country::all();
        $sectors = Utilities::switch_db('api')->select("SELECT * FROM sectors");

        return view('agency.signup')
            ->with('roles', $roles)
            ->with('sectors', $sectors)
            ->with('countries', $countries);
    }

    public function postRegister(StoreAgent $request)
    {
        $role_id = Utilities::switch_db('api')->select("SELECT id FROM roles WHERE name = 'agency'");

        if ($request->isMethod('POST')) {

            if ($request->hasFile('image_url')) {
                $image = $request->image_url;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                // $image_path = encrypt($clouder['url']);
                $image_path = encrypt($clouder['secure_url']);
            }

            $full_name = $request->first_name . ' ' . $request->last_name;
            $token = str_random(30);

            $userInsert = DB::table('users')->insert([
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'country_id' => $request->country_id,
                'address' => $request->address,
                'fullname' => $request->first_name . ' ' . $request->last_name,
                'status' => 'Unconfirmed',
                'confirmation_token' => $token
            ]);

            if ($userInsert) {
                $user_id = DB::select("SELECT id from users WHERE email = '$request->email'");
            }

            $role_user = DB::table('role_user')->insert([
                'user_id' => $user_id[0]->id,
                'role_id' => 4
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
                'user_type' => 4,
                'status' => 1
            ]);

            if ($userApiInsert) {
                $apiUser = Utilities::switch_db('api')->select("SELECT id FROM users WHERE email = '$request->email'");
            }

            $agentApiInsert = Utilities::switch_db('api')->table('agents')->insert([
                'id' => uniqid(),
                'user_id' => $apiUser[0]->id,
                'sector_id' => $request->sector_id,
                'nationality' => $request->country_id,
                'location' => $request->location,
                'image_url' => $image_path,
                'brand' => $request->username
            ]);

            if ($agentApiInsert) {
                $send_mail = Mail::to($request->email)->send(new SendConfirmationMail($token, $full_name, $request->email));
                Session::flash('success', 'Sign Up Successful, Please click on the Activate your account button to verify your email address');
                return redirect()->route('login');
            } else {
                Session::flash('error', trans('Sign Up not successful, try again'));
                return redirect()->back();
            }
        }
    }
}