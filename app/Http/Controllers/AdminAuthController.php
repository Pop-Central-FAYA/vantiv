<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Country;
use Vanguard\Libraries\Utilities;
use DB;
use Mail;
use Vanguard\Mail\SendConfirmationMail;
use Session;

class AdminAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdmin()
    {
        $countries = Country::all();
        return view('admin_auth.register', compact('countries'));
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

            $full_name = $request->first_name . ' ' . $request->last_name;
            $token = str_random(30);

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
                'status' => 'Active',
                'confirmation_token' => $token,
            ]);

            if ($userInsert) {
                $user_id = DB::select("SELECT id from users WHERE email = '$request->email'");
            }

            $role_user = DB::table('role_user')->insert([
                'user_id' => $user_id[0]->id,
                'role_id' => 1
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
                'user_type' => 1,
                'status' => 1
            ]);

            if ($userApiInsert) {
                $apiUser = Utilities::switch_db('api')->select("SELECT id FROM users WHERE email = '$request->email'");
            }

            $broadcasterApiInsert = Utilities::switch_db('api')->table('admins')->insert([
                'id' => uniqid(),
                'user_id' => $apiUser[0]->id,
                'nationality' => $request->country_id,
                'location' => $request->location,
                'image_url' => $image_path,
                'status' => 1,
            ]);

            if ($broadcasterApiInsert) {
//                $send_mail = Mail::to($request->email)->send(new SendConfirmationMail($token, $full_name, $request->email));
                Session::flash('success', 'Sign Up Successful, Please click on the Activate your account button to verify your email address');
                return redirect()->route('login');
            } else {
                Session::flash('error', trans('Sign Up not successful, try again'));
                return redirect()->back();
            }
        }
    }

}
