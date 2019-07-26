<?php

namespace Vanguard\Http\Controllers\Ssp;

use Auth;
use Vanguard\User;
use Vanguard\Http\Controllers\ProfileManagementsController as MainProfileManagementController; 

class ProfileManagementsController extends MainProfileManagementController
{
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        return view('broadcaster_module.profile.index')->with('user', $user);
    }
}
