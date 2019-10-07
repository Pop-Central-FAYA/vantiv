<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
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
     * Determine if the given user can be viewed by the current user
     * @return bool
     */
    protected function isCurrentUser($user, $profile)
    {
        $user_id = $user->id;
        $profile_id = $profile->id;
        return $user_id === $profile_id;
    }

    public function getProfile(User $user, User $profile)
    {
        return $this->isCurrentUser($user, $profile);
    }

    public function updateProfile(User $user, User $profile)
    {
       return $this->isCurrentUser($user, $profile);
    }
}
