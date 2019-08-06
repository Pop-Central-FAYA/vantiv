<?php

namespace Vanguard\Http\Controllers;

use Session;
use Faker\Factory;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Client\StoreClient;
use Illuminate\Support\Facades\Auth;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Client\StoreRequest;
use Vanguard\Http\Resources\ClientResource;


class ClientController extends Controller
{
    use CompanyIdTrait;
    public function storeClient(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $new_client = new StoreClient($request, $this->companyId(), $user);
        $client = $new_client->run(); 
        return new ClientResource($client);
    }
    
}
