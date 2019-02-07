<?php

namespace Vanguard\Services\Company;

use Vanguard\Models\Company;

class CompanyDetails
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getCompanyDetails()
    {
        return Company::where('id', $this->company_id)->first();
    }
}
