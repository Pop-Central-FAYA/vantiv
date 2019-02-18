<?php

namespace Vanguard\Http\Controllers\Traits;

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
}
