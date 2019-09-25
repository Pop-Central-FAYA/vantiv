<?php

namespace Vanguard\Http\Controllers\Dsp;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\AdVendor\CreateService;
use Vanguard\Services\AdVendor\UpdateService;
use Vanguard\Http\Resources\AdVendorResource;
use Vanguard\Http\Resources\AdVendorCollection;
use Vanguard\Models\AdVendor;
use Vanguard\Models\Publisher;
use Vanguard\Http\Requests\AdVendor\ListRequest;
use Vanguard\Http\Requests\AdVendor\StoreRequest;
use Vanguard\Http\Requests\AdVendor\UpdateRequest;
use Vanguard\Models\CampaignMpo;

class AdVendorController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:view.ad_vendor')->only(['list', 'get', 'index']);
        $this->middleware('permission:create.ad_vendor')->only(['create']);
        $this->middleware('permission:update.ad_vendor')->only(['update']);
    }

    /*******************************
     * BELOW ARE THE PAGES. (WHEN THIS BECOMES AN SPA, THERE SHOULD ONLY REALLY BE ONE OR TWO)
     *******************************/
    public function index(Request $request)
    {   
        $routes = [
            'list' => route('ad-vendor.list', [], false),
            'create' => route('ad-vendor.create', [], false)
        ];
        $publishers = Publisher::all(['id', 'name']);
        return view('agency.ad_vendor.index')
                    ->with('publishers', $publishers)
                    ->with('routes', $routes);
    }

     /**
     *View showing a single ad vendor
     */
    public function details($id)
    {
        $vendor = AdVendor::findOrFail($id);
        $this->authorize('get', $vendor);
        $mpos = CampaignMpo::with(array('campaign'=>function($query){
            $query->select('id','name');
        }))->where('ad_vendor_id', $id)->get();
        return view('agency.ad_vendor.ad_vendor')
        ->with('ad_vendor', new AdVendorResource($vendor))->with('mpos', $mpos);
    }


    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * Return a list of ad vendors that the currently logged in user has permission to view
     * Filter parameters are allowed
     * No need for a service now, until the query gets more complicated
     */
    public function list(ListRequest $request)
    {           
        $validated = $request->validated();
        $validated['company_id'] = $this->companyId();
        $vendor_list = AdVendor::filter($validated)->get();
        return new AdVendorCollection($vendor_list);
    }

    /**
     * Retrive a single ad vendor (no need for a service for now unless the details get more complicated)
     */
    public function get($id)
    {
        $vendor = AdVendor::findOrFail($id);
        $this->authorize('get', $vendor);
        return new AdVendorResource($vendor);
    }

    /**
     * This method orchestrates the creation of a new vendor.
     * Note, validation will automatically send a response json back on validation error
     */
    public function create(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $company_id = $this->companyId();
        $create_service = new CreateService($validated, $company_id, $user->id);
        $vendor = $create_service->run();
        
        $resource = new AdVendorResource(AdVendor::find($vendor->id));
        return $resource->response()->setStatusCode(201);
    }

    /**
     * Update fields that have changed in ad vendors
     */
    public function update(UpdateRequest $request, $id)
    {
        $vendor = AdVendor::findOrFail($id);
        $this->authorize('update', $vendor);

        $user = auth()->user();
        $validated = $request->validated();
        (new UpdateService($validated, $vendor, $user->id))->run();
        return new AdVendorResource(AdVendor::find($id));
    }
}
