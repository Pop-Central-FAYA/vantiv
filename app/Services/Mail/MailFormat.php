<?php

namespace Vanguard\Services\Mail;

use \Illuminate\Support\Facades\URL;

class MailFormat
{

    public $invited_user;
    public $inviter_name;
    public $subject;
   

    public function __construct($invited_user, $inviter_name, $subject)
    {
        $this->invited_user = $invited_user;
        $this->inviter_name = $inviter_name;
        $this->subject = $subject;
    }

    public function emailFormat()
    {
        return [
            'companies' => collect( $this->invited_user->companies()->pluck('name'))->implode(', '),
            'recipient' =>   $this->invited_user->email,
            'subject' => $this->subject,
            'inviter' =>  $this->inviter_name,
            'user_id' =>  $this->invited_user->id,
            'link' =>  URL::temporarySignedRoute('user.complete_registration', now()->addHour(1),
                ['id'=>  $this->invited_user->id])
        ];
    }
}
