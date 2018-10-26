<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Requests\ProfileUpdateRequest;
use Vanguard\Libraries\AmazonS3;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Auth;
use DB;
use JD\Cloudder\Facades\Cloudder;
use Session;

class ProfileManagementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
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
    public function updateDetails(ProfileUpdateRequest $request)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $agency_id = \Session::get('agency_id');
        $u_id = Auth::user()->id;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = request()->ip();

        if($agency_id){
            $agency_update = $this->updateAgency($request, $agency_id, $u_id);
            if($agency_update === 'success'){
                Session::flash('success', 'Profile updated successfully');
                return redirect()->back();
            }else{
                Session::flash('error', 'An error occurred while performing your request');
                return redirect()->back();
            }
        }else {
            $broadcaster_update = $this->updateBroadcaster($request, $broadcaster_id, $u_id);
            if($broadcaster_update === 'success'){
                Session::flash('success', 'Profile updated successfully');
                return redirect()->back();
            }else{
                Session::flash('error', 'An error occurred while performing your request');
                return redirect()->back();
            }
        }

    }

    public function updateBroadcaster($request, $broadcaster_id, $u_id)
    {
        if($request->hasFile('image_url')){
            $image_path = $this->uploadImage($request);
            $update_client = Utilities::switch_db('api')->select("UPDATE broadcasters set image_url = '$image_path' where id = '$broadcaster_id'");
        }

        if($request->has('password')){
            $password = $this->validatePassword($request, $u_id);
            $update_api = Utilities::switch_db('api')->select("UPDATE users set password = '$password' where id = (SELECT user_id from broadcasters where id = '$broadcaster_id')");
        }
        try {
            Utilities::switch_db('api')->transaction(function () use ($request, $broadcaster_id, $u_id) {
                DB::update("UPDATE users SET first_name = '$request->first_name',
                                                    last_name = '$request->last_name',
                                                    address = '$request->address',
                                                    username = '$request->username'
                                                WHERE id = '$u_id'");
                Utilities::switch_db('api')->update("UPDATE users SET  firstname = '$request->first_name',
                                                                            lastname = '$request->last_name',
                                                                            phone_number = '$request->phone'
                                                                      WHERE id = (SELECT user_id from broadcasters where id = '$broadcaster_id') ");
                Utilities::switch_db('api')->update("UPDATE broadcasters SET nationality = '$request->country_id',
                                                                                  location = '$request->location' where id = '$broadcaster_id'");

            });
        }catch (\Exception $e) {
            return 'error';
        }
        return 'success';
    }

    public function updateAgency($request, $agency_id, $u_id)
    {

        if($request->hasFile('image_url')){
            $image_path = $this->uploadImage($request);
            $update_client = Utilities::switch_db('api')->select("UPDATE agents set image_url = '$image_path' where id = '$agency_id'");
        }

        if($request->has('password')){
            $password = $this->validatePassword($request, $u_id);
            $update_api = Utilities::switch_db('api')->select("UPDATE users set password = '$password' where id = (SELECT user_id from agents where id = '$agency_id')");
        }

        try {
                Utilities::switch_db('api')->transaction(function () use ($request, $u_id, $agency_id) {
                $a = DB::update("UPDATE users SET  first_name = '$request->first_name',
                                                    last_name = '$request->last_name',
                                                    address = '$request->address',
                                                    username = '$request->username'
                                                WHERE id = '$u_id'");
                $b = Utilities::switch_db('api')->update("UPDATE users SET firstname = '$request->first_name',
                                                                          lastname = '$request->last_name',
                                                                          phone_number = '$request->phone'
                                                                      WHERE
                                                                        id = (SELECT user_id from agents where id = '$agency_id') ");
                $c = Utilities::switch_db('api')->update("UPDATE agents SET nationality = '$request->country_id',
                                                                           location = '$request->location'
                                                                       WHERE id = '$agency_id'");
            });
        }catch (\Exception $e){
            return 'error';
        }
        return 'success';

    }

    public function uploadImage($request)
    {
        $this->validate($request, [
            'image_url' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        $image = $request->image_url;
        $filename = realpath($image);
        $key = 'profile-images/'.uniqid().'-'.$image->getClientOriginalName();
        $image_url = AmazonS3::uploadToS3FromPath($filename, $key);
        return $image_url;
    }

    public function validatePassword($request, $u_id)
    {
        $this->validate($request, [
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        $password = bcrypt($request->password);
        $update_local = DB::update("UPDATE users set password = '$password' where id = '$u_id'");
        return $password;
    }

}
