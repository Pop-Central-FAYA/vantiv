<?php

namespace Vanguard\Services\Company;
use Vanguard\Models\Company;


/**
 * This service is to update a company.
 */
class UpdateCompany
{
    protected $company_id;
    protected $address;
    protected $image_url;

    public function __construct($company_id, $address, $image_url)
    {
        $this->company_id = $company_id;
        $this->address = $address;
        $this->image_url = $image_url;
    }

    public function run()
    {
        return $this->update();
    }
    
    protected function update()
    {
        $company = Company::where('id', $this->company_id)->first();
        $company->address = $this->address;
        $company->save();
        return $company;
    }

    public function updateAvatar()
    {
        $company = Company::where('id', $this->company_id)->first();
        $company->logo = $this->image_url;
        $company->save();
        return $company;
    }
}

