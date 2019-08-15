<?php

namespace Vanguard\Policies;

use Vanguard\User;
use Vanguard\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
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
    protected function belongsToUserCompany($user, $client)
    {
        $user_companies = $user->companyIdList();
        $client_company = $client->company_id;

        
        return in_array($client_company, $user_companies);
    }

    public function get(User $user, Client $client)
    {
        return $this->belongsToUserCompany($user, $client);
    }

    public function update(User $user, Client $client)
    {
      return $this->belongsToUserCompany($user, $client);
    }

    public function destroy(User $user, Client $client)
    {
      return $this->belongsToUserCompany($user, $client);
    }
}
