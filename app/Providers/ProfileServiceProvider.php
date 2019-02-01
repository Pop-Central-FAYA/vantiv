<?php

namespace Vanguard\Providers;

use Illuminate\Support\ServiceProvider;
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
                $broadcaster_id = \Session::get('broadcaster_id');
                $agency_id = \Session::get('agency_id');
                $user = User::where('id', \Auth::user()->id)->first();
                $user_details = $this->getUserDetails($user);

                $view->with('profile_user_details', $user_details)
                    ->with('agency_id', $agency_id)
                    ->with('broadcaster_id', $broadcaster_id);
            }
        });
    }

    public function getUserDetails($user)
    {
        return [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'phone' => $user->phone_number,
            'email' => $user->email,
            'address' => $user->address,
            'username' => $user->username,
            'image' => $user->avatar ? $user->avatar : '',
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
