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
use Vanguard\Http\Requests\Client\UpdateRequest;
use Vanguard\Services\Client\UpdateService;
use Vanguard\Http\Requests\Client\ListRequest;
use Vanguard\Models\Campaign;
use Vanguard\Http\Resources\ClientCollection;
use Illuminate\Http\Request;
use Vanguard\Services\Client\GetClientDetails;
use Vanguard\Services\Client\GetCampaignsByClient;

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
        $resource = new ClientResource(Client::with('contacts', 'brands')->find($client->id));
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
     */

    public function list(ListRequest $request)
    {           
        $validated = $request->validated();
        $validated['company_id'] = $this->companyId();
        $new_get_client = new GetClientDetails($validated);
        $client_details = $new_get_client->run(); 
        return new ClientCollection($client_details);
    }

    
    public function index(Request $request)
    {   
        return view('agency.clients.index');
    }

      /**
     * Retrive a single client
     */
    public function get($id)
    {
        $client = Client::with('contacts', 'brands')->findOrFail($id);
        $campaigns = new GetCampaignsByClient($id);
        $campaign_list = $campaigns->run();
        $this->authorize('get', $client);
        return view('agency.clients.client')->with('client', $client)->with('campaign_list', $campaign_list);
       
    }
}
