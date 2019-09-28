<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteUser extends Mailable
{
    use Queueable, SerializesModels;
    protected $user_mail_content_array;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_mail_content_array)
    {
        $this->user_mail_content_array = $user_mail_content_array;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invite_user')
                     ->subject($this->user_mail_content_array['subject'])
                    ->with('user_mail_content', $this->user_mail_content_array);
    }
}
