<?php

namespace Vanguard\Http\Controllers\Dsp;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Campaign;
use Vanguard\Services\Campaign\CampaignDetails;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\CampaignMpoTimeBelt;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Exception;
use Vanguard\Services\MediaAsset\GetMediaAssetByClient;
use Vanguard\Services\Traits\SplitTimeRange;
use Vanguard\Http\Requests\UpdateMpoTimeBeltRequest;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\StoreCampaignMpoAdslotRequest;
use Vanguard\Services\Mpo\StoreCampaignMpoTimeBelt;
use Vanguard\Services\Mpo\GetCampaignMpoTimeBelts;
use Vanguard\Services\Mpo\ExcelMpoExport;
use Vanguard\Services\Mpo\UpdateTimeBeltService;
use Vanguard\Events\Dsp\CampaignMpoTimeBeltUpdated;
use Vanguard\Http\Resources\CampaignResource;

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

    public function getDetails($id)
    {
        $agency_id = $this->companyId();
        $campaign_details_service = new CampaignDetails($id);
        $campaign_details = $campaign_details_service->run();
        $mpos_id = $campaign_details->campaign_mpos->pluck('id');
        $campaign_details_client_id = $campaign_details->client->id;
        $campaign_details_brand_id = $campaign_details->brand->id;
        $client_media_assets = (new GetMediaAssetByClient($campaign_details_client_id, $campaign_details_brand_id))->run();
        $campaign_files = (new GetCampaignMpoTimeBelts($mpos_id))->run();
        $time_belts = $this->splitTimeRangeByBase('00:00:00', '23:59:59', '15');
        return view('agency.campaigns.new_campaign_details', compact('campaign_details', 'client_media_assets', 'campaign_files', 'time_belts'));
    }

    public function exportMpoAsExcel($campaign_mpo_id)
    {
        return (new ExcelMpoExport($campaign_mpo_id))->run();
    }

    public function associateAssetsToMpo()
    {
        $file_durations = request()->durations;
        $media_assets = request()->assets;
        $mpo_id = request()->mpo_id;

        try {
            \DB::transaction(function () use ($file_durations, $media_assets, $mpo_id) {
                foreach ($file_durations as $key => $duration) {
                    if (array_key_exists($key, $media_assets)) {
                        CampaignMpoTimeBelt::where('mpo_id', $mpo_id)->where('duration',$duration)->update(['asset_id' => $media_assets[$key]]);
                    }
                }
            });
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => 'Something went wrong, Media asset cannot be associated with MPO.'.$ex->getMessage()
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => 'Media Assets successfully associated to MPO'
        ]);
    }

    public function deleteAdslot(Request $request, $mpo_id, $adslot_id)
    {
        $campaign_mpo_time_belt = CampaignMpoTimeBelt::findOrFail($adslot_id);
        $campaign_mpo = $campaign_mpo_time_belt->campaign_mpo;
        $this->authorize('delete', $campaign_mpo);
        
        DB::transaction(function() use ($campaign_mpo_time_belt, $campaign_mpo) {
            $campaign_mpo_time_belt->delete();
            event(new CampaignMpoTimeBeltUpdated($campaign_mpo));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot deleted successfully',
            'data' => (new CampaignDetails($campaign_mpo->campaign_id))->run()
        ]);
    }

    public function updateAdslot(UpdateMpoTimeBeltRequest $request, $mpo_id, $adslot_id)
    {
        $campaign_mpo_time_belt = CampaignMpoTimeBelt::findOrFail($adslot_id);
        $campaign_mpo = $campaign_mpo_time_belt->campaign_mpo;
        $this->authorize('update', $campaign_mpo);

        $validated = $request->validated();

        DB::transaction(function() use ($validated, $campaign_mpo) {
            (new UpdateTimeBeltService($validated))->run();
            event(new CampaignMpoTimeBeltUpdated($campaign_mpo));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign_mpo->campaign_id))->run()
        ]);
    }

    public function updateMultipleAdslots(UpdateMpoTimeBeltRequest $request, $mpo_id)
    {
        $campaign_mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('update', $campaign_mpo);

        $validated = $request->validated();

        DB::transaction(function() use ($validated, $campaign_mpo) {
            (new UpdateTimeBeltService($validated))->run();
            event(new CampaignMpoTimeBeltUpdated($campaign_mpo));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign_mpo->campaign_id))->run()
        ]);  
    }

    public function storeAdslot(StoreCampaignMpoAdslotRequest $request, $mpo_id)
    {
        $campaign_mpo = CampaignMpo::findOrFail($mpo_id);
        $this->authorize('store', $campaign_mpo);

        $validated = $request->validated();

        DB::transaction(function() use ($validated, $campaign_mpo, $mpo_id) {
            (new StoreCampaignMpoTimeBelt($validated, $mpo_id))->run();
            event(new CampaignMpoTimeBeltUpdated($campaign_mpo));
        });
        //this will be changed when we create a resource for the campaign
        return response()->json([
            'status' => 'success',
            'message' => 'Adslot updated successfully',
            'data' => (new CampaignDetails($campaign_mpo->campaign_id))->run()
        ]);
    }
}