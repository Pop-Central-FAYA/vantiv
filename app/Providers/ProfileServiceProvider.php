<?php

namespace Vanguard\Providers;

use Illuminate\Support\ServiceProvider;
use Vanguard\Country;
use Vanguard\Libraries\Utilities;

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
            if(\Auth::check()){
                $countries = Country::all();
                $broadcaster_id = \Session::get('broadcaster_id');
                $agency_id = \Session::get('agency_id');
                $u_id = \Auth::user()->id;
                if($agency_id){
                    $api_user = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from agents where id = '$agency_id')");
                    $local_user = \DB::select("SELECT * from users where id = '$u_id'");
                    $api_agent = Utilities::switch_db('api')->select("SELECT * from agents where id = '$agency_id'");
                }else{
                    $api_user = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from broadcasters where id = '$broadcaster_id')");
                    $local_user = \DB::select("SELECT * from users where id = '$u_id'");
                    $api_agent = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
                }

                $user_details = Utilities::getProfileDetails($api_user, $local_user, $api_agent);

                $view->with('countries', $countries)
                    ->with('profile_user_details', $user_details)
                    ->with('agency_id', $agency_id)
                    ->with('broadcaster_id', $broadcaster_id);
            }
        });
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
