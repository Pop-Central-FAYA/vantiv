<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MpoActionNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $mail_content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_content)
    {
        $this->mail_content = $mail_content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.mpo.action_notification')->with('mail_content', $this->mail_content);
    }
}
