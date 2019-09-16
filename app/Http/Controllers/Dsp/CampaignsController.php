<?php

namespace Vanguard\Http\Controllers\Dsp;

use Hash;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Campaign;
use Vanguard\Services\Campaign\CampaignDetails;
use Illuminate\Support\Facades\DB;
use Vanguard\Services\MediaAsset\GetMediaAssetByClient;
use Vanguard\Services\Traits\SplitTimeRange;
use Vanguard\Http\Requests\UpdateMpoTimeBeltRequest;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\StoreCampaignMpoAdslotRequest;
use Vanguard\Services\Mpo\GetCampaignTimeBelt;
use Vanguard\Services\Mpo\ExcelMpoExport;
use Vanguard\Services\Mpo\UpdateTimeBeltService;
use Vanguard\Events\Dsp\CampaignMpoTimeBeltUpdated;
use Vanguard\Http\Requests\AssociateFileToAdslotRequest;
use Vanguard\Http\Requests\GenerateCampaignMpoRequest;
use Vanguard\Http\Resources\CampaignMpoResource;
use Vanguard\Http\Resources\CampaignResource;
use Vanguard\Models\AdVendor;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\CampaignTimeBelt;
use Vanguard\Models\TvStation;
use Vanguard\Services\Mpo\StoreMpoService;
use Vanguard\Services\Mpo\StoreTimeBeltService;

class CampaignsController extends Controller
{
    private $campaign_dates, $utilities;

    use SplitTimeRange;
    use CompanyIdTrait;

    public function __construct(CampaignDate $campaignDate, Utilities $utilities)
    {
        $this->campaign_dates = $campaignDate;
        $this->utilities = $utilities;
        $this->middleware('permission:update.campaign')->only(['update']);
        $this->middleware('permission:view.campaign')->only(['index']);
    }

    public function index(Request $request)
    {
        $campaigns = Campaign::filter(['belongs_to'=> $this->companyId()])->get();
        $campaigns =   CampaignResource::collection($campaigns);
        return view('agency.campaigns.index')->with('campaigns', $campaigns);
    }

    public function getDetails(Request $request, $id)
    {
        $company_id = $this->companyId();
        $campaign_details_service = new CampaignDetails($id);
        $campaign_details = $campaign_details_service->run();
        $campaign_details_client_id = $campaign_details->client->id;
        $campaign_details_brand_id = $campaign_details->brand->id;
        $client_media_assets = (new GetMediaAssetByClient($campaign_details_client_id, $campaign_details_brand_id))->run();
        $campaign_files = (new GetCampaignTimeBelt($campaign_details->id))->run();
        $time_belt_range = $this->splitTimeRangeByBase('00:00:00', '23:59:59', '15');
        $ad_vendors = AdVendor::with('publishers')->where('company_id', $company_id)->get();
        return view('agency.campaigns.new_campaign_details', 
        compact('campaign_details', 'client_media_assets', 'campaign_files', 'time_belt_range', 'ad_vendors'));
    }

    public function groupCampaignTimeBelts($id, $group) 
    {
        return (new CampaignDetails($id, $group))->run();
    }

    public function generateMpo(GenerateCampaignMpoRequest $request, $campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        $this->authorize('update', $campaign);

        $validated = $request->validated();

        // //get the last mpo
        // $last_mpo = $campaign->campaign_mpos->last();
        // //convert the adslot to array
        // $last_adslot_array = json_decode($last_mpo->adslots, true);
        // //sort the adslot with duration is ascending order
        // $sorted_adslots = $this->sortAdslot($last_adslot_array);
        // //hash last mpo
        // $hashed_adslot = Hash::make(json_encode($sorted_adslots));
        // //sort incoming adslots
        // $sort_incoming_adslot = $this->sortAdslot($validated['adslots']);
        // //compare the two hashes
        // // dd(json_encode($sorted_adslots), json_encode($sort_incoming_adslot));
        // dd(Hash::check(json_encode($sort_incoming_adslot), $hashed_adslot));
        // if(Hash::check(json_encode($sort_incoming_adslot), $hashed_adslot)){
        //     return 'exists';
        // }

        //generate mpo
        (new StoreMpoService($validated, $campaign_id))->run();

        return $this->listMpos($campaign_id);   
    }

    private function sortAdslot($adslots)
    {
        array_multisort(array_map(function($element) {
            return $element['duration'];
        }, $adslots), SORT_ASC, $adslots);
        return $adslots;
    }

    public function exportMpoAsExcel($campaign_id, $mpo_id)
    {
        return (new ExcelMpoExport($mpo_id))->run();
    }

    public function listMpos($campaign_id)
    {
        $mpos = CampaignMpo::with('vendor.contacts')->where('campaign_id', $campaign_id)
                            ->where('ad_vendor_id', '<>', '')
                            ->latest()->get()
                            ->unique('ad_vendor_id');
        return CampaignMpoResource::collection($mpos);
    }

    public function associateAssetsToAdslot(AssociateFileToAdslotRequest $request, $campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        $this->authorize('update', $campaign);
        $validated = $request->validated();
        \DB::transaction(function () use ($campaign_id, $validated, $request) {
            foreach ($validated['durations'] as $duration) {
                if (array_key_exists($duration, $validated['assets'])) {
                    CampaignTimeBelt::where('campaign_id', $campaign_id)
                                    ->where('duration',$duration)
                                    ->whereIn('id', $request->id)
                                    ->update(['asset_id' => $validated['assets'][$duration]]);
                }
            }
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign->id, $request->group))->run()
        ]);
    }

    public function deleteAdslot(Request $request, $campaign_id, $adslot_id)
    {
        $campaign_mpo_time_belt = CampaignTimeBelt::findOrFail($adslot_id);
        $campaign = $campaign_mpo_time_belt->campaign;
        $this->authorize('delete', $campaign);
        
        DB::transaction(function() use ($campaign_mpo_time_belt, $campaign) {
            $campaign_mpo_time_belt->delete();
            event(new CampaignMpoTimeBeltUpdated($campaign));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot deleted successfully',
            'data' => (new CampaignDetails($campaign->id, $request->group))->run()
        ]);
    }

    public function updateAdslot(UpdateMpoTimeBeltRequest $request, $campaign_id, $adslot_id)
    {
        $campaign_time_belt = CampaignTimeBelt::findOrFail($adslot_id);
        $campaign = $campaign_time_belt->campaign;
        $this->authorize('update', $campaign);

        $validated = $request->validated();

        DB::transaction(function() use ($validated, $campaign) {
            (new UpdateTimeBeltService($validated))->run();
            event(new CampaignMpoTimeBeltUpdated($campaign));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign->id, $request->group))->run()
        ]);
    }

    public function updateMultipleAdslots(UpdateMpoTimeBeltRequest $request, $campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        $this->authorize('update', $campaign);

        $validated = $request->validated();

        DB::transaction(function() use ($validated, $campaign) {
            (new UpdateTimeBeltService($validated))->run();
            event(new CampaignMpoTimeBeltUpdated($campaign));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign->id, $request->group))->run()
        ]);  
    }

    public function storeAdslot(StoreCampaignMpoAdslotRequest $request, $campaign_id)
    {
        $campaign = Campaign::findOrFail($campaign_id);
        $this->authorize('store', $campaign);

        $validated = $request->validated();

        DB::transaction(function() use ($validated, $campaign, $campaign_id) {
            (new StoreTimeBeltService($validated, $campaign_id))->run();
            event(new CampaignMpoTimeBeltUpdated($campaign));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign->id))->run()
        ]);
    }
    
}