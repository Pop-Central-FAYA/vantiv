<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Vanguard\Models\CampaignMpo;

class CampaignMpoPolicy
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
    protected function belongsToUserCompany($user, $campaign_mpo)
    {
        $user_companies = $user->companyIdList();
        $campaign_mpo_company = $campaign_mpo->campaign->belongs_to;
        return in_array($campaign_mpo_company, $user_companies);
    }

    public function update(User $user, CampaignMpo $campaign_mpo)
    {
        return $this->belongsToUserCompany($user, $campaign_mpo);
    }

    public function delete(User $user, CampaignMpo $campaign_mpo)
    {
        return $this->belongsToUserCompany($user, $campaign_mpo);
    }

    public function store(User $user, CampaignMpo $campaign_mpo)
    {
        return $this->belongsToUserCompany($user, $campaign_mpo);
    }
}
