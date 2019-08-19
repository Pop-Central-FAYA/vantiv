<?php

namespace Vanguard\Http\Controllers\Guest;

use Vanguard\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Vanguard\Models\MpoShareLink;
use Vanguard\Services\Mpo\StoreMpoShareLinkActivity;

class MpoShareLinkController extends Controller
{
    public function index(Request $request, $id)
    {
        $share_link = MpoShareLink::findOrFail($id);
        if ($share_link->isExpired($id) || !$request->hasValidSignature()) {
            Session::flash('error', 'Link expired');
            (new StoreMpoShareLinkActivity('Link expired'))->run();
            return view('errors.expired_link'); //this is a newly created page
        }
        (new StoreMpoShareLinkActivity('Link is active'))->run();
        return view('guest.mpo')->with('share_link', $share_link);
    }
}
