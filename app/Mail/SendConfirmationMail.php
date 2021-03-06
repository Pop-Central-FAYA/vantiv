<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $token;
    public $email;
    public $full_name;

    public function __construct($token, $email, $full_name)
    {
        $this->token = $token;
        $this->email = $email;
        $this->full_name= $full_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.confirmation_mail', ['token' => $this->token, 'email' => $this->email, 'full_name' => $this->full_name]);
    }
}
