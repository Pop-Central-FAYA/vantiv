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
use Vanguard\Models\Client;
use Vanguard\Models\Brand;
use Vanguard\Http\Requests\Client\UpdateRequest;


class ClientController extends Controller
{
    use CompanyIdTrait;
    public function storeClient(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $new_client = new StoreClient($request, $this->companyId(), $user->id);
        $client = $new_client->run();   
        $resource = new ClientResource(Client::with('contact', 'brand')->find($client->id));
        return $resource->response()->setStatusCode(201);
    }

     /**
     * Update fields that have changed in ad vendors
     */
    public function update(UpdateRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);

        $validated = $request->validated();
        (new UpdateService($vendor, $validated))->run();
        
        return new ClientResource(Client::with('contact', 'brand')->find($id));
    }
    
}
