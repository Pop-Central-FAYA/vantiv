<?php

namespace Vanguard\Http\Controllers\Traits;

use Vanguard\Services\Company\CompanyDetailsFromIdList;

trait CompanyIdTrait
{
    public function companyId()
    {
        $user = \Auth::user();
        if($user->companies()->count() > 1){
            $company_id = $user->company_id;
        }else{
            $company_id = $user->companies->first()->id;
        }
        return $company_id;
    }

    public function getCompaniesDetails($company_ids)
    {
        if(is_array($company_ids)){
            $companies_service = new CompanyDetailsFromIdList($company_ids);
            $companies = $companies_service->getCompanyDetails();
        }else{
            $companies = '';
        }
        return $companies;
    }

    public function getCompanyIdsList()
    {
        $companies = \Auth::user()->companies()->get();
        return $companies->pluck("id")->toArray();
    }
}
