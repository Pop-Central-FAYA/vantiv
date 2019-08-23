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
        $validated = $request->validated();
        $validated['company_id'] = $this->companyId();
        $client_list = Client::with('contacts', 'brands')->filter($validated)->get();
        $client_details = $this->getClientDetails($client_list);
        return new ClientCollection($client_details);
    }

    public function getClientDetails($client_list)
    {
        $item_clients = [];
        foreach ($client_list as $client) 
        {
            $brands = 0;
            $sum_active_campaign = 0;
            $client_spendings = 0;
            foreach ($client->brands as $brand) 
            {
                $brands++;
                $sum_active_campaign += $this->getActiveCampaign($brand->id);
                $client_spendings += $this->getBrandSpendings($brand->id);
            }
            $item_client = array(
                'id' => $client->id,
                'image_url' => $client->image_url,
                'name'=> $client->name, 
                'number_brands' => $brands, 
                'sum_active_campaign' => $sum_active_campaign,
                'client_spendings' => $client_spendings,  
                'date_created' => $client->time_created, 
            );
            array_push($item_clients, $item_client);
        }
        return collect($item_clients);
        
    }

    public function getActiveCampaign($brand_id)
    {
        $campaigns = Campaign::where([['brand_id', '=', $brand_id], ['status', '=', 'active']])->get()->count();
        return $campaigns;
    }
    
    public function getBrandSpendings($brand_id)
    {
        $brand_spendings = 0;
        $campaigns = Campaign::where('brand_id', '=', $brand_id)->get();
        foreach ($campaigns as $campaign) 
        {
              $brand_spendings += $campaign->budget;
        }
        return $brand_spendings;
    }
}
