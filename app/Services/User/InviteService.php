<?php

namespace Vanguard\Services\User;

use Vanguard\Services\BaseServiceInterface;
use DB;

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
             
    }

    public function processInvite()
    {

        $companies = $this->getCompany($request->companies);
        $inviter_name = \Auth::user()->full_name;
        
        \DB::transaction(function () use ($request, $companies, $inviter_name) {
            $user_mail_content_array = [];

            foreach ($request->email as $email) {
                $invite_user_service = new InviteUser($request->roles, $companies, $email, "web");
                $invited_user = $invite_user_service->createUnconfirmedUser();
                $subject="Invitation to join Vantage";
                $email_format = new MailFormat($invited_user, $inviter_name, $subject);
                $user_mail_content_array[] = $email_format->emailFormat();
            }
            $email_invitation_service = new UserInvitationMail($user_mail_content_array);
            $email_invitation_service->sendInvitationMail();

            
        });
        
        return ['status'=>"success", 'message'=> "User(s) invited successfully, and emails sent"];
    }
}