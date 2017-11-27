<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;

class MpoController extends Controller
{
    public function index()
    {
        return view('mpos.index');
    }

    public function pending_mpos()
    {
        return view('mpos.pending-mpos');
    }
}
