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
