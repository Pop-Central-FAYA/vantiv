<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteUser extends Mailable
{
    use Queueable, SerializesModels;
    protected $inviter_name;
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inviter_name, $user)
    {
        $this->inviter_name = $inviter_name;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invite_user')
                    ->with('inviter_name', $this->inviter_name)
                    ->with('user', $this->user);
    }
}
