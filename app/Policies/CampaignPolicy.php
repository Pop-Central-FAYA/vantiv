<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Vanguard\Models\Campaign;

class CampaignPolicy
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
     * Determine if the given campaign mpo can be viewed by the current user
     * @return bool
     */
    protected function belongsToUserCompany($user, $campaign)
    {
        $user_companies = $user->companyIdList();
        $campaign_company = $campaign->belongs_to;
        return in_array($campaign_company, $user_companies);
    }

    public function update(User $user, Campaign $campaign)
    {
        return $this->belongsToUserCompany($user, $campaign);
    }

    public function delete(User $user, Campaign $campaign)
    {
        return $this->belongsToUserCompany($user, $campaign);
    }

    public function store(User $user, Campaign $campaign)
    {
        return $this->belongsToUserCompany($user, $campaign);
    }
}
