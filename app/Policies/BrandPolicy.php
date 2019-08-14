<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\Brand;
use Vanguard\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
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
     * Determine if the given advendor can be viewed by the current user
     * @return bool
     */
    protected function belongsToUserCompany($user, $brand)
    {
        $user_companies = $user->companyIdList();
        $brand_client_id =$brand->client_id;

        $brand_company = Client::find($brand_client_id)->company_id;
        return in_array($brand_company, $user_companies);
    }

    public function get(User $user, Brand $brand)
    {
        return $this->belongsToUserCompany($user, $brand);
    }

    public function update(User $user, Brand $brand)
    {
       return $this->belongsToUserCompany($user, $brand);    
    }
}
