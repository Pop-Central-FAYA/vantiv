<?php

namespace Vanguard\Services\User;
use Vanguard\Services\BaseServiceInterface;
use DB;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use Illuminate\Support\Arr;
use Vanguard\Services\Mail\InviteUserMailFormat;
use Vanguard\Mail\SendUserInvitationMail;

class InviteService implements BaseServiceInterface
{
    protected $user;
    protected $data;
    protected $guard;
    protected $subject;
    
    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;

    }

    public function run()
    {
        return $this->processInvite();
    }

    private function processInvite()
    {
          return  \DB::transaction(function (){
                $roles = Arr::pluck($this->data['roles'], 'role');
                $invited_user = $this->createUnconfirmedUser($roles, $this->user->companies->first()->id, $this->data['email'], 'web');
                $send_mail = \Mail::to($this->data['email'])->send(new SendUserInvitationMail($invited_user, $this->user->full_name));
                return $invited_user;
             });

    }

    private function createUnconfirmedUser($roles, $companies, $email, $guard)
    {
            $user = $this->createUser($email);
            $user->companies()->attach($companies);
            $user->assignRole($roles, $guard);
            return $user;
    }

    private function createUser($email)
    {
        $user = new User();
        $user->id = uniqid();
        $user->email = $email;
        $user->status = UserStatus::UNCONFIRMED;
        $user->save();
        return $user;
    }
}




