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
use Vanguard\Http\Requests\Client\ListRequest;

use Vanguard\Http\Resources\ClientCollection;

class ClientController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:create.client')->only(['create']);
        $this->middleware('permission:update.client')->only(['update']);
        $this->middleware('permission:view.client')->only(['list']);
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

      /**
     * Return a list of clients that the currently logged in user has permission to view
     * Filter parameters are allowed
     * @todo get list of client
     * @todo get summation of total spendings
     * @todo get date created
     * @todo get active campaigns
     */
    public function list(ListRequest $request)
    {           
        $itemCount =0;
        $validated = $request->validated();
        $validated['company_id'] = $this->companyId();
        $client_list = Client::with('contacts')->filter($validated)->get();
        foreach ($client_list as $client) {
            $itemCount = $client->contacts()->count();
        }
        //return new ClientCollection($client_list);

        return $itemCount;
    }
    
}
