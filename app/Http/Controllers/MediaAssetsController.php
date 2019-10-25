<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\StoreMediaAsset;
use Vanguard\Services\MediaAsset\CreateMediaAsset;
use Vanguard\Services\MediaAsset\GetMediaAssets;
use Vanguard\Models\MediaAsset;
use Vanguard\Models\Client;
use Vanguard\Libraries\ActivityLog\LogActivity;

class MediaAssetsController extends Controller
{

    use CompanyIdTrait;

    public function index()
    {
        // get clients associated with the logged in user company
        $clients = Client::with('brands')->filter(['company_id' => $this->companyId()])->get();
        $client_brands = [];
        $client_list = [];
        foreach ($clients as $client) {
            $brand_list = [];
            foreach ($client->brands as $brand) {
                $brand_list[] = ["name" => $brand->name, "id" => $brand->id];
            }
            //this is because we currently have some clients without brands
            if (count($brand_list) > 0) {
                $client_brands[$client->id] = $brand_list;
                $client_list[] = ["company_name" => $client->name, "id" => $client->id];
            }

        }
        return view('agency.media_assets.index')->with('clients', $client_list)->with('brands', $client_brands);
    }

    public function createAsset(StoreMediaAsset $request)
    {
        // store asset to db
        $store_media_asset = new CreateMediaAsset($request->all());
        $new_media_asset = $store_media_asset->run();
        $logactivity = new LogActivity($new_media_asset, "Created");
        $log = $logactivity->log();
        if ($new_media_asset) {
            $media_assets = new GetMediaAssets();
            return response()->json([
                'status' => 'success',
                'data' => $media_assets->run()
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => []
            ]);
        }
    }

    public function getAssets()
    {
        $media_assets = new GetMediaAssets();
        return response()->json([
            'status' => 'success',
            'data' => $media_assets->run()
        ]);
    }

    public function deleteAsset($id)
    {
        $media_asset =  MediaAsset::findOrFail($id);
        $logactivity = new LogActivity($media_asset, "Deleted");
        $log = $logactivity->log();
         // soft delete model
        MediaAsset::destroy($id);
        // call get all assets method
        return $this->getAssets();
    }
}
