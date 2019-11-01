<?php

namespace Vanguard\Providers;

use Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * admin roles
     *
     * @var array
     */
    const ADMIN_ROLES = ['dsp.admin', 'dsp.head_media_buyer', 'dsp.head_media_planner'];

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
        \Vanguard\Models\MediaPlan::class => \Vanguard\Policies\MediaPlanPolicy::class,
        \Vanguard\User::class => \Vanguard\Policies\UserPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-model', function($user, $model) {
            return $user->isFollowing($model) || $this->isUserHead(self::ADMIN_ROLES, $user->getRoleNames());
        });
    }

    /**
     * Returns true if the user roles is any of the head / admin
     */
    private function isUserHead($roles, $user_roles)
    {
        $result = array_intersect($roles, $user_roles->toArray());
        return count($result) > 0;
    }
}
