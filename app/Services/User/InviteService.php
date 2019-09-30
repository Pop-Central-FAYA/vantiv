<?php

namespace Vanguard\Services\User;
use Vanguard\Services\BaseServiceInterface;
use DB;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use Illuminate\Support\Arr;
use Vanguard\Services\Mail\InviteUserMailFormat;
use Vanguard\Mail\InviteUser;

class InviteService implements BaseServiceInterface
{
    protected $companies_id;
    protected $data;
    protected $guard;
    protected $subject;
    
    public function __construct($data, $companies_id, $guard, $subject)
    {
        $this->data = $data;
        $this->companies_id = $companies_id;
        $this->guard = $guard;
        $this->subject = $subject;
    }

    public function run()
    {
        return $this->processInvite();
    }

    public function processInvite()
    {
        $inviter_name = \Auth::user()->full_name;
         $new_user = '';
        \DB::transaction(function () use ($inviter_name) {
            $roles = Arr::pluck($this->data['roles'], 'role');
            foreach (explode(',', $this->data['email']) as $email) {
                $invited_user = $this->createUnconfirmedUser($roles, $this->companies_id, $email, $this->guard);
                $send_mail = \Mail::to($email)->send(new InviteUser($invited_user, $inviter_name, $this->subject));
             }
            $new_user = $invited_user;
        });
        return $new_user;
    }

    public function createUnconfirmedUser($roles, $companies, $email, $guard)
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




