<?php

namespace Vanguard\Http\Controllers\Dsp;

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

    }
}
