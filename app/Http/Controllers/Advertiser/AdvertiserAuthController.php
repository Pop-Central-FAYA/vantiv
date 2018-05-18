<?php

namespace Vanguard\Http\Controllers\Advertiser;

use DB;
use Vanguard\Http\Requests\StoreAdvertiser;
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

class AdvertiserAuthController extends Controller
{
    public function getRegister()
    {
        $roles = Role::all();
        $countries = Country::all();
        $sectors = Utilities::switch_db('reports')->select("SELECT * FROM sectors");

        return view('advertisers.signup')
            ->with('roles', $roles)
            ->with('sectors', $sectors)
            ->with('countries', $countries);
    }

    public function postRegister(StoreAdvertiser $request)
    {
        $role_id = Utilities::switch_db('reports')->select("SELECT id FROM roles WHERE name = 'advertiser'");

        if ($request->isMethod('POST')) {

            if ($request->hasFile('image_url')) {
                $image = $request->image_url;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);
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
            }else{
                $delete_user = DB::delete("DELETE * from users where email = '$request->email' LAST ");
            }

            $role_user = DB::table('role_user')->insert([
                'user_id' => $user_id[0]->id,
                'role_id' => 6
            ]);

            $this_user = $user_id[0]->id;

            if(!$role_user){
                $delete_role = DB::delete("DELETE * FROM role_users where user_id = '$this_user'");
                $delete_user = DB::delete("DELETE * from users where email = '$request->email' LAST ");
            }

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

            if ($userApiInsert) {
                $apiUser = Utilities::switch_db('reports')->select("SELECT id FROM users WHERE email = '$request->email'");
            }else{
                $delete_api_user = Utilities::switch_db('api')->delete("DELETE * from users where email = '$request->email'");
                $delete_role = DB::delete("DELETE * FROM role_users where user_id = '$this_user'");
                $delete_user = DB::delete("DELETE * from users where email = '$request->email' LAST ");
            }

            $agentAdvertiserInsert = Utilities::switch_db('reports')->table('advertisers')->insert([
                'id' => uniqid(),
                'user_id' => $apiUser[0]->id,
                'sector_id' => $request->sector_id,
                'nationality' => $request->country_id,
                'location' => $request->location,
                'image_url' => $image_path,
                'brand' => $request->username
            ]);

            if ($agentAdvertiserInsert) {
                $send_mail = Mail::to($request->email)->send(new SendConfirmationMail($token, $full_name, $request->email));
                Session::flash('success', 'Sign Up Successful, Please click on the Activate your account button to verify your email address');
                return redirect()->route('login');
            } else {
                $api_user_id = $apiUser[0]->id;
                $delete_advertiser = Utilities::switch_db('api')->delete("DELETE * from advertisers where user_id = '$api_user_id'");
                $delete_api_user = Utilities::switch_db('api')->delete("DELETE * from users where email = '$request->email'");
                $delete_role = DB::delete("DELETE * FROM role_users where user_id = '$this_user'");
                $delete_user = DB::delete("DELETE * from users where email = '$request->email' LAST ");
                return redirect()->back()->with('error', trans('Sign Up not successful, try again'));
            }
        }
    }

}

