<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailForApproval extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user_mail_content_array;

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
        return $this->view('mail.request_approval')->subject("Request for approval")->with('mail_content', $this->user_mail_content_array);
    }
}
