<?php

namespace Vanguard\Providers;

use Illuminate\Support\ServiceProvider;
use Vanguard\Country;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Agency;
use Vanguard\Models\Broadcaster;
use Vanguard\User;

class ProfileServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view)
        {
            /**
             * will have to come back here and do some modification when the user role and permission has been finally
             * implemented plus the legal entity
             */
            if(\Auth::check()){
                $countries = Country::all();
                $broadcaster_id = \Session::get('broadcaster_id');
                $agency_id = \Session::get('agency_id');
                $broadcaster_details = null;
                $agency_details = null;
                $user = User::where('id', \Auth::user()->id)->first();
                if($broadcaster_id){
                    $broadcaster_details = Broadcaster::where('user_id', \Auth::user()->id)->first();
                }else{
                    $agency_details = Agency::where('user_id', \Auth::user()->id)->first();
                }
                $user_details = $this->getUserDetails($user, $broadcaster_id, $agency_details, $broadcaster_details);

                $view->with('countries', $countries)
                    ->with('profile_user_details', $user_details)
                    ->with('agency_id', $agency_id)
                    ->with('broadcaster_id', $broadcaster_id);
            }
        });
    }

    public function getUserDetails($user, $broadcaster_id,$agency_details, $broadcaster_details)
    {
        return [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'phone' => $user->phone_number,
            'email' => $user->email,
            'address' => $user->address,
            'location' => $broadcaster_id ? $broadcaster_details->location : $agency_details->location,
            'nationality' => $broadcaster_id ? $broadcaster_details->nationality : $agency_details->nationality,
            'username' => $user->username,
            'image' => $broadcaster_id ? $broadcaster_details->image_url : $agency_details->image_url,
        ];
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
