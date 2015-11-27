<?php
namespace Madewithlove\Glue\Dummies\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Mockery;

class MockRouterProvider extends AbstractServiceProvider
{
    protected $provides = ['router'];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->share('router', function () {
            $router = Mockery::mock('Router');
            $router->shouldReceive('get')->once()->andReturnUsing(function ($route) {
                return $route;
            });

            return $router;
        });
    }
}
