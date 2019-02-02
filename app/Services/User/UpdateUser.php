<?php

namespace Vanguard\Services\User;

use Vanguard\User;

class UpdateUser
{
    protected $user_id;
    protected $first_name;
    protected $last_name;
    protected $phone_number;
    protected $address;
    protected $avatar;
    protected $password;
    protected $update_source;

    public function __construct($user_id, $first_name, $last_name, $phone_number, $address, $avatar, $password, $update_source)
    {
        $this->user_id = $user_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone_number = $phone_number;
        $this->address = $address;
        $this->avatar = $avatar;
        $this->password = $password;
        $this->update_source = $update_source;
    }

    public function updateUser()
    {
        $user = User::where('id', $this->user_id)->first();
        $user->firstname = $this->first_name;
        $user->lastname = $this->last_name;
        $user->phone_number = $this->phone_number;
        $user->address = $this->update_source == 'profile_update' ? $this->address : $user->address;
        $user->save();
        return $user;
    }

    public function updatePassword()
    {
        $user = User::where('id', $this->user_id)->first();
        $user->password = $this->password;
        $user->save();
        return $user;
    }

    public function updateAvatar()
    {
        $user = User::where('id', $this->user_id)->first();
        $user->avatar = $this->avatar;
        $user->save();
        return $user;
    }
}
