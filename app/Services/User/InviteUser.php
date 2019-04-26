<?php

namespace Vanguard\Services\User;

use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class InviteUser
{
    protected $roles;
    protected $companies_id;
    protected $email;
    public function __construct($roles, $companies_id, $email)
    {
        $this->roles = $roles;
        $this->companies_id = $companies_id;
        $this->email = $email;
    }

    public function createUnconfirmedUser()
    {
        \DB::transaction(function () use (&$user) {
            $user = $this->createUser();
            $user->companies()->attach($this->companies_id);
            $user->assignRole($this->roles);
        });
        return $user;
    }

    private function createUser()
    {
        $user = new User();
        $user->id = uniqid();
        $user->email = $this->email;
        $user->status = UserStatus::UNCONFIRMED;
        $user->save();
        return $user;
    }
}
