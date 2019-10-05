<?php

namespace Vanguard\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Vanguard\Models\AdVendor::class => \Vanguard\Policies\AdVendorPolicy::class,
        \Vanguard\Models\Company::class => \Vanguard\Policies\CompanyPolicy::class,
        \Vanguard\Models\Client::class => \Vanguard\Policies\ClientPolicy::class,
        \Vanguard\Models\Brand::class => \Vanguard\Policies\BrandPolicy::class,
        \Vanguard\Models\Campaign::class => \Vanguard\Policies\CampaignPolicy::class,
        \Vanguard\Models\CampaignMpo::class => \Vanguard\Policies\MpoPolicy::class,
        \Vanguard\User::class => \Vanguard\Policies\UserPolicy::class,
        \Vanguard\User::class => \Vanguard\Policies\ProfilePolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
