<?php

namespace Vanguard\Providers;

use Route;
use Vanguard\Permission;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Vanguard\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->bindUser();
        $this->bindRole();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapDSPRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => 'web',
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }


    protected function mapDSPRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => 'web',
        ], function ($router) {
            require base_path('routes/dsp_routes.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function () {
            require base_path('routes/api.php');
        });
    }

    private function bindUser()
    {
        $this->bindUsingRepository('user', UserRepository::class);
    }

    private function bindRole()
    {
        $this->bindUsingRepository('role', RoleRepository::class);
    }

    private function bindUsingRepository($entity, $repository, $method = 'find')
    {
        Route::bind($entity, function ($id) use ($repository, $method) {
            if ($object = app($repository)->$method($id)) {
                return $object;
            }

            throw new NotFoundHttpException;
        });
    }
}

