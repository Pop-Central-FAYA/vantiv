<?php

namespace Vanguard\Services\User;

use Vanguard\Services\BaseServiceInterface;
use Vanguard\Services\Mail\UserInvitationMail;
use Vanguard\Services\Mail\MailFormat;
use DB;
use Vanguard\User;
use Vanguard\Services\Mail\InviteUserMailFormat;
use Vanguard\Mail\InviteUser;

class ReinviteService implements BaseServiceInterface
{
    protected $subject;
    protected $user;
    
    public function __construct($user, $subject)
    {
        $this->user = $user;
        $this->subject = $subject;
    }

    public function run()
    {
        return $this->processInvite();
    }

    public function processInvite()
    {
        $email_format = new InviteUserMailFormat($this->user, \Auth::user()->full_name, $this->subject);
        $user_mail_content_array = $email_format->run();
        $send_mail = \Mail::to($user_mail_content_array['recipient'])->send(new InviteUser($user_mail_content_array));
        return $this->user;
    }   
}
