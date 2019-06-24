<?php

namespace Vanguard\Events\User;

class LoggedOut 
{
    protected $user;

    public function __construct($user)
    {
        
        $this->user = $user;
       
    }
 
    public function getUser()
    {
        return $this->user;
    }

}
