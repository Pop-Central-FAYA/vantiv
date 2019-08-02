<?php

namespace Vanguard\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Faker\Factory;
use Vanguard\Services\Client\StoreClient;
use Vanguard\Services\Client\StoreClientContact;
use Illuminate\Support\Facades\Auth;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;


class ClientController extends Controller
{
    use CompanyIdTrait;
    public function storeClient(Request $request)
    {
        $user = Auth::user();
        $new_client = new StoreClient($request, $this->companyId(), $user);
        $client = $new_client->run(); 

        if (is_bool($client) == true && $client) {
           // insertion successfull
        return "Added successfully";
        }else{
           //return error
        return $client;
        }

    }
    
}
