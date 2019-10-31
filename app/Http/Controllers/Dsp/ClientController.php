<?php

namespace Vanguard\Http\Controllers\Dsp;

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
use Vanguard\Http\Resources\ClientCollection;
use Illuminate\Http\Request;
use Vanguard\Services\Client\GetClientDetails;
use Vanguard\Services\Campaign\ListingService;
use Vanguard\Http\Resources\CampaignResource;
use Vanguard\Models\Brand;
use Illuminate\Support\Arr;
use Vanguard\Http\Resources\BrandCollection;
use Vanguard\Models\Campaign;
use Vanguard\Libraries\ActivityLog\LogActivity;

class ClientController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:create.client')->only(['create']);
        $this->middleware('permission:update.client')->only(['update']);
        $this->middleware('permission:view.client')->only(['list', 'get']);
    }

    /*******************************
     * BELOW ARE THE PAGES.
     *******************************/

    public function index(Request $request)
    {   
        return view('agency.clients.index');
    }

    /**
     *View showing a single client
     */
    public function details($id)
    {
        $client = Client::with('contacts', 'brands')->findOrFail($id);
        $this->authorize('get', $client);
        $client = new ClientResource($client);
        $brand_id = Arr::pluck($client->brands, 'id');
        $brands = Brand::with('campaigns')->whereIn('id', $brand_id)->get();
        $brands = new BrandCollection($brands);
        $campaigns = CampaignResource::collection(Campaign::filter(['brand_id'=> $brand_id])->get());
        return view('agency.clients.client')
        ->with('client', $client)
        ->with('campaign_list', $campaigns)
        ->with('brands', $brands);;
       
    }

    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * Return a list of client that the currently logged in user has permission to view
     * Filter parameters are allowed
     */
    
    public function create(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $new_client = new StoreClient($validated, $this->companyId(), $user->id);
        $client = $new_client->run(); 
        $logactivity = new LogActivity($client, "created client");
        $log = $logactivity->log();  
        $resource = new ClientResource(Client::with('contacts', 'brands')->find($client->id));
        return $resource->response()->setStatusCode(201);
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

    /**
     * Api Retrive a single client
     */
    public function get($id)
    {
        $client = Client::with('contacts', 'brands')->findOrFail($id);
        $this->authorize('get', $client);
        return  new ClientResource($client);
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

        $logactivity = new LogActivity($client, "updated client");
        $log = $logactivity->log();
        $resource = new ClientResource(Client::with('contacts')->find($id));
        return $resource->response()->setStatusCode(200);

    }
}
