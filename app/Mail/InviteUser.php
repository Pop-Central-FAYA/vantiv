<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use \Illuminate\Support\Facades\URL;

class InviteUser extends Mailable
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
    public function __construct($invited_user, $inviter_name, $subject)
    {
        $this->invited_user = $invited_user;
        $this->inviter_name = $inviter_name;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invite_user')
                     ->subject($this->format()['subject'])
                    ->with('user_mail_content', $this->format());
    }

    public function format()
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
