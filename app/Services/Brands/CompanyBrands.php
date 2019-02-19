<?php

namespace Vanguard\Services\Brands;

class CompanyBrands
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getBrandCreatedByCompany()
    {
        return \DB::table('brand_client')
                    ->join('brands', 'brands.id', '=', 'brand_client.brand_id')
                    ->select('brands.*', 'brand_client.media_buyer_id AS agency_broadcaster',
                        'brand_client.client_id AS client_walkins_id')
                    ->when(!is_array($this->company_id), function ($query) {
                        return $query->where('brand_client.media_buyer_id', $this->company_id);
                    })
                    ->get();
    }
}
