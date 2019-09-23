<?php

namespace Vanguard\Services\Mail;
use Illuminate\Support\Facades\Config;
class UserInvitationMail
{
    protected $mail_content_array;
    protected $invited_user;

    public function __construct($mail_content_array)
    {
        $this->mail_content_array = $mail_content_array;
    }

    private function processEmailContent()
    {
        $to = [];
        foreach ($this->mail_content_array as $mail_content){
            $to[] =  new \SendGrid\Mail\To(
                        $mail_content['recipient'],
                        $mail_content['recipient'],
                        [
                            '-link-' => $mail_content['link'],
                            '-email-' => $mail_content['recipient'],
                            '-inviter-' => $mail_content['inviter'],
                            '-company-' => $mail_content['companies'],
                            '-valid_duration-' => Config::get('app.valid_duration'). " hour(s)",
                            '-date_time-' => date('F j, Y | G:i A T')
                        ],
                        $mail_content['subject']
                    );
        }
        return $to;
    }

    public function sendInvitationMail()
    {
       
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
            $html_content = file_get_contents("../resources/views/mail/invite_user.html");
             break;
            default:
            $html_content = file_get_contents("../resources/views/mail/dsp_invite_user.html");
            break;
        }
        $from = new \SendGrid\Mail\From(getenv('EMAIL_FROM'), getenv('EMAIL_FROM_NAME'));
        $tos = $this->processEmailContent();
        $subject = new \SendGrid\Mail\Subject("Hi -email-!"); // default subject
        $globalSubstitutions = [
            '-time-' => date('Y-m-d H:i:s')
        ];
     
        $plainTextContent = new \SendGrid\Mail\PlainTextContent(
            "You have been invited by -inviter- to be part of -company- ,copy the  -link- in a browser to accept invitation"
        );
       
        $htmlContent = new \SendGrid\Mail\HtmlContent($html_content);
        $email = new \SendGrid\Mail\Mail(
            $from,
            $tos,
            $subject, // or array of subjects, these take precendence
            $plainTextContent,
            $htmlContent,
            $globalSubstitutions
        );
     
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
            $data = 'Emails were successfully sent to the following users :'.json_encode(collect($this->mail_content_array)->pluck('user_id'));
            \Log::info($data);
            return 'success';
        } catch (\Exception $e) {
            \Log::error($e);
            return 'error';
        }
    }
}
