<?php

namespace Vanguard\Services\User;

use Vanguard\User;

class UserDetails
{
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getUserDetails()
    {
        return User::where('id', $this->user_id)->first();
    }
}
