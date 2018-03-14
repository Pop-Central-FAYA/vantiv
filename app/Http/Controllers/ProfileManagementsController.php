<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Country;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Auth;
use DB;
use Image;
use JD\Cloudder\Facades\Cloudder;

class ProfileManagementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::all();
        $broadcaster_id = \Session::get('broadcaster_id');
        $agency_id = \Session::get('agency_id');
        $advertiser_id = \Session::get('advertiser_id');
        $user_details = [];
        $u_id = Auth::user()->id;
        if($agency_id){
            $api_user = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from agents where id = '$agency_id')");
            $local_user = \DB::select("SELECT * from users where id = '$u_id'");
            $api_agent = Utilities::switch_db('api')->select("SELECT * from agents where id = '$agency_id'");
            $user_details = [
                'first_name' => $api_user[0]->firstname,
                'last_name' => $api_user[0]->lastname,
                'phone' => $api_user[0]->phone_number,
                'email' => $api_user[0]->email,
                'address' => $local_user[0]->address,
                'location' => $api_agent[0]->location,
                'nationality' => $api_agent[0]->nationality,
                'username' => $local_user[0]->username,
            ];

        }elseif($advertiser_id){
            $api_user = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from advertisers where id = '$advertiser_id')");
            $local_user = \DB::select("SELECT * from users where id = '$u_id'");
            $api_agent = Utilities::switch_db('api')->select("SELECT * from advertisers where id = '$advertiser_id'");
            $user_details = [
                'first_name' => $api_user[0]->firstname,
                'last_name' => $api_user[0]->lastname,
                'phone' => $api_user[0]->phone_number,
                'email' => $api_user[0]->email,
                'address' => $local_user[0]->address,
                'location' => $api_agent[0]->location,
                'nationality' => $api_agent[0]->nationality,
                'username' => $local_user[0]->username,
            ];
        }else{
            $api_user = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from broadcasters where id = '$broadcaster_id')");
            $local_user = \DB::select("SELECT * from users where id = '$u_id'");
            $api_agent = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
            $user_details = [
                'first_name' => $api_user[0]->firstname,
                'last_name' => $api_user[0]->lastname,
                'phone' => $api_user[0]->phone_number,
                'email' => $api_user[0]->email,
                'address' => $local_user[0]->address,
                'location' => $api_agent[0]->location,
                'nationality' => $api_agent[0]->nationality,
                'username' => $local_user[0]->username,
            ];
        }
        return view('profile.index')->with('countries', $countries)->with('user_details', $user_details)
                                         ->with('agency_id', $agency_id)->with('advertiser_id', $advertiser_id)
                                         ->with('broadcaster_id', $broadcaster_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDetails(Request $request)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $agency_id = \Session::get('agency_id');
        $advertiser_id = \Session::get('advertiser_id');
        $u_id = Auth::user()->id;
        $update_user = [];

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $ip = request()->ip();

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'country_id' => 'required',
            'username' => 'required',
            'location' => 'required',
        ]);

        if($agency_id){
            $description = 'Profile updated with the following information first name='.$request->first_name.', last name=' .$request->last_name. ', address='.$request->address.', username='.$request->username. ', phone number='.$request->phone.', location='.$request->location.', country code='.$request->country_id.', password='.$request->password.' by '.$agency_id;
            if($request->hasFile('image_url')){
                $this->validate($request, [
                   'image_url' => 'required|image|mimes:jpg,jpeg,png',
                ]);
                $image = $request->image_url;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);
                $update_client = Utilities::switch_db('api')->select("UPDATE agents set image_url = '$image_path' where id = '$agency_id'");
            }

            if($request->has('password')){
                $this->validate($request, [
                   'password' => 'required|min:6',
                   'password_confirmation' => 'required|same:password'
                ]);
                $password = bcrypt($request->password);
                $update_local = DB::update("UPDATE users set password = '$password' where id = '$u_id'");
                $update_api = Utilities::switch_db('api')->select("UPDATE users set password = '$password' where id = (SELECT user_id from agents where id = '$agency_id')");
            }

            $update_local_user = DB::update("UPDATE users set first_name = '$request->first_name', last_name = '$request->last_name', address = '$request->address', username = '$request->username' where id = '$u_id'");
            $update_api_user = Utilities::switch_db('api')->update("UPDATE users set firstname = '$request->first_name', lastname = '$request->last_name', phone_number = '$request->phone' where id = (SELECT user_id from agents where id = '$agency_id') ");
            $update_user_agent = Utilities::switch_db('api')->update("UPDATE agents set nationality = '$request->country_id', location = '$request->location' where id = '$agency_id'");

            if(!$update_local_user || !$update_api_user || !$update_user_agent){
                $user_activity = Api::saveActivity($agency_id, $description, $ip, $user_agent);
                return back()->with('success', 'Profile Updated...');
            }else{
                return back()->with('error', 'Error occured while updating...');
            }

        }elseif($broadcaster_id){
            $description = 'Profile updated with the following information first name='.$request->first_name.', last name=' .$request->last_name. ', address='.$request->address.', username='.$request->username. ', phone number='.$request->phone.', location='.$request->location.', country code='.$request->country_id.', password='.$request->password.' by '.$broadcaster_id;
            if($request->hasFile('image_url')){
                $this->validate($request, [
                    'image_url' => 'required|image|mimes:jpg,jpeg,png',
                ]);
                $image = $request->image_url;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);
                $update_client = Utilities::switch_db('api')->select("UPDATE broadcasters set image_url = '$image_path' where id = '$broadcaster_id'");
            }

            if($request->has('password')){
                $this->validate($request, [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password'
                ]);
                $password = bcrypt($request->password);
                $update_local = DB::update("UPDATE users set password = '$password' where id = '$u_id'");
                $update_api = Utilities::switch_db('api')->select("UPDATE users set password = '$password' where id = (SELECT user_id from broadcasters where id = '$broadcaster_id')");
            }

            $update_local_user = DB::update("UPDATE users set first_name = '$request->first_name', last_name = '$request->last_name', address = '$request->address', username = '$request->username' where id = '$u_id'");
            $update_api_user = Utilities::switch_db('api')->update("UPDATE users set firstname = '$request->first_name', lastname = '$request->last_name', phone_number = '$request->phone' where id = (SELECT user_id from broadcasters where id = '$broadcaster_id') ");
            $update_user_agent = Utilities::switch_db('api')->update("UPDATE broadcasters set nationality = '$request->country_id', location = '$request->location' where id = '$broadcaster_id'");

            if(!$update_local_user || !$update_api_user || !$update_user_agent){
                $user_activity = Api::saveActivity($broadcaster_id, $description, $ip, $user_agent);
                return back()->with('success', 'Profile Updated...');
            }else{
                return back()->with('error', 'Error occured while updating...');
            }
        }else{
            $description = 'Profile updated with the following information first name='.$request->first_name.', last name=' .$request->last_name. ', address='.$request->address.', username='.$request->username. ', phone number='.$request->phone.', location='.$request->location.', country code='.$request->country_id.', password='.$request->password.' by '.$advertiser_id;
            if($request->hasFile('image_url')){
                $this->validate($request, [
                    'image_url' => 'required|image|mimes:jpg,jpeg,png',
                ]);
                $image = $request->image_url;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);
                $update_client = Utilities::switch_db('api')->select("UPDATE advertisers set image_url = '$image_path' where id = '$advertiser_id'");
            }
            if($request->has('password')){
                $this->validate($request, [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password'
                ]);
                $password = bcrypt($request->password);
                $update_local = DB::update("UPDATE users set password = '$password' where id = '$u_id'");
                $update_api = Utilities::switch_db('api')->select("UPDATE users set password = '$password' where id = (SELECT user_id from advertisers where id = '$advertiser_id')");
            }

            $update_local_user = DB::update("UPDATE users set first_name = '$request->first_name', last_name = '$request->last_name', address = '$request->address', username = '$request->username' where id = '$u_id'");
            $update_api_user = Utilities::switch_db('api')->update("UPDATE users set firstname = '$request->first_name', lastname = '$request->last_name', phone_number = '$request->phone' where id = (SELECT user_id from advertisers where id = '$advertiser_id') ");
            $update_user_agent = Utilities::switch_db('api')->update("UPDATE advertisers set nationality = '$request->country_id', location = '$request->location' where id = '$advertiser_id'");

            if(!$update_local_user || !$update_api_user || !$update_user_agent){
                $user_activity = Api::saveActivity($advertiser_id, $description, $ip, $user_agent);
                return back()->with('success', 'Profile Updated...');
            }else{
                return back()->with('error', 'Error occured while updating...');
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
