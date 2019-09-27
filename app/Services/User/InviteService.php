<?php

namespace Vanguard\Services\User;

use Vanguard\Services\BaseServiceInterface;
use Vanguard\Services\Mail\UserInvitationMail;
use Vanguard\Services\Mail\MailFormat;
use DB;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class InviteService implements BaseServiceInterface
{
    protected $companies_id;
    protected $user_detail;
    protected $guard;
    
    public function __construct($user_detail, $companies_id, $guard)
    {
        $this->user_detail = $user_detail;
        $this->companies_id = $companies_id;
        $this->guard = $guard;
    }

    public function run()
    {
        return $this->processInvite($this->user_detail,$this->companies_id, $this->guard);
    }

    public function processInvite($request, $companies, $inviter_name)
    {

        $inviter_name = \Auth::user()->full_name;
        $new_user = '';
        \DB::transaction(function () use ($request, $companies, $inviter_name) {
            $user_mail_content_array = [];
            $roles = [];
            foreach($request->roles as $role) {
                array_push($roles, $role['role']);
            }
            foreach (explode(',', $request->email) as $email) {
                $invited_user = $this->createUnconfirmedUser($roles, $companies, $email, "web");
                $subject="Invitation to join Vantage";
                $email_format = new MailFormat($invited_user, $inviter_name, $subject);
                $user_mail_content_array[] = $email_format->emailFormat();
            }
            $new_user = $invited_user;
            $email_invitation_service = new UserInvitationMail($user_mail_content_array);
            $email_invitation_service->sendInvitationMail();  
        });
        
        return $new_user;
    }

     private function getCompany($request)
    {
        if(isset($request->companies)) {
            $companies = $request->companies;
        }else{
            $companies = \Auth::user()->companies->first()->id;
        }
        return $companies;
    }

    public function createUnconfirmedUser($roles, $companies, $email, $guard)
    {
        \DB::transaction(function () use (&$user, $roles, $companies, $email, $guard) {
            $user = $this->createUser($email);
            $user->companies()->attach($companies);
            $user->assignRole($roles, $guard);
        });
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




