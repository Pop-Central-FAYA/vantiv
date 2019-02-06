<?php

namespace Vanguard\Services\User;


class AuthenticatableUser extends CreateUser
{
    protected $companies_id;
    public function __construct($firstname, $lastname, $email, $username, $phone_number, $password, $registration_source, $companies_id)
    {
        parent::__construct($firstname, $lastname, $email, $username, $phone_number, $password, $registration_source);
        $this->companies_id = $companies_id;
    }

    public function createAuthenticatableUser()
    {
        \DB::transaction(function () use (&$user) {
            $user = $this->createUser();
            $user->companies()->attach($this->companies_id);
        });
        return $user;
    }
}
