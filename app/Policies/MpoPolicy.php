<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Vanguard\Libraries\Enum\Dsp\CampaignStatus;
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

    /**
     * This is a shitty way of implementing this, will revisit it with the proper implementation
     */
    public function campaignStatus(User $user, CampaignMpo $campaignMpo)
    {
        $status_list = [CampaignStatus::ACTIVE, CampaignStatus::PENDING];
        return \in_array($campaignMpo->campaign->status, $status_list);
    }

    public function listUsers(User $user, CampaignMpo $campaignMpo)
    {
        return $this->belongsToUserCompany($user, $campaignMpo);
    }

    public function approve(User $user, CampaignMpo $campaignMpo)
    {
        return $this->belongsToUserCompany($user, $campaignMpo);
    }

    public function share(User $user, CampaignMpo $campaignMpo)
    {
        return $this->belongsToUserCompany($user, $campaignMpo);
    }
}
