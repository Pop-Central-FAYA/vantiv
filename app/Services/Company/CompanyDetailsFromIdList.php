<?php

namespace Vanguard\Services\Company;

use Vanguard\Models\Company;

class CompanyDetailsFromIdList
{
    protected $company_ids;

    public function __construct($company_ids)
    {
        $this->company_ids = $company_ids;
    }

    public function getCompanyDetails()
    {
        return Company::whereIn('id', $this->company_ids)
                    ->get();
    }
}
