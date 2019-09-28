<?php

namespace Vanguard\Services\MediaAsset;
use Vanguard\Models\MediaAsset;
use Auth;
use Log;
class GetMediaAssets
{
    public function __construct()
    {
       // constructor parameters goes here
    }

    public function run()
    {
        return $this->getMediaAssetByCompany();
    }

    public function getMediaAssetByCompany()
    {
        $company_id = Auth::user()->companies->first()->id;
        $media_assets = MediaAsset::with(['client:id,name','brand:id,name'])
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $final_assets = [];
        foreach ($media_assets as $asset) {
           if ($asset['client'] == null || $asset['brand'] == null) {
               Log::warning("Client for media asset {$asset['id']} is null");
           } else {
               $final_assets[] = $asset;
           }
        }
        return $final_assets;
    }    
}