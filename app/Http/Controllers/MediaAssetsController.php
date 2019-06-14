<?php

namespace Vanguard\Http\Controllers;
use Illuminate\Http\Request;
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\MediaAsset\ValidateCreateAsset;
use Vanguard\Services\MediaAsset\StoreMediaAsset;
use Vanguard\Services\MediaAsset\GetMediaAssets;
use Vanguard\Models\MediaAsset;

class MediaAssetsController extends Controller
{
    public function index()
    {
        // get clients associated with the logged in user company
        $clients = new AllClient(\Auth::user()->companies->first()->id);
        $clients = $clients->getAllClients();
        return view('agency.media_assets.index')->with('clients', $clients);
    }

    public function createAsset(Request $request)
    {
        // Validate requests
        $validate_media_asset = new ValidateCreateAsset($request->all());
        $validator = $validate_media_asset->run();

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => implode(",", $validator->messages()->all())
            ]);
        }

        // store asset to db
        $store_media_asset = new StoreMediaAsset($request->all());
        $new_media_asset = $store_media_asset->run();

        if ($new_media_asset) {
            return response()->json([
                'status' => 'success',
                'data' => 'Media asset was successfully created'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => 'Something went wrong, media assets cannot be created. Try again!'
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
