<?php

namespace Vanguard\Listeners;

use Vanguard\Events\User\Registered;
use Vanguard\Mailers\NotificationMailer;
use Vanguard\Mailers\UserMailer;
use Vanguard\Notifications\UserRegistered;
use Vanguard\Repositories\User\UserRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserWasRegisteredListener
{
    /**
     * @var UserRepository
     */
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if (! settings('notifications_signup_email')) {
            return;
        }
        
        foreach ($this->users->getUsersWithRole('Admin') as $user) {
            $user->notify(new UserRegistered($event->getRegisteredUser()));
        }
    }
}
