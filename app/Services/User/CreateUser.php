<?php

namespace Vanguard\Services\User;

use Vanguard\Libraries\Enum\UserStatus;
use Vanguard\User;

class CreateUser
{
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $username;
    protected $phone_number;
    protected $password;
    protected $registration_source;

    public function __construct($firstname, $lastname, $email, $username, $phone_number, $password, $registration_source)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->username = $username;
        $this->phone_number = $phone_number;
        $this->password = $password;
        $this->registration_source = $registration_source;
    }

    public function createUser()
    {
        $status = 'Active';
        if($this->registration_source == 'walkins'){
            $status = UserStatus::INACTIVE;
        }
        $user = new User();
        $user->id = uniqid(); //will come back and change when I finally figure out where the user model extends the base model class
        $user->email = $this->email;
        $user->username = $this->username;
        $user->password = $this->password;
        $user->firstname = $this->firstname;
        $user->lastname = $this->lastname;
        $user->phone_number = $this->phone_number;
        $user->status =  $status;
        $user->save();

        return $user;
    }
}
