<?php

namespace Vanguard\Providers;

use Vanguard\Events\User\Registered;
use Vanguard\Listeners\UserEventsSubscriber;
use Vanguard\Listeners\UserWasRegisteredListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Vanguard\Events\Dsp\Campaign\UpdateStatus;
use Vanguard\Events\Dsp\CampaignMpoTimeBeltUpdated;
use Vanguard\Listeners\Dsp\Campaign\ActiveStatus;
use Vanguard\Listeners\Dsp\Campaign\CompleteStatus;
use Vanguard\Listeners\Dsp\CampaignBudgetUpdated;
use Vanguard\Listeners\Dsp\CampaignMpoUpdated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [UserWasRegisteredListener::class],
        CampaignMpoTimeBeltUpdated::class => [
            //CampaignMpoUpdated::class,
            CampaignBudgetUpdated::class
        ],
        UpdateStatus::class => [
            ActiveStatus::class,
            CompleteStatus::class
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventsSubscriber::class,
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
