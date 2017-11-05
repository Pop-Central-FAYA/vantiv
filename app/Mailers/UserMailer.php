<?php

namespace Vanguard\Mailers;

use Vanguard\Notifications\EmailConfirmation;
use Vanguard\Notifications\ResetPassword;
use Vanguard\User;

class UserMailer extends AbstractMailer
{
    public function sendConfirmationEmail(User $user, $token)
    {
        $user->notify(new EmailConfirmation($token));
    }

    public function sendPasswordReminder(User $user, $token)
    {
        $user->notify(new ResetPassword($token));
    }
}
