<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Requests\ProfileUpdateRequest;
use Vanguard\Libraries\AmazonS3;
use Vanguard\Libraries\Enum\ClassMessages;
use Auth;
use Session;
use Vanguard\Services\User\UpdateUser;
use Vanguard\User;
use Vanguard\Libraries\Enum\CompanyTypeName;

class ProfileManagementsController extends Controller
{

    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        //this is just hack around as it will be fixed in the controller seperation ticket to use proper inheritance
        if($user->company_type === CompanyTypeName::BROADCASTER){
            return view('broadcaster_module.profile.index')->with('user', $user);
        }else{
            return view('agency.profile.index')->with('user', $user);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDetails(ProfileUpdateRequest $request)
    {
        if($request->password) {
            $this->validatePassword($request);
        }

        if($request->image_url){
            $this->uploadImage($request);
        }
        $update_user_service = new UpdateUser(Auth::user()->id, $request->first_name, $request->last_name, $request->phone,
            $request->address, null, null, 'profile_update', Auth::user()->status);
        $update_user_service->updateUser();

        Session::flash('success', ClassMessages::PROFILE_UPDATE_SUCCESS);
        return redirect()->back();
    }


    public function uploadImage($request)
    {
        $this->validate($request, [
            'image_url' => 'required|image|mimes:jpg,jpeg,png',
        ]);
        $avatar = $request->image_url;
        $filename = realpath($avatar);
        $key = 'profile-images/'.uniqid().'-'.$avatar->getClientOriginalName();
        $image_url = AmazonS3::uploadToS3FromPath($filename, $key);

        $update_user_service = new UpdateUser(Auth::user()->id, null, null, null,
                                    null, $image_url, null, 'profile_update', Auth::user()->status);
        return $update_user_service->updateAvatar();
    }

    public function validatePassword($request)
    {
        $this->validate($request, [
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        $update_user_service = new UpdateUser(Auth::user()->id, null, null, null,
            null, null, $request->password, 'profile_update', Auth::user()->status);
        return $update_user_service->updatePassword();
    }

}
