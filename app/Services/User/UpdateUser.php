<?php

namespace Vanguard\Services\User;

use Vanguard\User;

class UpdateUser
{
    protected $user_id;
    protected $first_name;
    protected $last_name;
    protected $phone_number;

    public function __construct($user_id, $first_name, $last_name, $phone_number)
    {
        $this->user_id = $user_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone_number = $phone_number;
    }

    public function updateUser()
    {
        $user = User::where('id', $this->user_id)->first();
        $user->firstname = $this->first_name;
        $user->lastname = $this->last_name;
        $user->phone_number = $this->phone_number;
        $user->save();
        return $user;
    }
}
