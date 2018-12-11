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

    }

}
