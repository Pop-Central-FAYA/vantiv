<?php

namespace Vanguard\Services\MediaAsset;
use Vanguard\Models\MediaAsset;
use Auth;

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
        return MediaAsset::with(['client', 'brand'])
                        ->where('company_id', Auth::user()->companies->first()->id)
                        ->latest()->get();
    }
}