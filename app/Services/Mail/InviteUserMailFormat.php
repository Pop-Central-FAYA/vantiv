<?php

namespace Vanguard\Services\Mail;

use \Illuminate\Support\Facades\URL;
use Vanguard\Services\BaseServiceInterface;

class InviteUserMailFormat implements BaseServiceInterface
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

    public function run()
    {
        return [
            'companies' => collect( $this->invited_user->companies()->pluck('name'))->implode(', '),
            'recipient' =>   $this->invited_user->email,
            'subject' => $this->subject,
            'inviter' =>  $this->inviter_name,
            'user_id' =>  $this->invited_user->id,
            'valid_duration' => env('INVITATION_LINK_USAGE_DURATION', 24). " hour(s)",
            'link' =>  URL::temporarySignedRoute('user.complete_registration', now()->addHour(env('INVITATION_LINK_USAGE_DURATION', 24)),
                ['id'=>  $this->invited_user->id])
        ];
    }
}
