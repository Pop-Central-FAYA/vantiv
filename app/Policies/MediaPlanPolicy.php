<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\MediaPlan;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPlanPolicy
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
     * Determine if the given media plan can be viewed by the current user
     * @return bool
     */
    protected function belongsToUserCompany($user, $media_plan)
    {
        $user_companies = $user->companyIdList();
        $media_plan_company = $media_plan->company_id;
        return in_array($media_plan_company, $user_companies);
    }

    public function get(User $user, MediaPlan $media_plan)
    {
        // if ($this->belongsToUserCompany($user, $media_plan) == false) {
        //     var_dump($media_plan->planner_id);
        //     var_dump($user->id);
        //     var_dump($media_plan->company_id);
        //     dd($user->companyIdList());
        // }
        return $this->belongsToUserCompany($user, $media_plan);
    }

    public function update(User $user, MediaPlan $media_plan)
    {
        return $this->belongsToUserCompany($user, $media_plan);
    }

    public function delete(User $user, MediaPlan $media_plan)
    {
       return $this->belongsToUserCompany($user, $media_plan);
    }

    public function assignFollower(User $user, MediaPlan $media_plan)
    {
        return $this->belongsToUserCompany($user, $media_plan);
    }
}
