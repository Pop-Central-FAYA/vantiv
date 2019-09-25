<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Vanguard\Models\CampaignMpo;

class MpoPolicy
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
    protected function belongsToUserCompany($user, $mpo)
    {
        $user_companies = $user->companyIdList();
        $campaign_company = $mpo->campaign->belongs_to;
        return in_array($campaign_company, $user_companies);
    }

    public function details(User $user, CampaignMpo $campaignMpo)
    {
        return $this->belongsToUserCompany($user, $campaignMpo);
    }
}
