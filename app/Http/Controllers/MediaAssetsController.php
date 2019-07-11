<?php

namespace Vanguard\Http\Controllers;
use Illuminate\Http\Request;
use Vanguard\Services\Client\AllClient;
use Vanguard\Http\Requests\StoreMediaAsset;
use Vanguard\Services\MediaAsset\CreateMediaAsset;
use Vanguard\Services\MediaAsset\GetMediaAssets;
use Vanguard\Models\MediaAsset;
use Vanguard\Services\Client\ClientBrand;

class MediaAssetsController extends Controller
{
    public function index()
    {
        // get clients associated with the logged in user company
        $clients = new AllClient(\Auth::user()->companies->first()->id);
        $clients = $clients->getAllClients();
        $client_brands = [];
        foreach ($clients as $client) {
            $brands = new ClientBrand($client->id);
            $client_brands[$client->id] = $brands->run();
        }
        return view('agency.media_assets.index')->with('clients', $clients)->with('brands', $client_brands);
    }

    public function getBrandsWithClients($id)
    {
        $client_brands = new ClientBrand($id);
        $brands = $client_brands->run();
        return response()->json(['brands' => $brands]);
    }

    public function createAsset(StoreMediaAsset $request)
    {
        // store asset to db
        $store_media_asset = new CreateMediaAsset($request->all());
        $new_media_asset = $store_media_asset->run();

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
        // soft delete model
        MediaAsset::destroy($id);

        // call get all assets method
        return $this->getAssets();
    }
}
