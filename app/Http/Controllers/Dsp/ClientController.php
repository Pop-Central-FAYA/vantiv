<?php

namespace Vanguard\Http\Controllers\Dsp;

use Session;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Client\StoreClient;
use Illuminate\Support\Facades\Auth;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Client\StoreRequest;
use Vanguard\Http\Resources\ClientResource;
use Vanguard\Models\Client;
use Vanguard\Models\Brand;
use Vanguard\Http\Requests\Client\UpdateRequest;
use Vanguard\Services\Client\UpdateService;
use Illuminate\Http\Request;
use Vanguard\Libraries\Enum\ClassMessages;



class ClientController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:create.client')->only(['create']);
        $this->middleware('permission:update.client')->only(['update', 'destroy']);
    }

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $new_client = new StoreClient($validated, $this->companyId(), $user->id);
        $client = $new_client->run();   
        $resource = new ClientResource(Client::with('contact', 'brand')->find($client->id));
        return $resource->response()->setStatusCode(201);
    }

     /**
     * Update fields that have changed in client
     */
    public function update(UpdateRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);
        $validated = $request->validated();
        (new UpdateService($client, $validated))->run();

        $resource = new ClientResource(Client::with('contacts')->find($id));
        return $resource->response()->setStatusCode(200);

    }
    
}
