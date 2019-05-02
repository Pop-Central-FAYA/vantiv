<?php

namespace Vanguard\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        Middleware\VerifyInstallation::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Fideloper\Proxy\TrustProxies::class
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Vanguard\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Vanguard\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

        ],
        'api' => [

            'bindings'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Vanguard\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \Vanguard\Http\Middleware\RedirectIfAuthenticated::class,
        'registration' => \Vanguard\Http\Middleware\Registration::class,
        'social.login' => \Vanguard\Http\Middleware\SocialLogin::class,
        'ability' => \Zizaco\Entrust\Middleware\EntrustAbility::class,
        'session.database' => \Vanguard\Http\Middleware\DatabaseSession::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,

    ];
}
