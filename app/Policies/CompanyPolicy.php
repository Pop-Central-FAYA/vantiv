<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

     /**
     * Determine if the given company can be viewed by the current user
     * @return bool
     */
    protected function belongsToCompany($user, $company)
    {
        $user_company_id = $user->companies->first()->id;
        $company_id = $company->id;
        return $user_company_id === $company_id;
    }

    public function update(User $user, Company $company)
    {
       return $this->belongsToCompany($user, $company);
    }
}
