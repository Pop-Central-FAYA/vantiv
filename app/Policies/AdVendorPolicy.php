<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\AdVendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdVendorPolicy
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
    protected function belongsToUserCompany($user, $ad_vendor)
    {
        $user_companies = $user->companyIdList();
        $ad_vendor_company = $ad_vendor->company_id;
        return in_array($ad_vendor_company, $user_companies);
    }

    public function get(User $user, AdVendor $ad_vendor)
    {
        return $this->belongsToUserCompany($user, $ad_vendor);
    }

    public function update(User $user, AdVendor $ad_vendor)
    {
        return $this->belongsToUserCompany($user, $ad_vendor);
    }
}
