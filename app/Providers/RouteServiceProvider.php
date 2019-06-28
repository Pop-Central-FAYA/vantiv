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
        // generic health checks for load balancers
        Route::get('/health', function () {
            return 'All Good!';
        });

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
        $product = env('PRODUCT');
        switch ($product) {
            case 'ssp':
                $this->mapApiRoutes();
                $this->mapWebRoutes();
                break;
            default:
                # The order here is very important
                # There are some routes in web routes that should only be in dsp routes
                # So, for now, those routes need to be loaded, but have dsp override
                $this->mapWebRoutes();
                $this->mapDspRoutes();
                break;
        }
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


    protected function mapDspRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => 'web',
        ], function ($router) {
            require base_path('routes/dsp_web.php');
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

