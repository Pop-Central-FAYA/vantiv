<?php

namespace Vanguard\Services\Company;

class CompanyDetailsFromIdList
{
    protected $company_ids;

    public function __construct($company_ids)
    {
        $this->company_ids = $company_ids;
    }

    public function getCompanyDetails()
    {
        return \DB::table('companies')
                    ->whereIn('id', $this->company_ids)
                    ->get();
    }
}
