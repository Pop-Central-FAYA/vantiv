<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use \Illuminate\Support\Facades\URL;

class SendUserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $invited_user;
    public $inviter_name;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invited_user, $inviter_name)
    {
        $this->invited_user = $invited_user;
        $this->inviter_name = $inviter_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invite_user')
                     ->subject("Invitation to join Vantage")
                     ->with('user_mail_content', $this->mailData());
    }

    private function mailData()
    {
        $expire_time =  env('INVITATION_LINK_USAGE_DURATION', 24);
        return [
            'companies' => collect( $this->invited_user->companies()->pluck('name'))->implode(', '),
            'recipient' =>   $this->invited_user->email,
            'subject' => $this->subject,
            'inviter' =>  $this->inviter_name,
            'valid_duration' => $expire_time. " hours",
            'link' =>  URL::temporarySignedRoute('user.complete_registration', now()->addHour($expire_time),
                ['id'=>  $this->invited_user->id])
        ];
    }
}
