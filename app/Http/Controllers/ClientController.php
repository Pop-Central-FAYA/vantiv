<?php

namespace Vanguard\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Faker\Factory;
use Vanguard\Services\Client\StoreClient;
use Vanguard\Services\Client\StoreClientContact;


class ClientController extends Controller
{
    public function store(Request $request)
    {
        return response()->json(true, 201);
    }
    
}
