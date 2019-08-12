<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\Brand;
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
    protected function belongsToUserCompany($user, $brand)
    {
        $user_company = $user->companies->first()->id;
        $brand_company = User::find($brand->created_by)->companies->first()->id;
        return $user_companies === $brand_company;
    }

    public function get(User $user, Brand $ad_vendor)
    {
        return $this->belongsToUserCompany($user, $brand);
    }

    public function update(User $user, Brand $brand)
    {
        return $this->belongsToUserCompany($user, $brand);
    }
}
