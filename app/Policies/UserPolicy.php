<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
    protected function belongsToCompany($user, $active_user)
    {
        $user_id = $user->companies->first()->id;
        $active_user_id = $active_user->companies->first()->id;

        return $user_id === $active_user_id;
    }

    public function get(User $user, User $active_user)
    {
        return $this->belongsToCompany($user, $active_user);
    }

    public function update(User $user, User $active_user)
    {
       return $this->belongsToCompany($user, $active_user);
    }
    public function delete(User $user, User $active_user)
    {
       return $this->belongsToCompany($user, $active_user);
    }
}
